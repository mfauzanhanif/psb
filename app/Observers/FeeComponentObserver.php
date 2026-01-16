<?php

namespace App\Observers;

use App\Models\Bill;
use App\Models\FeeComponent;

class FeeComponentObserver
{
    /**
     * Handle the FeeComponent "created" event.
     */
    public function created(FeeComponent $feeComponent): void
    {
        $this->recalculateBillsForInstitution($feeComponent->institution_id);
    }

    /**
     * Handle the FeeComponent "updated" event.
     */
    public function updated(FeeComponent $feeComponent): void
    {
        $this->recalculateBillsForInstitution($feeComponent->institution_id);
    }

    /**
     * Handle the FeeComponent "deleted" event.
     */
    public function deleted(FeeComponent $feeComponent): void
    {
        $this->recalculateBillsForInstitution($feeComponent->institution_id);
    }

    /**
     * Recalculate all bills for a specific institution.
     */
    protected function recalculateBillsForInstitution(int $institutionId): void
    {
        $bills = Bill::where('institution_id', $institutionId)->get();

        foreach ($bills as $bill) {
            $bill->recalculateAmount();
        }
    }
}
