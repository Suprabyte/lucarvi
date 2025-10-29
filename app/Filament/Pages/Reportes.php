<?php

namespace App\Filament\Pages;

use App\Models\Empleado;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;

class Reportes extends Page
{
    protected static \BackedEnum|string|null $navigationIcon  = 'heroicon-o-document-chart-bar';
    protected static ?string                 $navigationLabel = 'Reportes';
    protected static \UnitEnum|string|null   $navigationGroup = 'RR.HH';
    protected static ?int                    $navigationSort  = 95;

    protected static ?string $slug = 'reportes';
    protected string $view = 'filament.pages.reportes';

    /** Dispara un evento de navegador para abrir el PDF en popup */
    private function openReport(string $routeName, array $params = []): void
    {
        $url = route($routeName, array_filter($params));
        // Livewire v3: browser event
        $this->dispatch('open-window', url: $url);
    }

    protected function getHeaderActions(): array
    {
        $defaultDesde = now('America/Lima')->startOfMonth()->toDateString();
        $defaultHasta = now('America/Lima')->toDateString();

        $fechaFields = [
            DatePicker::make('desde')->label('Desde')->default($defaultDesde)->required(),
            DatePicker::make('hasta')->label('Hasta')->default($defaultHasta)->required(),
        ];

        $filtrosFields = [
            Select::make('empleado_id')
                ->label('Empleado')
                ->searchable()
                ->placeholder('Todos')
                ->options(fn () =>
                    Empleado::query()
                        ->select('id', 'apellidos', 'nombres')
                        ->orderBy('apellidos')->orderBy('nombres')
                        ->get()
                        ->mapWithKeys(fn ($e) => [$e->id => "{$e->apellidos} {$e->nombres}"])
                ),
            TextInput::make('area_id')->label('Área ID')->numeric()->placeholder('Todas'),
        ];

        return [
            Action::make('pdfAsistencia')
                ->label('PDF Asistencia')->icon('heroicon-o-document-text')->color('gray')
                ->form([...$fechaFields, ...$filtrosFields])
                ->modalSubmitActionLabel('Abrir')
                ->action(function (array $data) {
                    $this->openReport('reportes.asistencia', [
                        'desde'       => $data['desde'] ?? null,
                        'hasta'       => $data['hasta'] ?? null,
                        'empleado_id' => $data['empleado_id'] ?? null,
                        'area_id'     => $data['area_id'] ?? null,
                    ]);
                }),

            Action::make('pdfInasistencias')
                ->label('PDF Inasistencias')->icon('heroicon-o-exclamation-triangle')->color('warning')
                ->form([...$fechaFields, ...$filtrosFields])
                ->modalSubmitActionLabel('Abrir')
                ->action(function (array $data) {
                    $this->openReport('reportes.inasistencias', [
                        'desde'       => $data['desde'] ?? null,
                        'hasta'       => $data['hasta'] ?? null,
                        'empleado_id' => $data['empleado_id'] ?? null,
                        'area_id'     => $data['area_id'] ?? null,
                    ]);
                }),

            Action::make('pdfTardanzas')
                ->label('PDF Tardanzas')->icon('heroicon-o-clock')->color('rose')
                ->form([...$fechaFields, ...$filtrosFields])
                ->modalSubmitActionLabel('Abrir')
                ->action(function (array $data) {
                    $this->openReport('reportes.tardanzas', [
                        'desde'       => $data['desde'] ?? null,
                        'hasta'       => $data['hasta'] ?? null,
                        'empleado_id' => $data['empleado_id'] ?? null,
                        'area_id'     => $data['area_id'] ?? null,
                    ]);
                }),

            Action::make('pdfConsolidado')
                ->label('PDF Consolidado')->icon('heroicon-o-clipboard-document-check')->color('indigo')
                ->form([...$fechaFields, TextInput::make('area_id')->label('Área ID')->numeric()->placeholder('Todas')])
                ->modalSubmitActionLabel('Abrir')
                ->action(function (array $data) {
                    $this->openReport('reportes.consolidado', [
                        'desde'   => $data['desde'] ?? null,
                        'hasta'   => $data['hasta'] ?? null,
                        'area_id' => $data['area_id'] ?? null,
                    ]);
                }),

            Action::make('pdfValorizado')
                ->label('PDF Valorizado')->icon('heroicon-o-currency-dollar')->color('emerald')
                ->form([
                    ...$fechaFields,
                    ...$filtrosFields,
                    TextInput::make('tarifa_hora')->label('Tarifa por hora (S/.)')->numeric()->default(0)->minValue(0),
                ])
                ->modalSubmitActionLabel('Abrir')
                ->action(function (array $data) {
                    $this->openReport('reportes.valorizado', [
                        'desde'       => $data['desde'] ?? null,
                        'hasta'       => $data['hasta'] ?? null,
                        'empleado_id' => $data['empleado_id'] ?? null,
                        'area_id'     => $data['area_id'] ?? null,
                        'tarifa_hora' => $data['tarifa_hora'] ?? null,
                    ]);
                }),
        ];
    }
}
