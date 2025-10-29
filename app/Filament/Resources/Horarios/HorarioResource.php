<?php

namespace App\Filament\Resources\Horarios;

use App\Filament\Resources\Horarios\Pages;
use App\Models\Horario;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;                                   // ✅ nuevo esquema
use Filament\Forms\Components\{TextInput, TimePicker};
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use BackedEnum;
use UnitEnum;

class HorarioResource extends Resource
{
    protected static ?string $model = Horario::class;
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-clock';
    protected static UnitEnum|string|null $navigationGroup = 'RR.HH';

    // ✅ Cambiado a Schema (ya no Form)
    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            TextInput::make('nombre')->required(),
            TimePicker::make('ingreso')->required(),
            TimePicker::make('salida')->required(),
            TextInput::make('tolerancia_min')
                ->numeric()
                ->default(5)
                ->helperText('Minutos de tolerancia'),
            TimePicker::make('refrigerio_inicio'),
            TimePicker::make('refrigerio_fin'),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre')->searchable()->sortable(),
                TextColumn::make('ingreso'),
                TextColumn::make('salida'),
                TextColumn::make('tolerancia_min')->label('Tol. (min)'),
                TextColumn::make('refrigerio_inicio')->label('Ref. inicio'),
                TextColumn::make('refrigerio_fin')->label('Ref. fin'),
            ])
            ->defaultSort('nombre');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListHorarios::route('/'),
            'create' => Pages\CreateHorario::route('/create'),
            'edit'   => Pages\EditHorario::route('/{record}/edit'),
            'view'   => Pages\ViewHorario::route('/{record}'),
        ];
    }
}
