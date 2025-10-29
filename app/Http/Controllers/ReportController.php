<?php

namespace App\Http\Controllers;

use App\Models\AsistenciaDia;
use Illuminate\Http\Request;
use Carbon\CarbonImmutable as Carbon;

class ReportController extends Controller
{
    /** Renderiza PDF (Dompdf) o HTML si Dompdf no está instalado */
    private function renderPdfOrHtml(string $view, array $data, string $filename)
    {
        // Defaults para la cabecera global
        $data['empresa'] = $data['empresa'] ?? 'NEGOCIACIONES LUCARVI E.I.R.L';
        $data['ruc']     = $data['ruc']     ?? '20511039470';
        $data['titulo']  = $data['titulo']  ?? 'REPORTE';

        if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView($view, $data)
                ->setPaper('a4', 'portrait');
            // Si quieres mejor nitidez:
            // ->setOptions(['dpi' => 120, 'defaultFont' => 'DejaVu Sans']);

            return $pdf->stream($filename . '.pdf');
        }

        // Fallback: devuelve HTML
        return view($view, $data);
    }

    /** Normaliza y valida parámetros comunes */
    private function params(Request $request): array
    {
        $desdeIn = $request->input('desde');
        $hastaIn = $request->input('hasta');

        $desde = $desdeIn ? Carbon::parse($desdeIn, 'America/Lima')->startOfDay()
                          : Carbon::now('America/Lima')->startOfMonth();

        $hasta = $hastaIn ? Carbon::parse($hastaIn, 'America/Lima')->endOfDay()
                          : Carbon::now('America/Lima')->endOfDay();

        return [
            'desde'       => $desde,
            'hasta'       => $hasta,
            'empleado_id' => $request->integer('empleado_id') ?: null,
            'area_id'     => $request->integer('area_id') ?: null,
            'tarifa_hora' => (float) $request->input('tarifa_hora', 0),
        ];
    }

    /** Arma el texto de rango para mostrar bajo la cabecera */
    private function rangoTexto(array $p): string
    {
        $fmt = fn (Carbon $c) => $c->timezone('America/Lima')->format('d/m/Y');
        return $fmt($p['desde']) . ' — ' . $fmt($p['hasta']);
    }

    /** Query base de asistencias con joins necesarios */
    private function baseQuery(array $p)
    {
        $q = AsistenciaDia::query()
            ->with(['empleado:id,nombres,apellidos,dni,area_id,cargo_id'])
            ->whereBetween('fecha', [$p['desde']->toDateString(), $p['hasta']->toDateString()])
            ->orderBy('fecha')
            ->orderBy('empleado_id');

        if ($p['empleado_id']) {
            $q->where('empleado_id', $p['empleado_id']);
        }

        if ($p['area_id']) {
            $q->whereHas('empleado', fn ($qq) => $qq->where('area_id', $p['area_id']));
        }

        return $q;
    }

    public function asistencia(Request $request)
    {
        $p    = $this->params($request);
        $rows = $this->baseQuery($p)->get();

        $data = [
            'titulo' => 'REPORTE DE ASISTENCIA',
            'p'      => $p,
            'rango'  => $this->rangoTexto($p),
            'rows'   => $rows,
        ];

        return $this->renderPdfOrHtml('reportes.pdf.asistencia', $data, 'reporte-asistencia');
    }

    public function inasistencias(Request $request)
    {
        $p    = $this->params($request);
        $rows = $this->baseQuery($p)
            ->whereNull('hora_ingreso')
            ->whereNull('hora_salida')
            ->get();

        $data = [
            'titulo' => 'REPORTE DE AUSENCIAS INJUSTIFICADAS',
            'p'      => $p,
            'rango'  => $this->rangoTexto($p),
            'rows'   => $rows,
        ];

        return $this->renderPdfOrHtml('reportes.pdf.inasistencias', $data, 'reporte-inasistencias');
    }

    public function tardanzas(Request $request)
    {
        $p    = $this->params($request);
        $rows = $this->baseQuery($p)
            ->where('min_tardanza', '>', 0)
            ->get();

        $data = [
            'titulo' => 'REPORTE DE TARDANZAS',
            'p'      => $p,
            'rango'  => $this->rangoTexto($p),
            'rows'   => $rows,
        ];

        return $this->renderPdfOrHtml('reportes.pdf.tardanzas', $data, 'reporte-tardanzas');
    }

    public function consolidado(Request $request)
    {
        $p    = $this->params($request);
        $rows = $this->baseQuery($p)->get();

        // Sumas por empleado
        $resumen = $rows->groupBy('empleado_id')->map(function ($g) {
            $primero = $g->first();
            return [
                'empleado'     => optional($primero->empleado)->apellidos . ' ' . optional($primero->empleado)->nombres,
                'dni'          => optional($primero->empleado)->dni,
                'jornadas'     => $g->count(),
                'min_trab'     => (int) $g->sum('min_trabajados'),
                'min_tard'     => (int) $g->sum('min_tardanza'),
                'min_extra25'  => (int) $g->sum('min_extra_25'),
                'min_extra35'  => (int) $g->sum('min_extra_35'),
                'min_extra100' => (int) $g->sum('min_extra_100'),
            ];
        })->values();

        $data = [
            'titulo'  => 'REPORTE CONSOLIDADO',
            'p'       => $p,
            'rango'   => $this->rangoTexto($p),
            'resumen' => $resumen,
        ];

        return $this->renderPdfOrHtml('reportes.pdf.consolidado', $data, 'reporte-consolidado');
    }

    public function valorizado(Request $request)
    {
        $p    = $this->params($request);
        $rows = $this->baseQuery($p)->get();

        $tarifa = max(0, $p['tarifa_hora']); // S/ por hora

        $data = [
            'titulo' => 'REPORTE VALORIZADO',
            'p'      => $p,
            'rango'  => $this->rangoTexto($p),
            'tarifa' => $tarifa,
            'rows'   => $rows->map(function ($r) use ($tarifa) {
                $horas   = $r->min_trabajados / 60;
                $importe = round($horas * $tarifa, 2);
                return compact('r', 'horas', 'importe');
            }),
        ];

        return $this->renderPdfOrHtml('reportes.pdf.valorizado', $data, 'reporte-valorizado');
    }
}
