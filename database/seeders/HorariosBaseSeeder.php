<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\{Area, Cargo, HorarioDetalle};

class HorariosBaseSeeder extends Seeder
{
    public function run(): void
    {
        // Filas tomadas de tu Excel (puedes agregar/quitar)
        $rows = [
            // [area, cargo, ing, sal, ref_ing, ref_sal, obs]
            ['AVES VIVAS',       'ESTIBADOR',                 '0:00',  '9:00',  '6:00',  '7:30',  null],
            ['AVES VIVAS',       'ENC. DE DESPACHO VIVO',     '0:00',  '9:00',  '6:00',  '7:30',  null],
            ['AVES BENEFICIADO', 'OPERARIO DE PRODUCCIÓN',    '0:30',  '9:30',  '6:00',  '7:30',  null],
            ['AVES BENEFICIADO', 'LAVADOR DE BANDEJAS',       '9:00',  '18:00', '12:00', '13:00', null],
            ['AVES BENEFICIADO', 'AUXILIAR DE LIMPIEZA',      '6:00',  '15:00', '12:00', '13:00', null],
            ['AVES BENEFICIADO', 'OPERARIO DE CALDERO',       '22:00', '7:00',  '5:20',  '6:40',  'PERSONAL CON HORARIO ROTATIVO'],
            ['AVES BENEFICIADO', 'JEFE DE PRODUCCIÓN',        '0:30',  '9:30',  '7:00',  '8:00',  null],
            ['AVES VIVAS',       'FACTURADOR VIVO',           '23:30', '7:30',  '5:30',  '6:45',  'PERSONAL CON HORARIO ROTATIVO'],
            ['AVES BENEFICIADO', 'FACTURADOR BENEFICIADO',    '23:30', '7:30',  '5:30',  '6:45',  'PERSONAL CON HORARIO ROTATIVO'],
            ['AVES VIVAS',       'CH VIVO',                   '0:30',  '9:30',  '6:00',  '7:30',  null],
            ['AVES BENEFICIADO', 'CH BENEF',                  '1:00',  '10:00', '6:00',  '7:30',  null],

            ['COMERCIAL',        'INS.COBRANZA',              '9:00',  '6:00',  '12:00', '13:00', 'PERSONAL NO FISCALIZADO'],
            ['COMERCIAL',        'JEFE DE RESGUARDOS',        '9:00',  '6:00',  '12:00', '13:00', 'PERSONAL NO FISCALIZADO'],
            ['COMERCIAL',        'RESGUARDO',                 '9:00',  '6:00',  '12:00', '13:00', 'PERSONAL NO FISCALIZADO'],
            ['COMERCIAL',        'JEFE DE VENTAS',            '9:00',  '6:00',  '12:00', '13:00', 'PERSONAL NO FISCALIZADO'],
            ['COMERCIAL',        'VENDEDOR',                  '9:00',  '6:00',  '12:00', '13:00', 'PERSONAL NO FISCALIZADO'],

            ['ADMINISTRATIVO',   'VIGILANTE NOCHE',           '23:00', '11:00', '6:00',  '7:00',  'PERSONAL CON HORARIO ROTATIVO'],
            ['ADMINISTRATIVO',   'VIGILANTE DÍA',             '11:00', '23:00', '12:30', '13:30', 'PERSONAL CON HORARIO ROTATIVO'],

            ['MANTENIMIENTO',    'JEFE DE MANTENIMIENTO',     '9:00',  '18:00', '12:00', '13:00', null],
            ['MANTENIMIENTO',    'TECNICO',                    '9:00',  '18:00', '12:00', '13:00', 'PERSONAL CON HORARIO ROTATIVO'],

            ['ADMINISTRATIVO',   'ADMINISTRADOR',              '7:00',  '16:30', '12:30', '13:30', 'PERSONAL NO FISCALIZADO'],
            ['ADMINISTRATIVO',   'TITULAR GERENTE',            '7:00',  '16:30', '12:30', '13:30', 'PERSONAL NO FISCALIZADO'],
            ['AVES BENEFICIADO', 'ENC. CONTROL DE CALIDAD',    '0:00',  '9:00',  '6:00',  '7:00',  'PERSONAL NO FISCALIZADO'],
            ['ADMINISTRATIVO',   'ASIS. DE GERENCIA',          '9:00',  '18:00', '12:00', '13:00', 'PERSONAL NO FISCALIZADO'],
            ['ADMINISTRATIVO',   'LOGISTICA/ENC.TRANS',        '7:00',  '16:30', '12:30', '13:30', 'SABADOS : 07:00 AM A 12:30 PM'],
            ['ADMINISTRATIVO',   'ENCARGADA CONTABLE',         '7:00',  '16:30', '12:30', '13:30', 'SABADOS : 07:00 AM A 12:30 PM'],
            ['ADMINISTRATIVO',   'JEFA DE RRHH',               '7:00',  '16:30', '12:30', '13:30', 'SABADOS : 07:00 AM A 12:30 PM'],
        ];

        DB::transaction(function () use ($rows) {
            foreach ($rows as [$areaName, $cargoName, $ing, $sal, $rIng, $rSal, $obs]) {
                $area  = Area::firstOrCreate(['nombre' => trim($areaName)]);
                $cargo = Cargo::firstOrCreate(['nombre' => trim($cargoName)]);

                HorarioDetalle::updateOrCreate(
                    [
                        'area_id'  => $area->id,
                        'cargo_id' => $cargo->id,
                    ],
                    [
                        'hora_ingreso'       => self::toTime($ing),
                        'hora_salida'        => self::toTime($sal),
                        'refrigerio_ingreso' => self::toTime($rIng),
                        'refrigerio_salida'  => self::toTime($rSal),
                        'observacion'        => $obs ? Str::upper(trim($obs)) : null,
                    ]
                );
            }
        });
    }

    /**
     * Convierte "0:00" / "07:00" / "5:20" a "HH:MM:00" o null.
     */
    private static function toTime(?string $h): ?string
    {
        if (!$h) return null;

        $h = trim($h);
        // soporta "0:00", "7:00", "07:00", "23:00", "5:20", etc.
        if (!str_contains($h, ':')) {
            // "7" -> "7:00"
            $h .= ':00';
        }
        [$hh, $mm] = array_pad(explode(':', $h), 2, '00');

        $hh = str_pad((string) intval($hh), 2, '0', STR_PAD_LEFT);
        $mm = str_pad((string) intval($mm), 2, '0', STR_PAD_LEFT);

        return "{$hh}:{$mm}:00";
    }
}
