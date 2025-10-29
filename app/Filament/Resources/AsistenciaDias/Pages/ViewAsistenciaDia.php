<?php

namespace App\Filament\Resources\AsistenciaDias\Pages;

use App\Filament\Resources\AsistenciaDias\AsistenciaDiaResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewAsistenciaDia extends ViewRecord
{
    protected static string $resource = AsistenciaDiaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
