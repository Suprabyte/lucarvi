<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReporteAusenciasController extends Controller
{
    /**
     * Reporte 1: Agrupado por trabajador y mes
     */
    public function reportePorTrabajador()
    {
        // Usamos Carbon para poner los nombres de los meses en español
        Carbon::setLocale('es');

        $datos = DB::table('empleados as e')
            ->join('ausencias as au', 'au.empleado_id', '=', 'e.id')
            ->select(
                DB::raw('CONCAT(e.apellidos, ", ", e.nombres) as nombre_completo'),
                DB::raw('YEAR(au.fecha) as anio'),
                DB::raw('MONTH(au.fecha) as mes_numero'),
                'au.motivo',
                DB::raw('COUNT(au.id) as total')
            )
            ->groupBy('nombre_completo', 'anio', 'mes_numero', 'motivo')
            ->orderBy('anio', 'desc')
            ->orderBy('mes_numero', 'asc')
            ->orderBy('nombre_completo', 'asc')
            ->get()
            ->map(function ($item) {
                // Creamos el nombre del mes
                $item->mes_nombre = Carbon::create()->month($item->mes_numero)->translatedFormat('F');
                return $item;
            });

        // Agrupamos por trabajador para que la vista sea más fácil de leer
        $reporteAgrupado = $datos->groupBy('nombre_completo');

        return view('reportes.pdpor_trabajador', ['reporte' => $reporteAgrupado]);
    }

    /**
     * Reporte 2: General por mes (para el gráfico)
     */
    public function reporteGeneralPorMes()
    {
        Carbon::setLocale('es');

        $datos = DB::table('ausencias as au')
            ->select(
                DB::raw('YEAR(au.fecha) as anio'),
                DB::raw('MONTH(au.fecha) as mes_numero'),
                'au.motivo',
                DB::raw('COUNT(au.id) as total')
            )
            ->groupBy('anio', 'mes_numero', 'au.motivo')
            ->orderBy('anio', 'asc')
            ->orderBy('mes_numero', 'asc')
            ->get();

        // --- Preparamos los datos para Chart.js ---

        $labels = []; // Eje X: Meses (ej. "Enero 2025")
        $datasets = []; // Datos: Un dataset por cada "motivo"
        
        $motivos = $datos->pluck('motivo')->unique();
        $periodos = $datos->map(function($item) {
            // Formato 'YYYY-MM' para ordenar fácil
            return $item->anio . '-' . str_pad($item->mes_numero, 2, '0', STR_PAD_LEFT);
        })->unique()->sort();

        // 1. Crear Labels (los meses)
        foreach ($periodos as $periodo) {
            [$anio, $mes_num] = explode('-', $periodo);
            $labels[] = Carbon::create($anio, $mes_num, 1)->translatedFormat('F Y');
        }

        // Colores para los motivos (puedes agregar más)
        $colores = [
            'FALTA NO JUSTIFICADA' => 'rgba(239, 68, 68, 0.7)',  // Rojo
            'DESCANSO SEMANAL' => 'rgba(34, 197, 94, 0.7)',    // Verde
            'SANCION DISCIPLINARIA' => 'rgba(249, 115, 22, 0.7)', // Naranja
            'default' => 'rgba(107, 114, 128, 0.7)' // Gris
        ];

        // 2. Crear Datasets (los motivos)
        foreach ($motivos as $motivo) {
            $dataParaMotivo = [];
            foreach ($periodos as $periodo) {
                [$anio, $mes_num] = explode('-', $periodo);
                
                // Buscamos el total para este motivo y este mes
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

        $chartData = json_encode(['labels' => $labels, 'datasets' => $datasets]);

        return view('reportes.pdf.general_mes', ['chartData' => $chartData]);
    }
}