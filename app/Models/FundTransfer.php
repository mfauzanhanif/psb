<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FundTransfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'institution_id',
        'student_id',
        'bill_id',
        'transaction_id',
        'user_id',
        'amount',
        'transfer_date',
        'transfer_method',
        'notes',
        'status',
        'approved_at',
        'approved_by',
        'received_at',
        'received_by',
    ];

    protected $casts = [
        'transfer_date' => 'date',
        'amount' => 'decimal:2',
        'approved_at' => 'datetime',
        'received_at' => 'datetime',
    ];

    /**
     * Boot method to handle model events.
     * Update bill status only when transfer is COMPLETED.
     */
    protected static function booted(): void
    {
        static::updated(function ($fundTransfer) {
            // Only update bill when status changes to COMPLETED
            if ($fundTransfer->isDirty('status') && $fundTransfer->isCompleted() && $fundTransfer->bill_id) {
                $fundTransfer->bill->updateStatusFromTransfers();
            }
        });

        static::deleted(function ($fundTransfer) {
            if ($fundTransfer->bill_id && $fundTransfer->isCompleted()) {
                $fundTransfer->bill->updateStatusFromTransfers();
            }
        });
    }

    // Status helper methods
    public function isPending(): bool
    {
        return $this->status === 'PENDING';
    }

    public function isApproved(): bool
    {
        return $this->status === 'APPROVED';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'COMPLETED';
    }

    public function isRejected(): bool
    {
        return $this->status === 'REJECTED';
    }

    /**
     * Approve this transfer (Step 2 of settlement).
     */
    public function approve(User $user): void
    {
        $this->update([
            'status' => 'APPROVED',
            'approved_at' => now(),
            'approved_by' => $user->id,
        ]);
    }

    /**
     * Confirm receipt of this transfer (Step 3 of settlement).
     */
    public function confirmReceipt(User $user): void
    {
        $this->update([
            'status' => 'COMPLETED',
            'received_at' => now(),
            'received_by' => $user->id,
        ]);
    }

    /**
     * Reject this transfer.
     */
    public function reject(): void
    {
        $this->update(['status' => 'REJECTED']);
    }

    // Relationships
    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function bill(): BelongsTo
    {
        return $this->belongsTo(Bill::class);
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }
}
