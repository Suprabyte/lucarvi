<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use App\Models\Marcacion;
use App\Services\AsistenciaBuilder;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class AsistenciaSyncController extends Controller
{
    public function view()
    {
        return view('asistencia.sync');
    }

    public function run(Request $request, AsistenciaBuilder $builder)
    {
        $url   = $request->input('webhook_url', 'http://localhost:3120/zkteco/logs');
        $fechaForzada = $request->input('fecha'); // YYYY-MM-DD opcional
        $dias         = (int) $request->input('dias', 2);

        // 1) Llamar webhook (GET) y leer el array en "data"
        $resp = Http::timeout(25)->acceptJson()->get($url);
        if (!$resp->ok()) {
            return back()->with('sync_result', [
                'ok' => false,
                'step' => 'fetch',
                'status' => $resp->status(),
                'body' => $resp->body(),
                'msg' => 'No se pudo obtener datos del webhook',
            ]);
        }

        $data = $resp->json('data');
        if (!is_array($data)) {
            return back()->with('sync_result', [
                'ok' => false,
                'step' => 'parse',
                'msg' => 'El webhook no devolvió data[]',
                'body' => $resp->body(),
            ]);
        }

        // Definir límite de fecha
        $limiteDesde = Carbon::now('America/Lima')->startOfDay()->subDays(max(0, $dias));

        // 2) Ingesta/Upsert de marcaciones
        $inserted = 0; $updated = 0; $skipped = 0; $errores = [];
        $fechasAfectadasPorEmpleado = [];
        $noEncontrados = [];

        DB::beginTransaction();
        try {
            foreach ($data as $row) {
                try {
                    // CAMBIO CLAVE: Usar userSn como DNI ya que deviceUserId viene vacío
                    $userSn = $row['userSn'] ?? null;
                    $tsRaw = $row['recordTime_lima'] ?? null;

                    if (!$userSn || !$tsRaw) { 
                        $skipped++; 
                        continue; 
                    }

                    // Normalizar DNI con padding de ceros
                    $dni = $this->normalizarDNI($userSn);
                    
                    // Buscar empleado con estrategias múltiples
                    $empleado = $this->buscarEmpleadoPorDNI($dni);
                    
                    if (!$empleado) {
                        $noEncontrados[$userSn] = true; 
                        $skipped++; 
                        continue; 
                    }

                    // Parse del timestamp (ya trae -05:00)
                    $ts = Carbon::parse($tsRaw);
                    
                    // Validar fecha límite
                    if ($ts->lt($limiteDesde)) { 
                        $skipped++; 
                        continue; 
                    }

                    // Determinar tipo de marcación considerando horario
                    $tipo = $this->determinarTipoMarcacionConHorario($empleado, $ts);

                    // Crear hash único para evitar duplicados
                    $hashUnico = md5($empleado->id . '_' . $ts->format('Y-m-d H:i:s'));

                    // Upsert por hash_seguridad único
                    $m = Marcacion::updateOrCreate(
                        ['hash_seguridad' => $hashUnico],
                        [
                            'empleado_id' => $empleado->id,
                            'timestamp'   => $ts,
                            'tipo'        => $tipo,
                            'origen'      => 'zkteco_n8n',
                            'user_sn'     => (string)$userSn  // Guardamos el userSn original
                        ]
                    );

                    // Marcar fechas a recalcular considerando turnos nocturnos
                    $fechasAfectadas = $this->obtenerFechasAfectadas($empleado, $ts);
                    foreach ($fechasAfectadas as $fecha) {
                        $fechasAfectadasPorEmpleado[$empleado->id][$fecha] = true;
                    }

                    $m->wasRecentlyCreated ? $inserted++ : $updated++;
                    
                } catch (\Throwable $e) {
                    $errores[] = "UserSn {$userSn}: " . $e->getMessage();
                }
            }
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('sync_result', [
                'ok' => false,
                'step' => 'upsert',
                'msg' => 'Error en transacción: ' . $e->getMessage(),
            ]);
        }

        // 3) Generar asistencias por empleado/fecha ancla
        $totalAsis = 0;
        foreach ($fechasAfectadasPorEmpleado as $empleadoId => $mapFechas) {
            $emp = Empleado::find($empleadoId);
            if (!$emp) continue;

            // incluir fecha forzada si la pasaron
            if ($fechaForzada) $mapFechas[$fechaForzada] = true;

            foreach (array_keys($mapFechas) as $fechaStr) {
                // Validar que la fecha no sea anterior al límite
                if (Carbon::parse($fechaStr)->lt($limiteDesde)) continue;
                
                try {
                    $builder->buildParaFecha($emp, Carbon::parse($fechaStr));
                    $totalAsis++;
                } catch (\Throwable $e) {
                    $errores[] = "Emp {$empleadoId} {$fechaStr}: ".$e->getMessage();
                }
            }
        }

        $resultado = [
            'ok'       => true,
            'inserted' => $inserted,
            'updated'  => $updated,
            'skipped'  => $skipped,
            'asistencias_generadas' => $totalAsis,
            'errores'  => $errores,
        ];
        
        if (!empty($noEncontrados)) {
            $resultado['no_encontrados'] = array_keys($noEncontrados);
        }
        
        return back()->with('sync_result', $resultado);
    }

    /**
     * Normaliza el DNI agregando ceros a la izquierda si es necesario
     */
    private function normalizarDNI($userSn): string
    {
        $dni = (string)$userSn;
        // Si tiene menos de 8 dígitos, agregar ceros a la izquierda
        return str_pad($dni, 8, '0', STR_PAD_LEFT);
    }

    /**
     * Busca un empleado por DNI con múltiples estrategias
     */
    private function buscarEmpleadoPorDNI(string $dni)
    {
        // Estrategia 1: Buscar con el DNI normalizado (con ceros)
        $empleado = Empleado::where('dni', $dni)->first();
        
        if ($empleado) {
            return $empleado;
        }
        
        // Estrategia 2: Buscar sin ceros iniciales
        $dniSinCeros = ltrim($dni, '0');
        $empleado = Empleado::where('dni', $dniSinCeros)->first();
        
        if ($empleado) {
            return $empleado;
        }
        
        // Estrategia 3: Buscar con LIKE para manejar variaciones
        // Esto maneja casos donde el DNI en la BD tiene diferentes formatos
        return Empleado::whereRaw('LPAD(dni, 8, "0") = ?', [$dni])->first();
    }

    /**
     * Determina si una marcación es ingreso o salida considerando el horario del empleado
     */
    private function determinarTipoMarcacionConHorario($empleado, Carbon $timestamp): string
    {
        // Obtener el horario del empleado a través del cargo
        $horario = DB::table('horarios_detalle as hd')
            ->join('cargos as c', 'c.id', '=', 'hd.cargo_id')
            ->where('c.id', $empleado->cargo_id)
            ->select('hd.hora_ingreso', 'hd.hora_salida')
            ->first();

        if (!$horario) {
            // Si no hay horario definido, usar lógica simple
            return $this->determinarTipoMarcacionSimple($empleado->id, $timestamp);
        }

        // Parsear las horas del horario
        $horaIngreso = Carbon::parse($horario->hora_ingreso);
        $horaSalida = Carbon::parse($horario->hora_salida);
        
        // Determinar si es un turno nocturno (cruza días)
        $esTurnoNocturno = $horaSalida->lt($horaIngreso);
        
        // Obtener la hora de la marcación normalizada
        $horaMarcacion = $timestamp->copy()->setDate(2000, 1, 1);
        $horaIngresoRef = $horaIngreso->copy()->setDate(2000, 1, 1);
        $horaSalidaRef = $horaSalida->copy()->setDate(2000, 1, 1);
        
        // Para turnos nocturnos, ajustar referencias
        if ($esTurnoNocturno) {
            $horaSalidaRef->addDay();
            
            // Si la marcación es en la madrugada, ajustarla también
            if ($horaMarcacion->lt($horaIngresoRef)) {
                $horaMarcacion->addDay();
            }
        }
        
        // Calcular proximidad a ingreso y salida
        $diffIngreso = abs($horaMarcacion->diffInMinutes($horaIngresoRef));
        $diffSalida = abs($horaMarcacion->diffInMinutes($horaSalidaRef));
        
        // Buscar marcaciones previas considerando turnos nocturnos
        $inicioBusqueda = $timestamp->copy()->startOfDay();
        $finBusqueda = $timestamp;
        
        // Para turnos nocturnos en la madrugada, buscar desde el día anterior
        if ($esTurnoNocturno && $timestamp->hour < 12) {
            $inicioBusqueda->subDay();
        }
        
        $marcacionPrevia = Marcacion::where('empleado_id', $empleado->id)
            ->whereBetween('timestamp', [$inicioBusqueda, $finBusqueda])
            ->where('timestamp', '<', $timestamp)
            ->orderBy('timestamp', 'desc')
            ->first();
        
        // Si no hay marcación previa
        if (!$marcacionPrevia) {
            // Usar proximidad al horario para determinar
            return ($diffIngreso <= $diffSalida) ? 'INGRESO' : 'SALIDA';
        }
        
        // Si hay marcación previa, alternar
        if ($marcacionPrevia->tipo === 'INGRESO') {
            return 'SALIDA';
        } elseif ($marcacionPrevia->tipo === 'SALIDA') {
            return 'INGRESO';
        }
        
        // Si no tiene tipo definido, usar proximidad
        return ($diffIngreso <= $diffSalida) ? 'INGRESO' : 'SALIDA';
    }

    /**
     * Lógica simple cuando no hay horario definido
     */
    private function determinarTipoMarcacionSimple($empleadoId, Carbon $timestamp): string
    {
        $inicioDia = $timestamp->copy()->startOfDay();
        
        $marcacionesPrevias = Marcacion::where('empleado_id', $empleadoId)
            ->whereBetween('timestamp', [$inicioDia, $timestamp])
            ->where('timestamp', '<', $timestamp)
            ->orderBy('timestamp', 'asc')
            ->get();
        
        if ($marcacionesPrevias->isEmpty()) {
            return 'INGRESO';
        }
        
        $ultimaMarcacion = $marcacionesPrevias->last();
        
        if ($ultimaMarcacion->tipo === 'INGRESO') {
            return 'SALIDA';
        } elseif ($ultimaMarcacion->tipo === 'SALIDA') {
            return 'INGRESO';
        } else {
            // Usar lógica par/impar
            $cantidadPrevias = $marcacionesPrevias->count();
            return ($cantidadPrevias % 2 === 0) ? 'INGRESO' : 'SALIDA';
        }
    }

    /**
     * Obtiene las fechas que deben recalcularse considerando turnos nocturnos
     */
    private function obtenerFechasAfectadas($empleado, Carbon $timestamp): array
    {
        $fechas = [];
        
        // Siempre agregar la fecha del timestamp
        $fechas[] = $timestamp->copy()->startOfDay()->toDateString();
        
        // Obtener el horario del empleado
        $horario = DB::table('horarios_detalle as hd')
            ->join('cargos as c', 'c.id', '=', 'hd.cargo_id')
            ->where('c.id', $empleado->cargo_id)
            ->select('hd.hora_ingreso', 'hd.hora_salida')
            ->first();
        
        if ($horario) {
            $horaIngreso = Carbon::parse($horario->hora_ingreso);
            $horaSalida = Carbon::parse($horario->hora_salida);
            
            // Si es turno nocturno (cruza días)
            if ($horaSalida->lt($horaIngreso)) {
                // Marcación en la madrugada afecta el día anterior
                if ($timestamp->hour < 12) {
                    $fechas[] = $timestamp->copy()->subDay()->startOfDay()->toDateString();
                }
                // Marcación en la noche afecta el día siguiente
                if ($timestamp->hour >= 20) {
                    $fechas[] = $timestamp->copy()->addDay()->startOfDay()->toDateString();
                }
            }
        } else {
            // Sin horario, usar regla estándar para madrugadas
            if ($timestamp->hour < 6) {
                $fechas[] = $timestamp->copy()->subDay()->startOfDay()->toDateString();
            }
        }
        
        return array_unique($fechas);
    }
}