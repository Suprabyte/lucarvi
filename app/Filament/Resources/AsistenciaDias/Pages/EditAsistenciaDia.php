<?php

namespace App\Filament\Resources\AsistenciaDias\Pages;

use App\Filament\Resources\AsistenciaDias\AsistenciaDiaResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditAsistenciaDia extends EditRecord
{
    protected static string $resource = AsistenciaDiaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
