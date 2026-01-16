<?php

namespace App\Filament\Resources\FeeComponentResource\Pages;

use App\Filament\Resources\FeeComponentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFeeComponents extends ListRecords
{
    protected static string $resource = FeeComponentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Rincian Biaya')
                ->modalWidth('lg'),
        ];
    }
}
