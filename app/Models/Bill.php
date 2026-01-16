<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bill extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'institution_id',
        'amount',
        'remaining_amount',
        'status',
        'description',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function fundTransfers(): HasMany
    {
        return $this->hasMany(FundTransfer::class);
    }

    /**
     * Get the total amount distributed to this bill via fund transfers.
     */
    public function getDistributedAmount(): float
    {
        return (float) $this->fundTransfers()->sum('amount');
    }

    /**
     * Get the remaining amount that needs to be distributed.
     */
    public function getPendingDistributionAmount(): float
    {
        // Get total paid by student for this institution (via parent student's transactions)
        $studentPaidAmount = $this->getStudentPaidAmount();
        $distributedAmount = $this->getDistributedAmount();

        return max(0, $studentPaidAmount - $distributedAmount);
    }

    /**
     * Get how much the student has paid that applies to this bill.
     * This is calculated using the priority distribution algorithm.
     */
    public function getStudentPaidAmount(): float
    {
        // This will be calculated by PaymentDistributionService
        return (float) ($this->amount - $this->remaining_amount);
    }

    /**
     * Apply a payment directly to this bill.
     * Used in the hybrid cash collection system.
     */
    public function applyPayment(float $amount): void
    {
        $newRemaining = max(0, $this->remaining_amount - $amount);
        
        $status = 'unpaid';
        if ($newRemaining <= 0) {
            $status = 'paid';
        } elseif ($newRemaining < $this->amount) {
            $status = 'partial';
        }

        $this->update([
            'remaining_amount' => $newRemaining,
            'status' => $status,
        ]);
    }

    /**
     * Update bill status based on fund transfers.
     */
    public function updateStatusFromTransfers(): void
    {
        $distributedAmount = $this->getDistributedAmount();

        $this->remaining_amount = max(0, $this->amount - $distributedAmount);

        if ($this->remaining_amount <= 0) {
            $this->status = 'paid';
        } elseif ($distributedAmount > 0) {
            $this->status = 'partial';
        } else {
            $this->status = 'unpaid';
        }

        $this->save();
    }

    /**
     * Recalculate bill amount based on current fee components.
     */
    public function recalculateAmount(): void
    {
        if (!$this->institution_id) {
            return;
        }

        $activeYear = AcademicYear::where('is_active', true)->first();
        if (!$activeYear) {
            return;
        }

        $feeComponents = FeeComponent::where('institution_id', $this->institution_id)
            ->where('academic_year_id', $activeYear->id)
            ->get();

        $totalAmount = $feeComponents->sum('amount');
        $distributedAmount = $this->getDistributedAmount();

        // Build description
        $descriptions = $feeComponents->map(function ($fee) {
            return $fee->name . ': Rp ' . number_format($fee->amount, 0, ',', '.');
        })->implode("\n");

        $this->update([
            'amount' => $totalAmount,
            'remaining_amount' => max(0, $totalAmount - $distributedAmount),
            'status' => ($totalAmount - $distributedAmount) <= 0 ? 'paid' : ($distributedAmount > 0 ? 'partial' : 'unpaid'),
            'description' => $descriptions,
        ]);
    }
}
