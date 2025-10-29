<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SuspensionTiposSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('suspension_tipos')->insert([
            ['tipo' => 'SP', 'codigo' => 'SA', 'descripcion' => 'SANCION DISCIPLINARIA'],
            ['tipo' => 'SP', 'codigo' => 'HU', 'descripcion' => 'EJERCICIO DEL DERECHO DE HUELGA'],
            ['tipo' => 'SP', 'codigo' => 'DE', 'descripcion' => 'DETENCIÓN DEL TRABAJADOR, SALVO EL CASO DE CONDENA PRIVATIVA DE LA LIBERTAD'],
            ['tipo' => 'SP', 'codigo' => 'IN', 'descripcion' => 'INHABILITACIÓN ADMINISTRATIVA O JUDICIAL POR PERÍODO NO SUPERIOR A TRES MESES'],
            ['tipo' => 'SP', 'codigo' => 'PE', 'descripcion' => 'PERMISO O LICENCIA CONCEDIDOS POR EL EMPLEADOR SIN GOCE DE HABER'],
            ['tipo' => 'SP', 'codigo' => 'CF', 'descripcion' => 'CASO FORTUITO O FUERZA MAYOR'],
            ['tipo' => 'SP', 'codigo' => 'FI', 'descripcion' => 'FALTA NO JUSTIFICADA'],
            ['tipo' => 'SP', 'codigo' => 'TI', 'descripcion' => 'POR TEMPORADA O INTERMITENTE'],
            ['tipo' => 'SP', 'codigo' => 'DT', 'descripcion' => 'DETENCIÓN DEL TRABAJO'],

            ['tipo' => 'SI', 'codigo' => 'C', 'descripcion' => 'CESADO'],
            ['tipo' => 'SI', 'codigo' => 'D', 'descripcion' => 'DESCANSO SEMANAL'],

            ['tipo' => 'SI', 'codigo' => 'DM', 'descripcion' => 'ENFERMEDAD O ACCIDENTE (PRIMEROS VEINTE DÍAS)'],
            ['tipo' => 'SI', 'codigo' => 'IT', 'descripcion' => 'INCAPACIDAD TEMPORAL (INVALIDEZ, ENFERMEDAD Y ACCIDENTES)'],
            ['tipo' => 'SI', 'codigo' => 'MA', 'descripcion' => 'MATERNIDAD DURANTE EL DESCANSO PRE Y POST NATAL'],
            ['tipo' => 'SI', 'codigo' => 'DV', 'descripcion' => 'DESCANSO VACACIONAL'],
            ['tipo' => 'SI', 'codigo' => 'LI', 'descripcion' => 'LICENCIA PARA DESEMPEÑAR CARGO CÍVICO Y SERVICIO MILITAR OBLIGATORIO'],
            ['tipo' => 'SI', 'codigo' => 'CS', 'descripcion' => 'PERMISO Y LICENCIA POR EL DESEMPEÑO DE CARGOS SINDICALES'],
            ['tipo' => 'SI', 'codigo' => 'GH', 'descripcion' => 'LICENCIA CON GOCE DE HABER'],
            ['tipo' => 'SI', 'codigo' => 'ST', 'descripcion' => 'DÍAS COMPENSADOS POR HORAS TRABAJADAS EN SOBRETIEMPO'],
            ['tipo' => 'SI', 'codigo' => 'LP', 'descripcion' => 'LICENCIA POR PATERNIDAD'],
            ['tipo' => 'SI', 'codigo' => 'LU', 'descripcion' => 'LICENCIA POR LUTO'],
            ['tipo' => 'SI', 'codigo' => 'FG', 'descripcion' => 'LICENCIA A TRABAJADORES CON FAMILIARES CON ENFERMEDADES GRAVES O TERMINALES'],
            ['tipo' => 'SI', 'codigo' => 'ES', 'descripcion' => 'DÍAS SUBSIDIADOS POR ESSALUD (MÁS DE 21 DÍAS)'],
            ['tipo' => 'SI', 'codigo' => 'FE', 'descripcion' => 'FERIADO'],
            ['tipo' => 'SI', 'codigo' => 'FT', 'descripcion' => 'SIEMPRE Y CUANDO TRABAJEN EN FERIADOS'],
        ]);
    }
}
