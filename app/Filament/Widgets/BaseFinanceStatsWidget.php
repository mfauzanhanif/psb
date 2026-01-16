<?php

namespace App\Filament\Widgets;

use App\Models\Bill;
use App\Models\Institution;
use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

abstract class BaseFinanceStatsWidget extends BaseWidget
{
    protected function getPollingInterval(): ?string
    {
        return '10s';
    }

    /**
     * Get the institution types to filter by.
     * Return null to include all institutions.
     */
    abstract protected static function getInstitutionTypes(): ?array;

    /**
     * Get the widget label/title.
     */
    abstract protected static function getWidgetLabel(): string;

    /**
     * Get the color for the stats.
     */
    protected static function getStatsColor(): string
    {
        return 'primary';
    }

    protected function getStats(): array
    {
        $institutionTypes = static::getInstitutionTypes();
        $label = static::getWidgetLabel();

        // Base queries
        $billQuery = Bill::query();
        $transactionQuery = Transaction::query();

        // Filter by institution type if specified
        if ($institutionTypes !== null) {
            $institutionIds = Institution::whereIn('type', $institutionTypes)->pluck('id');

            // Filter bills directly by institution_id
            $billQuery->whereIn('institution_id', $institutionIds);

            // Filter transactions by student's bills' institution_id
            $transactionQuery->whereHas('student.bills', function ($q) use ($institutionIds) {
                $q->whereIn('institution_id', $institutionIds);
            });
        }

        $totalBill = (clone $billQuery)->sum('amount');
        $totalPaid = (clone $transactionQuery)->sum('amount');
        $totalRemaining = (clone $billQuery)->sum('remaining_amount');

        return [
            Stat::make("[$label] Total Tagihan", 'Rp ' . number_format($totalBill, 0, ',', '.'))
                ->description('Semua tagihan')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('warning'),

            Stat::make("[$label] Sudah Dibayar", 'Rp ' . number_format($totalPaid, 0, ',', '.'))
                ->description('Total pemasukan')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make("[$label] Belum Dibayar", 'Rp ' . number_format($totalRemaining, 0, ',', '.'))
                ->description('Sisa tagihan')
                ->descriptionIcon('heroicon-m-clock')
                ->color('danger'),
        ];
    }
}

