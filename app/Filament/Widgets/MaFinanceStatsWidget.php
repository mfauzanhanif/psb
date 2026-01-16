<?php

namespace App\Filament\Widgets;

class MaFinanceStatsWidget extends BaseFinanceStatsWidget
{
    protected static ?int $sort = 6;

    protected static function getInstitutionTypes(): ?array
    {
        return ['ma'];
    }

    protected static function getWidgetLabel(): string
    {
        return 'MA';
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

        // Kepala/Bendahara MA
        if ($user->hasRole(['Kepala', 'Bendahara Unit']) && $user->institution?->type === 'ma') {
            return true;
        }

        return false;
    }
}
