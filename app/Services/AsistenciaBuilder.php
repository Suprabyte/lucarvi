<?php

namespace App\Services;

use App\Models\AsistenciaDia;
use App\Models\Empleado;
use App\Models\Marcacion;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AsistenciaBuilder
{
    /** Tolerancias (minutos) */
    private int $toleranciaMin   = 20;   // ventana +/- para casar una marca con la hora referencia
    private int $preAperturaMin  = 90;   // antes del ingreso esperado para abrir rango
    private int $postCierreMin   = 180;  // después de la salida esperada para cerrar rango

    public function buildParaFecha(Empleado $empleado, Carbon $fechaAncla): AsistenciaDia
    {
        $tabla = 'horarios_detalle';
        $cols  = Schema::getColumnListing($tabla);

        // Columnas posibles en tu tabla de horarios
        $cIng   = $this->pickCol($cols, ['ingreso','hora_ingreso','entrada','hora_entrada']);
        $cSal   = $this->pickCol($cols, ['salida','hora_salida']);
        $cRefIn = $this->pickCol($cols, ['ref_ingreso','refrigerio_ingreso','hora_ref_ingreso','ref_entrada']);
        $cRefOut= $this->pickCol($cols, ['ref_salida','refrigerio_salida','hora_ref_salida']);

        foreach (['Ingreso'=>$cIng, 'Salida'=>$cSal, 'Ref. Ingreso'=>$cRefIn, 'Ref. Salida'=>$cRefOut] as $label=>$col) {
            if (!$col) {
                throw new \RuntimeException("No puedo encontrar columna para {$label} en {$tabla}. Columnas: ".implode(', ', $cols));
            }
        }

        // Horario por cargo del empleado
        $hd = DB::table($tabla)->where('cargo_id', $empleado->cargo_id)->first();
        if (!$hd) {
            throw new \RuntimeException("No hay registro en {$tabla} para cargo_id={$empleado->cargo_id}");
        }

        // Referencias (como DATETIME) usando la fecha ancla + time del horario
        $ingExp    = $this->atDateTime($fechaAncla, (string)($hd->{$cIng}    ?? '00:00:00'));
        $salExp    = $this->atDateTime($fechaAncla, (string)($hd->{$cSal}    ?? '00:00:00'));
        $refInExp  = $this->atDateTime($fechaAncla, (string)($hd->{$cRefIn}  ?? '00:00:00'));
        $refOutExp = $this->atDateTime($fechaAncla, (string)($hd->{$cRefOut} ?? '00:00:00'));

        // Cruce de medianoche
        if ($salExp->lessThanOrEqualTo($ingExp)) {
            $salExp->addDay();
            if ($refInExp->lessThan($ingExp))  $refInExp->addDay();
            if ($refOutExp->lessThan($ingExp)) $refOutExp->addDay();
        }

        // Rango de búsqueda de marcaciones
        $inicioRango = $ingExp->copy()->subMinutes($this->preAperturaMin);
        $finRango    = $salExp->copy()->addMinutes($this->postCierreMin);

        // Traer marcaciones del rango (forzamos Carbon por si algún cast faltara)
        $marcas = Marcacion::where('empleado_id', $empleado->id)
            ->whereBetween('timestamp', [$inicioRango, $finRango])
            ->orderBy('timestamp')
            ->get()
            ->map(function ($m) {
                $m->timestamp = $m->timestamp instanceof Carbon ? $m->timestamp : Carbon::parse((string)$m->timestamp);
                return $m;
            });

        // Selector con ventana de tolerancia alrededor de una referencia
        $pick = function (Carbon $ref) use ($marcas) {
            $ini = $ref->copy()->subMinutes($this->toleranciaMin);
            $fin = $ref->copy()->addMinutes($this->toleranciaMin);
            $cand = $marcas->filter(fn($m) => $m->timestamp->between($ini, $fin));
            if ($cand->isEmpty()) return null;
            $centro = $ini->copy()->addSeconds($fin->diffInSeconds($ini) / 2);
            return $cand->sortBy(fn($m) => abs($m->timestamp->diffInSeconds($centro)))
                        ->first()->timestamp->copy();
        };

        // Intento principal por ventana
        $horaIngreso    = $pick($ingExp);
        $horaSalidaRef  = $pick($refOutExp);
        $horaRetornoRef = $pick($refInExp);
        $horaSalida     = $pick($salExp);

        // Heurísticas si no hubo match por ventana
        if (!$horaIngreso && $marcas->isNotEmpty()) $horaIngreso = $marcas->first()->timestamp->copy();
        if (!$horaSalida  && $marcas->isNotEmpty()) $horaSalida  = $marcas->last()->timestamp->copy();

        // Si no hay refri por ventana, detectar por mayor gap en la jornada
        if ((!$horaSalidaRef || !$horaRetornoRef) && $marcas->count() >= 3) {
            [$gapStart, $gapEnd] = $this->mayorGap($marcas, $ingExp, $salExp);
            if ($gapStart && $gapEnd) {
                $horaSalidaRef  = $horaSalidaRef  ?: $gapStart->copy();
                $horaRetornoRef = $horaRetornoRef ?: $gapEnd->copy();
            }
        }

        // Caso con 1 sola marca: decidir si fue ingreso o salida por cercanía
        if ($marcas->count() === 1) {
            $unica = $marcas->first()->timestamp;
            if ($unica->diffInMinutes($ingExp) <= $unica->diffInMinutes($salExp)) {
                $horaIngreso = $unica->copy();
            } else {
                $horaSalida  = $unica->copy();
            }
        }

        /* ===================== CÁLCULOS NORMALIZADOS ===================== */

        // helper: minutos enteros y no negativos
        $intMinutes = function (?Carbon $a, ?Carbon $b): int {
            if (!$a instanceof Carbon || !$b instanceof Carbon) return 0;
            return max(0, $a->diffInMinutes($b));
        };

        // Validar refrigerio dentro de jornada y orden correcto
        $refrigerioOk = false;
        if ($horaSalidaRef && $horaRetornoRef) {
            $refrigerioOk =
                $horaSalidaRef->lessThan($horaRetornoRef) &&
                $horaSalidaRef->between($ingExp->copy()->subMinutes(5), $salExp->copy()->addMinutes(5)) &&
                $horaRetornoRef->between($ingExp->copy()->subMinutes(5), $salExp->copy()->addMinutes(5));

            if (!$refrigerioOk) {
                $horaSalidaRef = null;
                $horaRetornoRef = null;
            }
        }

        // Minutos trabajados
        $minTrabajados = 0;
        if ($horaIngreso && $horaSalida && $horaSalida->greaterThan($horaIngreso)) {
            $minTrabajados = $intMinutes($horaIngreso, $horaSalida);
            if ($refrigerioOk) {
                $minTrabajados -= $intMinutes($horaSalidaRef, $horaRetornoRef);
            }
        }
        $minTrabajados = max(0, (int)$minTrabajados);

        // Tardanza (solo si llega después del ingreso esperado)
        $minTardanza = 0;
        if ($horaIngreso) {
            $dif = $ingExp->diffInMinutes($horaIngreso, false); // signed
            $minTardanza = $dif > 0 ? (int)$dif : 0;
        }

        // Extras (simple: todo lo posterior a la salida esperada)
        $minExtra25  = 0;
        $minExtra35  = 0;
        $minExtra100 = 0;
        if ($horaSalida && $horaSalida->greaterThan($salExp)) {
            $minExtra25 = $intMinutes($salExp, $horaSalida);
        }
        // clamp
        $minExtra25  = max(0, (int)$minExtra25);
        $minExtra35  = max(0, (int)$minExtra35);
        $minExtra100 = max(0, (int)$minExtra100);

        // ===================== GUARDAR =====================
        $payload = [
            'empleado_id'             => $empleado->id,
            'fecha'                   => $fechaAncla->toDateString(),
            'hora_ingreso'            => $this->toTimeOrNull($horaIngreso),
            'hora_salida_refrigerio'  => $this->toTimeOrNull($horaSalidaRef),
            'hora_retorno_refrigerio' => $this->toTimeOrNull($horaRetornoRef),
            'hora_salida'             => $this->toTimeOrNull($horaSalida),
            'min_trabajados'          => (int)$minTrabajados,
            'min_tardanza'            => (int)$minTardanza,
            'min_extra_25'            => (int)$minExtra25,
            'min_extra_35'            => (int)$minExtra35,
            'min_extra_100'           => (int)$minExtra100,
            'bloqueado'               => 0,
            'bloqueo_hash'            => $this->makeHash($empleado->id, $fechaAncla, $marcas->pluck('id')->all()),
        ];

        return AsistenciaDia::updateOrCreate(
            ['empleado_id' => $empleado->id, 'fecha' => $fechaAncla->toDateString()],
            $payload
        );
    }

    /** Devuelve el primer nombre de columna existente entre las candidatas */
    private function pickCol(array $cols, array $candidatas): ?string
    {
        $map = array_change_key_case(array_flip($cols), CASE_LOWER);
        foreach ($candidatas as $c) {
            if (isset($map[strtolower($c)])) return $cols[$map[strtolower($c)]];
        }
        return null;
    }

    /** Construye Carbon con la fecha dada + HH:MM(:SS) */
    private function atDateTime(Carbon $fecha, string $hora): Carbon
    {
        $time = trim($hora) ?: '00:00:00';
        if (strlen($time) === 5) $time .= ':00';
        return Carbon::parse($fecha->toDateString().' '.$time);
    }

    /**
     * Mayor hueco entre marcas dentro de la jornada extendida (+/- 180 min)
     * @return array{0:Carbon|null,1:Carbon|null}
     */
    private function mayorGap($marcas, Carbon $ingExp, Carbon $salExp): array
    {
        $maxGap = -1;
        $gapStart = $gapEnd = null;

        $prev = null;
        foreach ($marcas as $m) {
            $ts = $m->timestamp;
            if ($prev) {
                $gap = $prev->diffInMinutes($ts, false);
                if (
                    $gap > $maxGap &&
                    $prev->greaterThanOrEqualTo($ingExp->copy()->subMinutes(180)) &&
                    $ts->lessThanOrEqualTo($salExp->copy()->addMinutes(180))
                ) {
                    $maxGap = $gap;
                    $gapStart = $prev->copy();
                    $gapEnd   = $ts->copy();
                }
            }
            $prev = $ts;
        }

        // Consideramos almuerzo razonable si 20–150 min
        if ($maxGap >= 20 && $maxGap <= 150) {
            return [$gapStart, $gapEnd];
        }
        return [null, null];
    }

    /** Formatea a HH:MM:SS o devuelve null */
    private function toTimeOrNull($dt): ?string
    {
        if (!$dt instanceof Carbon) return null;
        return $dt->format('H:i:s');
    }

    private function makeHash(int $empleadoId, Carbon $fecha, array $marcacionIds): string
    {
        return hash('sha256', $empleadoId.'|'.$fecha->toDateString().'|'.implode(',', $marcacionIds));
    }
}
