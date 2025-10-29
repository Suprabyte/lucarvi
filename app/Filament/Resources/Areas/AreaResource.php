<?php

namespace App\Filament\Resources\Areas;

use App\Filament\Resources\Areas\Pages;
use App\Models\Area;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use BackedEnum;
use UnitEnum;

class AreaResource extends Resource
{
    protected static ?string $model = Area::class;
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-building-office';
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
            'index'  => Pages\ListAreas::route('/'),
            'create' => Pages\CreateArea::route('/create'),
            'edit'   => Pages\EditArea::route('/{record}/edit'),
        ];
    }
}
