<?php

namespace App\Filament\Resources\FeeComponentResource\Pages;

use App\Filament\Resources\FeeComponentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateFeeComponent extends CreateRecord
{
    protected static string $resource = FeeComponentResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
