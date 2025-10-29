<?php

namespace App\Filament\Resources\HorarioDetalles;

use App\Filament\Resources\HorarioDetalles\Pages;
use App\Models\{HorarioDetalle, Area, Cargo};
use Filament\Resources\Resource;
use Filament\Schemas\Schema; // v4 usa Schema para formularios
use Filament\Forms\Components\{Select, TimePicker, Textarea}; // componentes siguen en Forms\Components
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use BackedEnum;
use UnitEnum;

class HorarioDetalleResource extends Resource
{
    protected static ?string $model = HorarioDetalle::class;
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-clock';
    protected static UnitEnum|string|null $navigationGroup = 'RR.HH';
    protected static ?string $navigationLabel = 'Horarios por Cargo';

    // ✅ Firma correcta en Filament v4
    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Select::make('area_id')
                ->label('Área')
                ->options(Area::query()->pluck('nombre', 'id'))
                ->searchable(),

            Select::make('cargo_id')
                ->label('Cargo')
                ->options(Cargo::query()->pluck('nombre', 'id'))
                ->searchable(),

            TimePicker::make('hora_ingreso')->label('Ingreso'),
            TimePicker::make('hora_salida')->label('Salida'),
            TimePicker::make('refrigerio_ingreso')->label('Ref. Ingreso'),
            TimePicker::make('refrigerio_salida')->label('Ref. Salida'),

            Textarea::make('observacion')
                ->label('Observación')
                ->maxLength(255)
                ->columnSpanFull(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('area.nombre')->label('Área')->sortable()->searchable(),
            TextColumn::make('cargo.nombre')->label('Cargo')->sortable()->searchable(),
            TextColumn::make('hora_ingreso')->label('Ingreso'),
            TextColumn::make('hora_salida')->label('Salida'),
            TextColumn::make('refrigerio_ingreso')->label('Ref. Ingreso'),
            TextColumn::make('refrigerio_salida')->label('Ref. Salida'),
            TextColumn::make('observacion')->limit(30)->wrap(),
        ])->defaultSort('area.nombre');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListHorarioDetalles::route('/'),
            'create' => Pages\CreateHorarioDetalle::route('/create'),
            'edit'   => Pages\EditHorarioDetalle::route('/{record}/edit'),
        ];
    }
}
