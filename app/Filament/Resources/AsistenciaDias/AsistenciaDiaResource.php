<?php

namespace App\Filament\Resources\AsistenciaDias;

use App\Filament\Resources\AsistenciaDias\Pages;
use App\Models\{AsistenciaDia, Empleado};
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Forms\Components\{Select, DatePicker, TimePicker, Toggle, TextInput};
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Columns\{TextColumn, IconColumn, BadgeColumn};
use Filament\Tables\Filters\{SelectFilter, Filter};
use Filament\Forms\Get;
use BackedEnum;
use UnitEnum;

class AsistenciaDiaResource extends Resource
{
    protected static ?string $model = AsistenciaDia::class;
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-clipboard-document-check';
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
            DatePicker::make('fecha')->required(),
            Select::make('tipo_suspension')->options([
                'NINGUNA' => 'Ninguna',
                'SP'      => 'Suspensión Perfecta',
                'SI'      => 'Suspensión Imperfecta',
            ])->default('NINGUNA'),

            TimePicker::make('hora_ingreso'),
            TimePicker::make('hora_salida_refrigerio'),
            TimePicker::make('hora_retorno_refrigerio'),
            TimePicker::make('hora_salida'),

            TextInput::make('min_trabajados')->numeric()->default(0),
            TextInput::make('min_tardanza')->numeric()->default(0),
            TextInput::make('min_extra_25')->numeric()->default(0),
            TextInput::make('min_extra_35')->numeric()->default(0),
            TextInput::make('min_extra_100')->numeric()->default(0),

            Toggle::make('bloqueado')->inline(false),
            TextInput::make('bloqueo_hash')->disabled()->dehydrated(false),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('fecha')->date()->sortable(),
                TextColumn::make('empleado.apellidos')->label('Apellidos')->searchable()->sortable(),
                TextColumn::make('empleado.nombres')->label('Nombres')->searchable()->sortable(),
                BadgeColumn::make('tipo_suspension')->colors([
                    'gray'   => 'NINGUNA',
                    'danger' => 'SP',
                    'warning'=> 'SI',
                ]),
                TextColumn::make('hora_ingreso'),
                TextColumn::make('hora_salida_refrigerio')->label('Ref. salida'),
                TextColumn::make('hora_retorno_refrigerio')->label('Ref. retorno'),
                TextColumn::make('hora_salida'),
                TextColumn::make('min_trabajados')->label('Min. trabajados'),
                TextColumn::make('min_tardanza')->label('Tardanza'),
                IconColumn::make('bloqueado')->boolean(),
            ])
            ->filters([
                Filter::make('rango_fecha')
                    ->form([ DatePicker::make('desde'), DatePicker::make('hasta') ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['desde'] ?? null, fn($q, $d) => $q->whereDate('fecha', '>=', $d))
                            ->when($data['hasta'] ?? null, fn($q, $h) => $q->whereDate('fecha', '<=', $h));
                    }),
                SelectFilter::make('empleado_id')
                    ->label('Empleado')
                    ->options(Empleado::orderBy('apellidos')->get()->pluck('apellidos','id')->toArray()),
                SelectFilter::make('tipo_suspension')->options([
                    'NINGUNA' => 'Ninguna',
                    'SP' => 'SP',
                    'SI' => 'SI',
                ]),
            ])
            ->defaultSort('fecha', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListAsistenciaDias::route('/'),
            'create' => Pages\CreateAsistenciaDia::route('/create'),
            'edit'   => Pages\EditAsistenciaDia::route('/{record}/edit'),
            'view'   => Pages\ViewAsistenciaDia::route('/{record}'),
        ];
    }
}
