<?php

namespace App\Filament\Resources\Marcacions;

use App\Filament\Resources\Marcacions\Pages;
use App\Models\{Marcacion, Empleado};
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Forms\Components\{Select, DateTimePicker, TextInput};
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Columns\{TextColumn, BadgeColumn};
use BackedEnum;
use UnitEnum;

class MarcacionResource extends Resource
{
    protected static ?string $model = Marcacion::class;
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-finger-print';
    protected static UnitEnum|string|null $navigationGroup = 'RR.HH';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Select::make('empleado_id')
                ->label('Empleado')
                ->relationship('empleado', 'apellidos')
                ->getOptionLabelFromRecordUsing(fn (Empleado $e) => "{$e->apellidos}, {$e->nombres} ({$e->dni})")
                ->searchable()
                ->required(),
            DateTimePicker::make('timestamp')->required()->seconds(false),
            Select::make('tipo')->options([
                'INGRESO' => 'Ingreso',
                'SALIDA' => 'Salida',
                'REFRIGIO_IN' => 'Refrigerio (salida)',
                'REFRIGIO_OUT' => 'Refrigerio (retorno)',
            ]),
            TextInput::make('origen')->maxLength(50),
            TextInput::make('hash_seguridad')->maxLength(100),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('empleado.apellidos')->label('Apellidos')->searchable()->sortable(),
            TextColumn::make('empleado.nombres')->label('Nombres')->searchable()->sortable(),
            TextColumn::make('empleado.dni')->label('DNI')->searchable(),
            TextColumn::make('timestamp')->dateTime('Y-m-d H:i'),
            BadgeColumn::make('tipo')->colors([
                'primary' => 'INGRESO',
                'warning' => 'REFRIGIO_IN',
                'success' => 'REFRIGIO_OUT',
                'danger'  => 'SALIDA',
            ]),
            TextColumn::make('origen')->toggleable(isToggledHiddenByDefault: true),
        ])->defaultSort('timestamp', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListMarcacions::route('/'),
            'create' => Pages\CreateMarcacion::route('/create'),
            'edit'   => Pages\EditMarcacion::route('/{record}/edit'),
        ];
    }
}
