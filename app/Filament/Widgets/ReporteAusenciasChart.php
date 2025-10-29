<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReporteAusenciasChart extends ChartWidget
{
    protected ?string $heading = 'Reporte General de Ausencias por Mes';
    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        Carbon::setLocale('es');

        $datos = DB::table('ausencias as au')
            ->select(
                DB::raw('YEAR(au.fecha) as anio'),
                DB::raw('MONTH(au.fecha) as mes_numero'),
                'au.motivo',
                DB::raw('COUNT(au.id) as total')
            )
            ->where('au.fecha', '>=', now()->subYear()) // Agregado: solo del último año
            ->groupBy('anio', 'mes_numero', 'au.motivo')
            ->orderBy('anio', 'asc')
            ->orderBy('mes_numero', 'asc')
            ->get();

        $labels = [];
        $datasets = [];
        $motivos = $datos->pluck('motivo')->unique();
        $periodos = $datos->map(function ($item) {
            return $item->anio . '-' . str_pad($item->mes_numero, 2, '0', STR_PAD_LEFT);
        })->unique()->sort();

        foreach ($periodos as $periodo) {
            [$anio, $mes_num] = explode('-', $periodo);
            $labels[] = Carbon::create($anio, $mes_num, 1)->translatedFormat('F Y');
        }

        $colores = [
            'FALTA NO JUSTIFICADA' => 'rgba(239, 68, 68, 0.7)',  // Rojo
            'DESCANSO SEMANAL' => 'rgba(34, 197, 94, 0.7)',    // Verde
            'SANCION DISCIPLINARIA' => 'rgba(249, 115, 22, 0.7)', // Naranja
            'default' => 'rgba(107, 114, 128, 0.7)' // Gris
        ];

        foreach ($motivos as $motivo) {
            $dataParaMotivo = [];
            foreach ($periodos as $periodo) {
                [$anio, $mes_num] = explode('-', $periodo);
                $total = $datos->where('anio', $anio)
                    ->where('mes_numero', $mes_num)
                    ->where('motivo', $motivo)
                    ->sum('total');
                $dataParaMotivo[] = $total;
            }

            $datasets[] = [
                'label' => $motivo,
                'data' => $dataParaMotivo,
                'backgroundColor' => $colores[$motivo] ?? $colores['default'],
            ];
        }

        return [
            'datasets' => $datasets,
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
            ],
            'scales' => [
                'x' => ['stacked' => true],
                'y' => ['stacked' => true],
            ],
        ];
    }
}