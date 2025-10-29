<?php

namespace App\Filament\Resources\Ausencias\Pages;

use App\Filament\Resources\Ausencias\AusenciaResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditAusencia extends EditRecord
{
    protected static string $resource = AusenciaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
