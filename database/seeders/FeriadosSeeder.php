<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FeriadosSeeder extends Seeder
{
    public function run(): void
    {
        // Solo feriados nacionales oficiales (no incluye "días no laborables" del sector público).
        // laborable = false  => día feriado (descanso)
        $feriados = [
            // ===== 2025 =====
            ['2025-01-01', 'Año Nuevo', false],
            ['2025-04-17', 'Jueves Santo', false],
            ['2025-04-18', 'Viernes Santo', false],
            ['2025-05-01', 'Día del Trabajo', false],
            ['2025-06-29', 'San Pedro y San Pablo', false],
            ['2025-07-28', 'Fiestas Patrias – Independencia', false],
            ['2025-07-29', 'Fiestas Patrias – Día complementario', false],
            ['2025-08-30', 'Santa Rosa de Lima', false],
            ['2025-10-08', 'Combate de Angamos', false],
            ['2025-11-01', 'Día de Todos los Santos', false],
            ['2025-12-08', 'Inmaculada Concepción', false],
            ['2025-12-09', 'Batalla de Ayacucho', false],
            ['2025-12-25', 'Navidad', false],

            // ===== 2026 =====
            ['2026-01-01', 'Año Nuevo', false],
            ['2026-04-02', 'Jueves Santo', false],
            ['2026-04-03', 'Viernes Santo', false],
            ['2026-05-01', 'Día del Trabajo', false],
            ['2026-06-29', 'San Pedro y San Pablo', false],
            ['2026-07-28', 'Fiestas Patrias – Independencia', false],
            ['2026-07-29', 'Fiestas Patrias – Día complementario', false],
            ['2026-08-30', 'Santa Rosa de Lima', false],
            ['2026-10-08', 'Combate de Angamos', false],
            ['2026-11-01', 'Día de Todos los Santos', false],
            ['2026-12-08', 'Inmaculada Concepción', false],
            ['2026-12-09', 'Batalla de Ayacucho', false],
            ['2026-12-25', 'Navidad', false],

            // ===== 2027 =====
            ['2027-01-01', 'Año Nuevo', false],
            ['2027-03-25', 'Jueves Santo', false],
            ['2027-03-26', 'Viernes Santo', false],
            ['2027-05-01', 'Día del Trabajo', false],
            ['2027-06-29', 'San Pedro y San Pablo', false],
            ['2027-07-28', 'Fiestas Patrias – Independencia', false],
            ['2027-07-29', 'Fiestas Patrias – Día complementario', false],
            ['2027-08-30', 'Santa Rosa de Lima', false],
            ['2027-10-08', 'Combate de Angamos', false],
            ['2027-11-01', 'Día de Todos los Santos', false],
            ['2027-12-08', 'Inmaculada Concepción', false],
            ['2027-12-09', 'Batalla de Ayacucho', false],
            ['2027-12-25', 'Navidad', false],
        ];

        foreach ($feriados as [$fecha, $descripcion, $laborable]) {
            DB::table('feriados')->updateOrInsert(
                ['fecha' => $fecha],
                [
                    'descripcion' => $descripcion,
                    'laborable'   => $laborable,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]
            );
        }
    }
}
