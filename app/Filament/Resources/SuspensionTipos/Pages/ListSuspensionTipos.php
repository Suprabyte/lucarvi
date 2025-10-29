<?php

namespace App\Filament\Resources\SuspensionTipos\Pages;

use App\Filament\Resources\SuspensionTipos\SuspensionTipoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSuspensionTipos extends ListRecords
{
    protected static string $resource = SuspensionTipoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
