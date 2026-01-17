<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'user_id',
        'amount',
        'payment_method',
        'transaction_date',
        'proof_image',
        'notes',
        'verification_token',
        'payment_location',
        'is_settled',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'amount' => 'decimal:2',
        'is_settled' => 'boolean',
    ];

    // No booted events - bill status is updated via FundTransfer

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function fundTransfers(): HasMany
    {
        return $this->hasMany(FundTransfer::class);
    }

    /**
     * Check if payment is at PANITIA (central collection).
     */
    public function isAtPanitia(): bool
    {
        return $this->payment_location === 'PANITIA';
    }

    /**
     * Check if payment is at UNIT (direct collection to Madrasah or Sekolah).
     */
    public function isAtUnit(): bool
    {
        return in_array($this->payment_location, ['UNIT', 'MADRASAH', 'SEKOLAH']);
    }

    /**
     * Check if payment is at Madrasah.
     */
    public function isAtMadrasah(): bool
    {
        return $this->payment_location === 'MADRASAH';
    }

    /**
     * Check if payment is at Sekolah (SMP/MA).
     */
    public function isAtSekolah(): bool
    {
        return $this->payment_location === 'SEKOLAH';
    }

    /**
     * Mark this transaction as settled (funds distributed).
     */
    public function markAsSettled(): void
    {
        $this->update(['is_settled' => true]);
    }

    /**
     * Get the total amount already distributed from this transaction.
     */
    public function getDistributedAmount(): float
    {
        return (float) $this->fundTransfers()->sum('amount');
    }

    /**
     * Get the remaining amount that hasn't been distributed yet.
     */
    public function getUndistributedAmount(): float
    {
        return max(0, (float) $this->amount - $this->getDistributedAmount());
    }

    /**
     * Check if this transaction is fully distributed.
     */
    public function isFullyDistributed(): bool
    {
        return $this->getUndistributedAmount() <= 0;
    }

    /**
     * Get the public download URL for this transaction receipt.
     */
    public function getDownloadUrl(): string
    {
        return url("/transaksi/{$this->verification_token}");
    }

    /**
     * Get the public verification URL for this transaction.
     */
    public function getVerifyUrl(): string
    {
        return url("/transaksi/verify/{$this->verification_token}");
    }

    /**
     * Get friendly payment location label based on who entered the transaction.
     */
    public function getPaymentLocationLabel(): string
    {
        return match ($this->payment_location) {
            'PANITIA' => 'Panitia',
            'MADRASAH' => 'Bendahara Madrasah',
            'SEKOLAH' => $this->user?->institution
                ? 'Bendahara ' . $this->user->institution->name
                : 'Bendahara Sekolah',
            'UNIT' => $this->user?->institution
                ? 'Bendahara ' . $this->user->institution->name
                : 'Unit',
            default => 'Unknown',
        };
    }
}
