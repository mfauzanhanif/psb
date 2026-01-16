<?php

namespace App\Filament\Widgets;

class GlobalFinanceStatsWidget extends BaseFinanceStatsWidget
{
    protected static ?int $sort = 2;

    protected static function getInstitutionTypes(): ?array
    {
        return null; // All institutions
    }

    protected static function getWidgetLabel(): string
    {
        return 'Global';
    }

    public static function canView(): bool
    {
        $user = auth()->user();

        // Administrator, Petugas, Bendahara Pondok can see all
        if ($user->hasRole(['Administrator', 'Petugas', 'Bendahara Pondok'])) {
            return true;
        }

        // Kepala Pondok can see all
        if ($user->hasRole('Kepala') && $user->institution?->type === 'pondok') {
            return true;
        }

        return false;
    }
}
