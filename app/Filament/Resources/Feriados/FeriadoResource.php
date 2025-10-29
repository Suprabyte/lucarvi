<?php

namespace App\Filament\Resources\Feriados;

use App\Filament\Resources\Feriados\Pages;
use App\Models\Feriado;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Forms\Components\{DatePicker, TextInput, Toggle};
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Columns\{TextColumn, IconColumn};
use BackedEnum;
use UnitEnum;

class FeriadoResource extends Resource
{
    protected static ?string $model = Feriado::class;
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-calendar-days';
    protected static UnitEnum|string|null $navigationGroup = 'RR.HH';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            DatePicker::make('fecha')->required(),
            TextInput::make('descripcion')->required()->maxLength(120),
            Toggle::make('laborable')->inline(false)->default(false),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('fecha')->date()->sortable(),
            TextColumn::make('descripcion')->searchable()->sortable(),
            IconColumn::make('laborable')->boolean(),
        ])->defaultSort('fecha','desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListFeriados::route('/'),
            'create' => Pages\CreateFeriado::route('/create'),
            'edit'   => Pages\EditFeriado::route('/{record}/edit'),
            'view'   => Pages\ViewFeriado::route('/{record}'),
        ];
    }
}
