<?php

namespace App\Filament\Resources\AsistenciaDias\Pages;

use App\Filament\Resources\AsistenciaDias\AsistenciaDiaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAsistenciaDias extends ListRecords
{
    protected static string $resource = AsistenciaDiaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
