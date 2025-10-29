<?php

namespace App\Filament\Resources\SuspensionTipos;

use App\Filament\Resources\SuspensionTipos\Pages;
use App\Models\SuspensionTipo;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Forms\Components\{TextInput, Select, Toggle};
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Columns\{TextColumn, IconColumn};
use BackedEnum;
use UnitEnum;

class SuspensionTipoResource extends Resource
{
    protected static ?string $model = SuspensionTipo::class;
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static UnitEnum|string|null $navigationGroup = 'RR.HH';
    protected static ?string $navigationLabel = 'Tipos de Suspensión';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Select::make('tipo')
                ->options(['SP' => 'SP', 'SI' => 'SI'])
                ->required()
                ->native(false),

            TextInput::make('codigo')
                ->label('Código')
                ->required()
                ->maxLength(5)
                ->unique(ignoreRecord: true),

            TextInput::make('descripcion')
                ->required()
                ->maxLength(255)
                ->columnSpanFull(),

            Toggle::make('activo')->default(true)->inline(false),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('id')->sortable(),
            TextColumn::make('tipo')->badge()->sortable(),
            TextColumn::make('codigo')->badge()->sortable()->searchable(),
            TextColumn::make('descripcion')->wrap()->searchable(),
            IconColumn::make('activo')->boolean(),
            TextColumn::make('created_at')->since()->label('Creado'),
        ])->defaultSort('tipo');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListSuspensionTipos::route('/'),
            'create' => Pages\CreateSuspensionTipo::route('/create'),
            'edit'   => Pages\EditSuspensionTipo::route('/{record}/edit'),
        ];
    }
}
