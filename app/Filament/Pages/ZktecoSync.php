<?php

namespace App\Filament\Pages;

use App\Models\Empleado;
use App\Models\Marcacion;
use App\Services\AsistenciaBuilder;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class ZktecoSync extends Page
{
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-arrow-path';
    protected static ?string $navigationLabel = 'Sincronizar ZKTeco (n8n)';
    protected static ?string $title = 'Sincronizar ZKTeco (n8n)';
    protected static ?int $navigationSort = 90;

    protected string $view = 'filament.pages.zkteco-sync';

    public ?array $lastResult = null;
    public array $recentMarks = [];

    protected function getHeaderActions(): array
    {
        // ... código de acciones sin cambios ...
        return [
            // Todas las acciones existentes se mantienen igual
            Action::make('sync')
                ->label('Sincronizar ahora')
                ->icon('heroicon-o-arrow-path')
                ->color('primary')
                ->form([
                    TextInput::make('webhook_url')
                        ->label('Webhook URL')
                        ->default('http://localhost:3120/zkteco/logs')
                        ->required()
                        ->url(),
                    DatePicker::make('fecha')->label('Fecha base (opcional)'),
                    TextInput::make('dias')->label('Días hacia atrás')->numeric()->default(2)->minValue(1)->maxValue(30),
                ])
                ->requiresConfirmation()
                ->action(function (array $data) {
                    $this->runSync(
                        $data['webhook_url'] ?? 'http://localhost:3120/zkteco/logs',
                        $data['fecha'] ?? null,
                        isset($data['dias']) ? (int) $data['dias'] : 2,
                    );
                }),
            // ... resto de acciones ...
        ];
    }

    protected function runSync(string $url, ?string $fechaForzada, int $dias): void
    {
        @set_time_limit(180);
        @ini_set('max_execution_time', '180');

        try {
            $resp = Http::acceptJson()
                ->connectTimeout(8)
                ->timeout(25)
                ->retry(2, 1500, throw: false)
                ->get($url);

            if (! $resp->ok()) {
                $this->lastResult = [
                    'ok' => false,
                    'status' => $resp->status(),
                    'message' => 'No se pudo obtener datos del webhook',
                ];
                Notification::make()
                    ->title('No se pudo obtener datos del webhook')
                    ->body('HTTP '.$resp->status().' - '.$resp->reason())
                    ->danger()->send();
                return;
            }

            $data = $resp->json('data');
            if (! is_array($data)) {
                $this->lastResult = ['ok' => false, 'message' => 'El webhook no devolvió data[]'];
                Notification::make()->title('Payload inválido')->body('El webhook no devolvió data[]')->danger()->send();
                return;
            }

            $limiteDesde = Carbon::now('America/Lima')->startOfDay()->subDays(max(0, $dias));

            $inserted = 0; $updated = 0; $skipped = 0; $errores = [];
            $fechasAfectadasPorEmpleado = [];
            $noEncontrados = [];

            DB::beginTransaction();
            try {
                foreach ($data as $row) {
                    try {
                        // CAMBIO IMPORTANTE: Ahora usamos userSn como DNI
                        // ya que deviceUserId viene vacío
                        $userSn = $row['userSn'] ?? null;
                        $tsRaw = $row['recordTime_lima'] ?? null;
                        
                        if (! $userSn || ! $tsRaw) { 
                            $skipped++; 
                            continue; 
                        }

                        // Convertir userSn a DNI con padding de ceros si es necesario
                        $dni = $this->normalizarDNI($userSn);
                        
                        // Buscar empleado por DNI (intentar con y sin ceros)
                        $empleado = $this->buscarEmpleadoPorDNI($dni);
                        
                        if (! $empleado) { 
                            $noEncontrados[$userSn] = true;
                            $skipped++; 
                            continue; 
                        }

                        $ts = Carbon::parse($tsRaw);

                        if ($ts->lt($limiteDesde)) { 
                            $skipped++; 
                            continue; 
                        }

                        // Determinar tipo de marcación considerando horarios del empleado
                        $tipo = $this->determinarTipoMarcacionConHorario($empleado, $ts);

                        // Hash único usando empleado_id + timestamp exacto
                        $hashUnico = md5($empleado->id . '_' . $ts->format('Y-m-d H:i:s'));

                        $m = Marcacion::updateOrCreate(
                            ['hash_seguridad' => $hashUnico],
                            [
                                'empleado_id' => $empleado->id, 
                                'timestamp' => $ts, 
                                'tipo' => $tipo,
                                'origen' => 'zkteco_n8n',
                                'user_sn' => (string)$userSn  // Guardamos el userSn original
                            ]
                        );

                        // Marcar fechas afectadas considerando turnos nocturnos
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
                $this->lastResult = ['ok' => false, 'message' => 'Error al guardar marcaciones', 'error' => $e->getMessage()];
                Notification::make()->title('Error al guardar marcaciones')->body($e->getMessage())->danger()->send();
                return;
            }

            // Generar asistencias
            $builder = app(AsistenciaBuilder::class);
            $totalAsis = 0;

            foreach ($fechasAfectadasPorEmpleado as $empleadoId => $mapFechas) {
                $emp = Empleado::find($empleadoId);
                if (! $emp) continue;

                if ($fechaForzada) $mapFechas[$fechaForzada] = true;

                foreach (array_keys($mapFechas) as $fechaStr) {
                    if (Carbon::parse($fechaStr)->lt($limiteDesde)) continue;

                    try {
                        $builder->buildParaFecha($emp, Carbon::parse($fechaStr));
                        $totalAsis++;
                    } catch (\Throwable $e) {
                        $errores[] = "Emp {$empleadoId} {$fechaStr}: ".$e->getMessage();
                    }
                }
            }

            // Obtener marcaciones recientes
            $recent = Marcacion::with('empleado:id,nombres,apellidos,dni')
                ->where('origen', 'zkteco_n8n')
                ->orderByDesc('updated_at')
                ->limit(50)
                ->get()
                ->map(function (Marcacion $m) {
                    $ts = $m->timestamp;
                    $fmt = $ts instanceof \DateTimeInterface
                        ? $ts->format('Y-m-d H:i:s')
                        : \Carbon\Carbon::parse((string) $ts)->format('Y-m-d H:i:s');

                    return [
                        'empleado'  => optional($m->empleado)->apellidos.' '.optional($m->empleado)->nombres,
                        'dni'       => optional($m->empleado)->dni,
                        'timestamp' => $fmt,
                        'tipo'      => $m->tipo,
                        'origen'    => $m->origen,
                        'hash'      => $m->hash_seguridad,
                    ];
                })->all();

            $this->recentMarks = $recent;
            
            // Preparar mensaje de resultado
            $body = "Insertadas: {$inserted}\nActualizadas: {$updated}\nOmitidas: {$skipped}\nAsistencias: {$totalAsis}";
            
            if (!empty($noEncontrados)) {
                $dniList = implode(', ', array_keys($noEncontrados));
                $body .= "\n\nDNIs no encontrados: {$dniList}";
            }
            
            if ($errores) {
                $body .= "\n\nErrores:\n- " . implode("\n- ", array_slice($errores, 0, 10));
            }

            $this->lastResult = [
                'ok'        => true,
                'inserted'  => $inserted,
                'updated'   => $updated,
                'skipped'   => $skipped,
                'asistencias_generadas' => $totalAsis,
                'no_encontrados' => array_keys($noEncontrados),
                'errores'   => $errores,
            ];

            Notification::make()->title('Sincronización completada')->body($body)->success()->send();
            $this->dispatch('$refresh');

        } catch (ConnectionException $e) {
            $this->lastResult = ['ok' => false, 'message' => $e->getMessage()];
            Notification::make()->title('Timeout al llamar al webhook')->body($e->getMessage())->danger()->send();
        } catch (\Throwable $e) {
            $this->lastResult = ['ok' => false, 'message' => $e->getMessage()];
            Notification::make()->title('Fallo inesperado')->body($e->getMessage())->danger()->send();
        }
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
     * Busca un empleado por DNI, intentando con y sin ceros
     */
    private function buscarEmpleadoPorDNI(string $dni)
    {
        // Primero buscar con el DNI normalizado (con ceros)
        $empleado = Empleado::where('dni', $dni)->first();
        
        if ($empleado) {
            return $empleado;
        }
        
        // Si no encuentra, buscar sin ceros iniciales
        $dniSinCeros = ltrim($dni, '0');
        $empleado = Empleado::where('dni', $dniSinCeros)->first();
        
        if ($empleado) {
            return $empleado;
        }
        
        // Última opción: buscar con LIKE para manejar cualquier variación
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
            // Si no hay horario, usar la lógica simple
            return $this->determinarTipoMarcacionSimple($empleado->id, $timestamp);
        }

        // Parsear las horas del horario
        $horaIngreso = Carbon::parse($horario->hora_ingreso);
        $horaSalida = Carbon::parse($horario->hora_salida);
        
        // Determinar si es un turno nocturno (cruza días)
        $esTurnoNocturno = $horaSalida->lt($horaIngreso);
        
        // Obtener la hora de la marcación (sin fecha)
        $horaMarcacion = $timestamp->copy()->setDate(2000, 1, 1);
        $horaIngresoRef = $horaIngreso->copy()->setDate(2000, 1, 1);
        $horaSalidaRef = $horaSalida->copy()->setDate(2000, 1, 1);
        
        // Para turnos nocturnos, ajustar la hora de salida al día siguiente
        if ($esTurnoNocturno) {
            $horaSalidaRef->addDay();
            
            // Si la marcación es en la madrugada, ajustarla también
            if ($horaMarcacion->lt($horaIngresoRef)) {
                $horaMarcacion->addDay();
            }
        }
        
        // Calcular las diferencias con las horas de ingreso y salida
        $diffIngreso = abs($horaMarcacion->diffInMinutes($horaIngresoRef));
        $diffSalida = abs($horaMarcacion->diffInMinutes($horaSalidaRef));
        
        // Buscar marcaciones previas del día
        $inicioBusqueda = $timestamp->copy()->startOfDay();
        $finBusqueda = $timestamp;
        
        // Para turnos nocturnos, expandir el rango de búsqueda
        if ($esTurnoNocturno && $timestamp->hour < 12) {
            $inicioBusqueda->subDay();
        }
        
        $marcacionesPrevias = Marcacion::where('empleado_id', $empleado->id)
            ->whereBetween('timestamp', [$inicioBusqueda, $finBusqueda])
            ->where('timestamp', '<', $timestamp)
            ->orderBy('timestamp', 'desc')
            ->first();
        
        // Si no hay marcaciones previas
        if (!$marcacionesPrevias) {
            // Es más probable que sea INGRESO si está cerca de la hora de ingreso
            return ($diffIngreso <= $diffSalida) ? 'INGRESO' : 'SALIDA';
        }
        
        // Si hay marcación previa, alternar
        if ($marcacionesPrevias->tipo === 'INGRESO') {
            return 'SALIDA';
        } elseif ($marcacionesPrevias->tipo === 'SALIDA') {
            return 'INGRESO';
        }
        
        // Si la marcación previa no tiene tipo, usar proximidad al horario
        return ($diffIngreso <= $diffSalida) ? 'INGRESO' : 'SALIDA';
    }

    /**
     * Lógica simple de determinación de tipo cuando no hay horario
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
                // Si la marcación es en la madrugada, también afectar el día anterior
                if ($timestamp->hour < 12) {
                    $fechas[] = $timestamp->copy()->subDay()->startOfDay()->toDateString();
                }
                // Si la marcación es en la noche, también afectar el día siguiente
                if ($timestamp->hour >= 20) {
                    $fechas[] = $timestamp->copy()->addDay()->startOfDay()->toDateString();
                }
            }
        } else {
            // Sin horario específico, usar lógica estándar para madrugadas
            if ($timestamp->hour < 6) {
                $fechas[] = $timestamp->copy()->subDay()->startOfDay()->toDateString();
            }
        }
        
        return array_unique($fechas);
    }

    private function openReport(string $routeName, array $params = []): void
    {
        $url = route($routeName, array_filter($params));
        $this->dispatch('open-popup', url: $url);
    }
}