<?php

namespace App\Filament\Resources\Cargos;

use App\Filament\Resources\Cargos\Pages;
use App\Models\Cargo;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use BackedEnum;
use UnitEnum;

class CargoResource extends Resource
{
    protected static ?string $model = Cargo::class;
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-briefcase';
    protected static UnitEnum|string|null $navigationGroup = 'RR.HH';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            TextInput::make('nombre')->required()->unique(ignoreRecord: true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('id')->sortable(),
            TextColumn::make('nombre')->searchable()->sortable(),
            TextColumn::make('created_at')->dateTime('Y-m-d H:i'),
        ])->defaultSort('nombre');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCargos::route('/'),
            'create' => Pages\CreateCargo::route('/create'),
            'edit'   => Pages\EditCargo::route('/{record}/edit'),
        ];
    }
}
