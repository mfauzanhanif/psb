<?php

namespace App\Filament\Widgets;

use App\Services\PaymentDistributionService;
use Filament\Widgets\Widget;

class FundSummaryWidget extends Widget
{
    protected string $view = 'filament.resources.fund-transfer.header';

    protected int|string|array $columnSpan = 'full';

    public function getSummary(): array
    {
        $service = app(PaymentDistributionService::class);
        return $service->getFundSummary()->values()->toArray();
    }

    public function getFloatingCash(): float
    {
        $service = app(PaymentDistributionService::class);
        return $service->getFloatingCashAtPanitia();
    }

    public function getPendingSettlements(): array
    {
        return \App\Models\FundTransfer::whereIn('status', ['PENDING', 'APPROVED'])
            ->with('institution')
            ->get()
            ->groupBy('institution_id')
            ->map(function ($transfers, $institutionId) {
                $institution = $transfers->first()->institution;
                return [
                    'institution' => $institution,
                    'pending_count' => $transfers->where('status', 'PENDING')->count(),
                    'approved_count' => $transfers->where('status', 'APPROVED')->count(),
                    'total_amount' => $transfers->sum('amount'),
                ];
            })
            ->values()
            ->toArray();
    }

    public function getPanitiaSummary(): array
    {
        $service = app(PaymentDistributionService::class);
        return $service->getPanitiaSummary();
    }

    protected function getViewData(): array
    {
        return [
            'panitiaSummary' => $this->getPanitiaSummary(),
            'summary' => $this->getSummary(),
            'floatingCash' => $this->getFloatingCash(),
            'pendingSettlements' => $this->getPendingSettlements(),
        ];
    }
}
