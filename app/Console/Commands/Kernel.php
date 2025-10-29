<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        \App\Console\Commands\GenerarAsistenciaCommand::class,
    ];

    protected function schedule(Schedule $schedule): void
    {
        // ejemplo: correr cada 10 min
        // $schedule->command('asistencia:generar --dias=2')->everyTenMinutes();
    }
}
