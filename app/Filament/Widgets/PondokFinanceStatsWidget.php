<?php

namespace App\Filament\Widgets;

class PondokFinanceStatsWidget extends BaseFinanceStatsWidget
{
    protected static ?int $sort = 3;

    protected static function getInstitutionTypes(): ?array
    {
        return ['pondok', 'madrasah'];
    }

    protected static function getWidgetLabel(): string
    {
        return 'Pondok';
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
