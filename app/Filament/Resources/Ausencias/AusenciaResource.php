<?php

namespace App\Filament\Resources\Ausencias;

use App\Filament\Resources\Ausencias\Pages;
use App\Models\{Ausencia, Empleado};
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Forms\Components\{Select, DatePicker, Textarea};
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Columns\{TextColumn, BadgeColumn};
use Filament\Tables\Filters\{SelectFilter, Filter};
use BackedEnum;
use UnitEnum;

class AusenciaResource extends Resource
{
    protected static ?string $model = Ausencia::class;
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-user-minus';
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
            Select::make('tipo')->options([
                'SP' => 'Suspensión Perfecta',
                'SI' => 'Suspensión Imperfecta',
                'VACACIONES' => 'Vacaciones',
                'DESCANSO_MEDICO' => 'Descanso Médico',
                'OTRO' => 'Otro',
            ])->required(),
            Textarea::make('motivo')->rows(3),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('fecha')->date()->sortable(),
            TextColumn::make('empleado.apellidos')->label('Apellidos')->searchable()->sortable(),
            TextColumn::make('empleado.nombres')->label('Nombres')->searchable()->sortable(),
            BadgeColumn::make('tipo')->colors([
                'danger'  => 'SP',
                'warning' => 'SI',
                'primary' => 'VACACIONES',
                'gray'    => 'DESCANSO_MEDICO',
                'info'    => 'OTRO',
            ]),
            TextColumn::make('motivo')->limit(50),
        ])->defaultSort('fecha','desc')->filters([
            SelectFilter::make('tipo')->options([
                'SP'=>'SP','SI'=>'SI','VACACIONES'=>'Vacaciones','DESCANSO_MEDICO'=>'Descanso Médico','OTRO'=>'Otro',
            ]),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListAusencias::route('/'),
            'create' => Pages\CreateAusencia::route('/create'),
            'edit'   => Pages\EditAusencia::route('/{record}/edit'),
            'view'   => Pages\ViewAusencia::route('/{record}'),
        ];
    }
}
