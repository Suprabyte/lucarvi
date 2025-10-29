<?php

namespace App\Filament\Resources\SuspensionTipos\Pages;

use App\Filament\Resources\SuspensionTipos\SuspensionTipoResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSuspensionTipo extends EditRecord
{
    protected static string $resource = SuspensionTipoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
