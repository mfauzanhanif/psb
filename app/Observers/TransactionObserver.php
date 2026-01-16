<?php

namespace App\Observers;

use App\Models\Transaction;
use Illuminate\Support\Str;

class TransactionObserver
{
    /**
     * Handle the Transaction "created" event.
     * Auto-generate verification token.
     */
    public function created(Transaction $transaction): void
    {
        if (empty($transaction->verification_token)) {
            $transaction->updateQuietly([
                'verification_token' => Str::random(16)
            ]);
        }
    }
}
