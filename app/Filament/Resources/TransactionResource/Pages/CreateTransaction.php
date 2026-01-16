<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use App\Models\Bill;
use App\Models\FundTransfer;
use App\Models\Student;
use App\Models\Transaction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateTransaction extends CreateRecord
{
    protected static string $resource = TransactionResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        $user = auth()->user();
        $studentId = $data['student_id'] ?? null;

        if (!$studentId) {
            Notification::make()
                ->title('Santri harus dipilih')
                ->danger()
                ->send();
            $this->halt();
        }

        // Determine payment location based on user role
        $isUnitUser = $user->hasRole('Bendahara Unit') && $user->institution_id;
        $paymentLocation = $isUnitUser ? 'UNIT' : 'PANITIA';

        // Get the student's unpaid bills
        $student = Student::with('bills.institution')->find($studentId);
        $unpaidBills = $student->bills->where('remaining_amount', '>', 0)->sortBy('id');

        if ($unpaidBills->isEmpty()) {
            Notification::make()
                ->title('Tidak ada tagihan yang perlu dibayar')
                ->danger()
                ->send();
            $this->halt();
        }

        $amountToPay = (float) $data['amount'];
        $totalPaid = 0;
        $createdTransfers = [];

        // Generate verification token
        $verificationToken = Str::random(32);

        // Create single transaction for the total amount
        $transaction = Transaction::create([
            'student_id' => $studentId,
            'amount' => $amountToPay,
            'payment_method' => $data['payment_method'] ?? 'cash',
            'transaction_date' => $data['transaction_date'],
            'proof_image' => $data['proof_image'] ?? null,
            'notes' => $data['notes'] ?? null,
            'user_id' => $data['user_id'],
            'verification_token' => $verificationToken,
            'payment_location' => $paymentLocation,
            'is_settled' => $isUnitUser, // UNIT payments are immediately settled
        ]);

        // Distribute payment across bills (FIFO)
        foreach ($unpaidBills as $bill) {
            if ($amountToPay <= 0) break;

            $payForThisBill = min($amountToPay, (float) $bill->remaining_amount);

            // Apply payment to bill
            $bill->applyPayment($payForThisBill);
            $totalPaid += $payForThisBill;

            // For UNIT payments, auto-create COMPLETED FundTransfer
            if ($isUnitUser) {
                $transfer = FundTransfer::create([
                    'institution_id' => $user->institution_id,
                    'student_id' => $studentId,
                    'bill_id' => $bill->id,
                    'transaction_id' => $transaction->id,
                    'user_id' => $user->id,
                    'amount' => $payForThisBill,
                    'transfer_date' => now(),
                    'transfer_method' => $data['payment_method'] ?? 'cash',
                    'notes' => 'Auto-transfer: Pembayaran langsung di Unit',
                    'status' => 'COMPLETED',
                    'received_at' => now(),
                    'received_by' => $user->id,
                ]);
                $createdTransfers[] = $transfer;
            }

            $amountToPay -= $payForThisBill;
        }

        // Show appropriate notification based on location
        if ($paymentLocation === 'UNIT') {
            Notification::make()
                ->title('Pembayaran berhasil dicatat')
                ->body('Dana tercatat langsung di kas Unit.')
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('Pembayaran berhasil dicatat')
                ->body('Dana berada di Panitia. Silakan lakukan distribusi ke Unit terkait.')
                ->success()
                ->send();
        }

        return $transaction;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
