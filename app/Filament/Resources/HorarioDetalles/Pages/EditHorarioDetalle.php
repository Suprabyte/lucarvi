<?php

namespace App\Filament\Resources\HorarioDetalles\Pages;

use App\Filament\Resources\HorarioDetalles\HorarioDetalleResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditHorarioDetalle extends EditRecord
{
    protected static string $resource = HorarioDetalleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
