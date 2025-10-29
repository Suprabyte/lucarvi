<?php

namespace App\Filament\Resources\Empleados;

use App\Filament\Resources\Empleados\Pages;
use App\Models\{Empleado, Area, Cargo};
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Columns\{TextColumn, BadgeColumn};
use UnitEnum; 
use BackedEnum;
use Filament\Schemas\Schema;
use Filament\Forms\Components\{TextInput, DatePicker, Select, TimePicker};

use Illuminate\Validation\Rule;


class EmpleadoResource extends Resource
{
    protected static ?string $model = Empleado::class;
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-user-group';
    protected static UnitEnum|string|null $navigationGroup = 'RR.HH'; // 👈 tipo correcto

    public static function form(Schema $schema): Schema
{
    return $schema->schema([
        TextInput::make('dni')->label('DNI')->required()->maxLength(8)->unique(ignoreRecord: true),


        TextInput::make('apellidos')->required()->maxLength(120),
        TextInput::make('nombres')->required()->maxLength(120),
        DatePicker::make('fecha_nacimiento'),
        TextInput::make('direccion')->columnSpanFull(),
        TextInput::make('celular')->nullable(),
        TextInput::make('email')->email()->nullable(),
        Select::make('sexo')->options(['M' => 'M', 'F' => 'F']),
        Select::make('estado')
            ->options(['ACTIVO' => 'Activo', 'CESADO' => 'Cesado'])
            ->default('ACTIVO'),
        DatePicker::make('fecha_ingreso'),
        TextInput::make('tipo_trabajador'),
        TextInput::make('sueldo')->numeric()->prefix('S/')->default(0),
        Select::make('area_id')
            ->label('Área')
            ->options(\App\Models\Area::pluck('nombre', 'id'))
            ->searchable(),
        Select::make('cargo_id')
            ->label('Cargo')
            ->options(\App\Models\Cargo::pluck('nombre', 'id'))
            ->searchable(),
    ])->columns(2);
}

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('dni')->searchable()->sortable(),
                TextColumn::make('apellidos')->searchable()->sortable(),
                TextColumn::make('nombres')->searchable()->sortable(),
                TextColumn::make('area.nombre')->label('Área')->badge(),
                TextColumn::make('cargo.nombre')->label('Cargo')->badge(),
                BadgeColumn::make('estado')->colors([
                    'success' => 'ACTIVO',
                    'danger' => 'CESADO',
                ]),
                TextColumn::make('fecha_ingreso')->date(),
                TextColumn::make('sueldo')->money('PEN', true),
            ])
            ->defaultSort('apellidos');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListEmpleados::route('/'),
            'create' => Pages\CreateEmpleado::route('/create'),
            'edit'   => Pages\EditEmpleado::route('/{record}/edit'),
            // Si generaste página de solo lectura (View):
            // 'view' => Pages\ViewEmpleado::route('/{record}'),
        ];
    }
}
