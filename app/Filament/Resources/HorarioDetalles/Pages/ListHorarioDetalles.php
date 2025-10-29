<?php

namespace App\Filament\Resources\HorarioDetalles\Pages;

use App\Filament\Resources\HorarioDetalles\HorarioDetalleResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListHorarioDetalles extends ListRecords
{
    protected static string $resource = HorarioDetalleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
