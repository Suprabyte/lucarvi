<?php

namespace App\Console\Commands;

use App\Models\Empleado;
use App\Services\AsistenciaBuilder;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerarAsistenciaCommand extends Command
{
    protected $signature = 'asistencia:generar
        {--fecha= : YYYY-MM-DD (default hoy)}
        {--dias=1 : procesar N días hacia atrás}
        {--empleado_id= : sólo este empleado}';

    protected $description = 'Genera/actualiza asistencia_dias a partir de marcacions';

    public function handle(AsistenciaBuilder $builder)
    {
        $base = $this->option('fecha') ? Carbon::parse($this->option('fecha')) : now()->startOfDay();
        $dias = (int) ($this->option('dias') ?? 1);

        $query = \App\Models\Empleado::query();
        if ($this->option('empleado_id')) {
            $query->where('id', $this->option('empleado_id'));
        }
        $empleados = $query->get();

        foreach (range(0, $dias - 1) as $off) {
            $fecha = $base->copy()->subDays($off);
            foreach ($empleados as $emp) {
                try {
                    $res = $builder->buildParaFecha($emp, $fecha);
                    $this->info("OK {$emp->id} {$fecha->toDateString()} -> asistencia {$res->id}");
                } catch (\Throwable $e) {
                    $this->error("ERR {$emp->id} {$fecha->toDateString()}: ".$e->getMessage());
                }
            }
        }
        return self::SUCCESS;
    }
}
