<?php

namespace App\Filament\Widgets;

use App\Models\Student;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RegistrationStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getPollingInterval(): ?string
    {
        return '5s';
    }

    protected function getStats(): array
    {
        $user = auth()->user();
        $query = Student::query();

        // Filter by institution if user has one (except Admin)
        if ($user->institution_id && !$user->hasRole('Administrator')) {
            $institution = \App\Models\Institution::find($user->institution_id);
            if (!in_array($institution?->type, ['pondok', 'madrasah'])) {
                $query->whereHas('registration', function ($q) use ($user) {
                    $q->where('destination_institution_id', $user->institution_id);
                });
            }
        }

        $total = (clone $query)->count();
        $male = (clone $query)->where('gender', 'male')->count();
        $female = (clone $query)->where('gender', 'female')->count();

        return [
            Stat::make('Total Pendaftar', $total)
                ->description('Semua santri')
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),

            Stat::make('Laki-laki', $male)
                ->description('Santri putra')
                ->descriptionIcon('heroicon-m-user')
                ->color('info'),

            Stat::make('Perempuan', $female)
                ->description('Santri putri')
                ->descriptionIcon('heroicon-m-user')
                ->color('danger'),
        ];
    }
}
