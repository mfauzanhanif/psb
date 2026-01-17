<?php

namespace App\Services;

use App\Models\Bill;
use App\Models\FundTransfer;
use App\Models\Institution;
use App\Models\Student;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Collection;

class PaymentDistributionService
{
    /**
     * Calculate FIXED entitlement for each institution based on PANITIA payments only.
     * Direct payments to MADRASAH/SEKOLAH are NOT included - they're already applied directly.
     * Uses priority algorithm: Madrasah → 50:50 Sekolah/Pondok with overflow.
     *
     * @param Student $student
     * @return array ['institution_id' => ['bill_id' => X, 'entitlement' => Y, 'transferred' => Z, 'pending' => W]]
     */
    public function calculateStudentEntitlement(Student $student): array
    {
        // Only use PANITIA payments for Priority Algorithm
        // Direct payments to MADRASAH/SEKOLAH are already applied to their bills
        $panitiaPaid = $student->getPaidAtPanitia();

        if ($panitiaPaid <= 0) {
            return [];
        }

        $bills = $student->bills()
            ->with('institution')
            ->get();

        if ($bills->isEmpty()) {
            return [];
        }

        $entitlements = [];
        $remainingAmount = $panitiaPaid;

        // Group bills by institution type
        $billsByType = $bills->groupBy(fn($bill) => $bill->institution?->type ?? 'unknown');

        // Step 1: Madrasah Priority - Full bill amount (minus what's already paid directly)
        $remainingAmount = $this->allocateEntitlement(
            $billsByType,
            'madrasah',
            $remainingAmount,
            $entitlements
        );

        if ($remainingAmount <= 0) {
            return $this->addTransferInfo($entitlements);
        }

        // Step 2: Calculate 50:50 split for Sekolah and Pondok
        $sekolahTypes = ['smp', 'ma', 'mts'];
        $sekolahBills = $bills->filter(fn($bill) => in_array($bill->institution?->type, $sekolahTypes));

        $sekolahBillTotal = $sekolahBills->sum('amount');
        $halfAmount = $remainingAmount / 2;

        // Step 3: Check Sekolah plafon (max = bill amount)
        $sekolahAllocation = min($halfAmount, $sekolahBillTotal);
        $sekolahOverflow = $halfAmount - $sekolahAllocation;

        // Pondok gets its half plus any overflow from Sekolah
        $pondokAllocation = $halfAmount + $sekolahOverflow;

        // Allocate to Sekolah
        foreach ($sekolahTypes as $type) {
            if ($sekolahAllocation <= 0)
                break;
            $sekolahAllocation = $this->allocateEntitlement(
                $billsByType,
                $type,
                $sekolahAllocation,
                $entitlements
            );
        }

        // Step 4: Pondok as final recipient
        $this->allocateEntitlement(
            $billsByType,
            'pondok',
            $pondokAllocation,
            $entitlements
        );

        return $this->addTransferInfo($entitlements);
    }

    /**
     * Allocate entitlement to bills of a specific institution type.
     * Entitlement is capped at the bill amount.
     */
    protected function allocateEntitlement(
        Collection $billsByType,
        string $type,
        float $amount,
        array &$entitlements
    ): float {
        $bills = $billsByType->get($type, collect());

        foreach ($bills as $bill) {
            if ($amount <= 0)
                break;

            // Entitlement capped at bill amount
            $maxEntitlement = $bill->amount;
            $allocateAmount = min($amount, $maxEntitlement);

            if ($allocateAmount > 0) {
                $entitlements[] = [
                    'institution_id' => $bill->institution_id,
                    'student_id' => $bill->student_id,
                    'bill_id' => $bill->id,
                    'entitlement' => $allocateAmount,
                ];
            }

            $amount -= $allocateAmount;
        }

        return $amount;
    }

    /**
     * Add transfer info (already transferred and pending) to entitlements.
     */
    protected function addTransferInfo(array $entitlements): array
    {
        foreach ($entitlements as &$item) {
            $transferred = FundTransfer::where('bill_id', $item['bill_id'])->sum('amount');
            $item['transferred'] = (float) $transferred;
            $item['pending'] = max(0, $item['entitlement'] - $item['transferred']);
        }
        return $entitlements;
    }

    /**
     * Calculate what still needs to be distributed (pending amounts only).
     * Used for the distribution action.
     */
    public function calculateStudentPendingDistribution(Student $student): array
    {
        $entitlements = $this->calculateStudentEntitlement($student);

        // Only return items with pending > 0
        return array_filter($entitlements, fn($item) => $item['pending'] > 0);
    }

    /**
     * Calculate bulk distribution for all students, grouped by institution.
     * Shows FIXED entitlements, not recalculated amounts.
     *
     * @return Collection ['institution_id' => ['institution' => X, 'total_entitlement' => Y, 'total_transferred' => Z, 'total_pending' => W]]
     */
    public function calculateBulkDistribution(): Collection
    {
        // Get all students with transactions
        $students = Student::whereHas('transactions')
            ->with(['bills.institution', 'transactions'])
            ->get();

        $allEntitlements = [];

        foreach ($students as $student) {
            $studentEntitlements = $this->calculateStudentEntitlement($student);
            $allEntitlements = array_merge($allEntitlements, $studentEntitlements);
        }

        // Group by institution_id
        return collect($allEntitlements)->groupBy('institution_id')->map(function ($items, $institutionId) {
            $institution = Institution::find($institutionId);
            return [
                'institution' => $institution,
                'total_entitlement' => $items->sum('entitlement'),
                'total_transferred' => $items->sum('transferred'),
                'total_pending' => $items->sum('pending'),
                'details' => $items->values()->all(),
            ];
        });
    }

    /**
     * Create settlement requests (PENDING) to a specific institution.
     * Uses priority algorithm to calculate amounts.
     * FundTransfers are created with status = PENDING (3-step workflow).
     *
     * @param Institution $institution
     * @param User $user The user creating the request
     * @param string $transferMethod
     * @param string|null $notes
     * @return Collection Created FundTransfer records
     */
    public function createSettlementRequests(
        Institution $institution,
        User $user,
        string $transferMethod = 'cash',
        ?string $notes = null
    ): Collection {
        $bulkDistribution = $this->calculateBulkDistribution();
        $institutionData = $bulkDistribution->get($institution->id);

        if (!$institutionData || empty($institutionData['details'])) {
            return collect();
        }

        $createdTransfers = collect();

        foreach ($institutionData['details'] as $detail) {
            // Only create transfer for pending amount
            if ($detail['pending'] <= 0) {
                continue;
            }

            $transfer = FundTransfer::create([
                'institution_id' => $detail['institution_id'],
                'student_id' => $detail['student_id'],
                'bill_id' => $detail['bill_id'],
                'user_id' => $user->id,
                'amount' => $detail['pending'],
                'transfer_date' => now(),
                'transfer_method' => $transferMethod,
                'notes' => $notes,
                'status' => 'PENDING', // Key change: create as PENDING
            ]);

            $createdTransfers->push($transfer);
        }

        return $createdTransfers;
    }

    /**
     * Execute distribution to a specific institution (legacy - creates PENDING).
     * Alias for createSettlementRequests for backward compatibility.
     */
    public function executeDistribution(
        Institution $institution,
        User $user,
        string $transferMethod = 'cash',
        ?string $notes = null
    ): Collection {
        return $this->createSettlementRequests($institution, $user, $transferMethod, $notes);
    }

    /**
     * Get summary for dashboard widget - includes ALL institutions with their totals.
     * Shows both Priority Algorithm entitlements AND direct unit payments.
     *
     * @return Collection
     */
    public function getFundSummary(): Collection
    {
        // Get Priority Algorithm distribution (from PANITIA payments only)
        $priorityDistribution = $this->calculateBulkDistribution();
        
        // Get all institutions to ensure we show all of them
        $allInstitutions = Institution::all();
        
        $summary = collect();
        
        foreach ($allInstitutions as $institution) {
            // Get direct payments to this institution (MADRASAH/SEKOLAH payments)
            $directPayments = $this->getDirectPaymentsToInstitution($institution);
            
            // Get Priority Algorithm data if exists
            $priorityData = $priorityDistribution->get($institution->id);
            $priorityEntitlement = $priorityData['total_entitlement'] ?? 0;
            $priorityTransferred = $priorityData['total_transferred'] ?? 0;
            $priorityPending = $priorityData['total_pending'] ?? 0;
            
            // Calculate totals
            $totalEntitlement = $priorityEntitlement + $directPayments;
            $totalReceived = $priorityTransferred + $directPayments; // Direct payments are already "received"
            $pendingAmount = $priorityPending; // Only priority algorithm has pending
            
            // Only include institutions with activity
            if ($totalEntitlement > 0 || $directPayments > 0) {
                $summary->put($institution->id, [
                    'institution' => $institution,
                    'total_entitlement' => $totalEntitlement,
                    'total_transferred' => $totalReceived,
                    'pending_amount' => $pendingAmount,
                    'direct_payments' => $directPayments,
                    'priority_entitlement' => $priorityEntitlement,
                ]);
            }
        }
        
        return $summary->values();
    }

    /**
     * Get total direct payments made to a specific institution.
     * These are payments with payment_location = MADRASAH or SEKOLAH.
     */
    public function getDirectPaymentsToInstitution(Institution $institution): float
    {
        $paymentLocation = match ($institution->type) {
            'madrasah' => 'MADRASAH',
            'smp', 'ma', 'mts' => 'SEKOLAH',
            default => null,
        };
        
        if (!$paymentLocation) {
            return 0;
        }
        
        // Get direct payments where the user belongs to this institution
        return (float) Transaction::where('payment_location', $paymentLocation)
            ->whereHas('user', fn($q) => $q->where('institution_id', $institution->id))
            ->sum('amount');
    }

    /**
     * Get total floating cash at PANITIA (payments not yet settled/distributed).
     */
    public function getFloatingCashAtPanitia(): float
    {
        return (float) Transaction::where('payment_location', 'PANITIA')
            ->where('is_settled', false)
            ->sum('amount');
    }

    /**
     * Get summary of Panitia payments for the first table.
     * Returns: total_panitia, total_distributed, total_pending
     */
    public function getPanitiaSummary(): array
    {
        $totalPanitia = (float) Transaction::where('payment_location', 'PANITIA')->sum('amount');
        
        // Total distributed = sum of all FundTransfers from Panitia (not direct payments)
        $totalDistributed = (float) FundTransfer::whereHas('transaction', function($q) {
            $q->where('payment_location', 'PANITIA');
        })->whereIn('status', ['COMPLETED', 'APPROVED', 'PENDING'])->sum('amount');
        
        $totalPending = $totalPanitia - $totalDistributed;
        
        return [
            'total_panitia' => $totalPanitia,
            'total_distributed' => $totalDistributed,
            'total_pending' => max(0, $totalPending),
        ];
    }

    /**
     * Get total cash held at a specific UNIT.
     * = Direct UNIT payments + Received (COMPLETED) transfers
     */
    public function getCashAtUnit(Institution $institution): float
    {
        // Determine payment location based on institution type
        $paymentLocation = match ($institution->type) {
            'madrasah' => 'MADRASAH',
            'smp', 'ma', 'mts' => 'SEKOLAH',
            default => 'UNIT',
        };

        // Direct unit payments to this institution
        $directPayments = (float) Transaction::whereIn('payment_location', [$paymentLocation, 'UNIT'])
            ->whereHas('student.bills', fn($q) => $q->where('institution_id', $institution->id))
            ->sum('amount');

        // Received transfers (COMPLETED)
        $receivedTransfers = (float) FundTransfer::where('institution_id', $institution->id)
            ->where('status', 'COMPLETED')
            ->sum('amount');

        return $directPayments + $receivedTransfers;
    }

    /**
     * Get pending settlement amount for an institution (PENDING + APPROVED, not yet COMPLETED).
     */
    public function getPendingSettlementAmount(Institution $institution): float
    {
        return (float) FundTransfer::where('institution_id', $institution->id)
            ->whereIn('status', ['PENDING', 'APPROVED'])
            ->sum('amount');
    }
}
