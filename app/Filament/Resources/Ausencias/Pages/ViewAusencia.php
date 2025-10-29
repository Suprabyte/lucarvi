<?php

namespace App\Filament\Resources\Ausencias\Pages;

use App\Filament\Resources\Ausencias\AusenciaResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewAusencia extends ViewRecord
{
    protected static string $resource = AusenciaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
