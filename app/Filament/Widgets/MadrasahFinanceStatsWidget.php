<?php

namespace App\Filament\Widgets;

class MadrasahFinanceStatsWidget extends BaseFinanceStatsWidget
{
    protected static ?int $sort = 4;

    protected static function getInstitutionTypes(): ?array
    {
        return ['madrasah'];
    }

    protected static function getWidgetLabel(): string
    {
        return 'Madrasah';
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

        // Kepala/Bendahara Madrasah
        if ($user->hasRole(['Kepala', 'Bendahara Unit']) && $user->institution?->type === 'madrasah') {
            return true;
        }

        return false;
    }
}
