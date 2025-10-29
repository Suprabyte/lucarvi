<?php

namespace Database\Seeders;

use App\Models\Empleado;
use App\Models\Marcacion;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class MarcacionesSeeder extends Seeder
{
    public function run(): void
    {
        // Datos de ejemplo (puedes reemplazar por tu JSON completo)
        $json = [
            "data" => [
                ["userSn"=>58859,"deviceUserId"=>"45857758","recordTime_lima"=>"2025-09-25T07:31:15-05:00"],
                ["userSn"=>58862,"deviceUserId"=>"45857758","recordTime_lima"=>"2025-09-25T08:03:30-05:00"],
                ["userSn"=>58919,"deviceUserId"=>"45857758","recordTime_lima"=>"2025-09-25T11:06:06-05:00"],
                ["userSn"=>58863,"deviceUserId"=>"71017153","recordTime_lima"=>"2025-09-25T08:04:24-05:00"],
                ["userSn"=>58866,"deviceUserId"=>"71017153","recordTime_lima"=>"2025-09-25T08:16:49-05:00"],
                ["userSn"=>58890,"deviceUserId"=>"71389765","recordTime_lima"=>"2025-09-25T08:55:17-05:00"],
                ["userSn"=>58862,"deviceUserId"=>"71389765","recordTime_lima"=>"2025-09-25T17:12:44-05:00"],
            ],
        ];

        $marcaciones = $json['data'];

        DB::transaction(function () use ($marcaciones) {
            foreach ($marcaciones as $item) {
                $dni = str_pad($item['deviceUserId'], 8, '0', STR_PAD_LEFT);
                $fechaHora = Carbon::parse($item['recordTime_lima'])->timezone('America/Lima');

                // Buscar empleado existente
                $empleado = Empleado::where('dni', $dni)->first();

                Marcacion::create([
                    'empleado_id'   => $empleado?->id,
                    'timestamp'     => $fechaHora,
                    'tipo'          => $this->definirTipo($fechaHora),
                    'origen'        => 'zkteco',
                    'hash_seguridad'=> $this->generarHash($dni, $fechaHora),
                ]);
            }
        });
    }

    /**
     * Determina el tipo de marca según la hora del día
     */
    private function definirTipo(Carbon $ts): string
    {
        $h = (int) $ts->format('H');
        $m = (int) $ts->format('i');
        $mins = $h * 60 + $m;

        // < 12:00 => INGRESO
        if ($mins < 12 * 60) return 'INGRESO';

        // 12:00–13:00 => salida a refrigerio
        if ($mins >= 12 * 60 && $mins < 13 * 60) return 'REF_SALIDA';

        // 13:00–15:00 => retorno de refrigerio
        if ($mins >= 13 * 60 && $mins < 15 * 60) return 'REF_INGRESO';

        // >= 15:00 => SALIDA
        return 'SALIDA';
    }

    /**
     * Genera un hash único de seguridad
     */
    private function generarHash(string $dni, Carbon $ts): string
    {
        return hash('sha256', $dni . '|' . $ts->toDateTimeString() . '|' . Str::random(10));
    }
}
