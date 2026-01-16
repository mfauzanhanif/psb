<?php

namespace App\Filament\Widgets;

class SmpFinanceStatsWidget extends BaseFinanceStatsWidget
{
    protected static ?int $sort = 5;

    protected static function getInstitutionTypes(): ?array
    {
        return ['smp'];
    }

    protected static function getWidgetLabel(): string
    {
        return 'SMP';
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

        // Kepala/Bendahara SMP
        if ($user->hasRole(['Kepala', 'Bendahara Unit']) && $user->institution?->type === 'smp') {
            return true;
        }

        return false;
    }
}
