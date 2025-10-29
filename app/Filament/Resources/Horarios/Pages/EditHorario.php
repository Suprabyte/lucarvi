<?php

namespace App\Filament\Resources\Horarios\Pages;

use App\Filament\Resources\Horarios\HorarioResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditHorario extends EditRecord
{
    protected static string $resource = HorarioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
