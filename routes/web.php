<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

// IMPORTA TUS CONTROLADORES
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ZktecoSyncController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// ZKTECO SYNC (POST protegido con auth)
Route::post('/admin/zkteco/sync', [ZktecoSyncController::class, 'sync'])
    ->middleware(['auth'])
    ->name('zkteco.sync');

// REPORTES (UN SOLO GRUPO)
Route::prefix('reportes')->name('reportes.')->middleware(['auth'])->group(function () {
    Route::get('/asistencia',      [ReportController::class, 'asistencia'])->name('asistencia');
    Route::get('/inasistencias',   [ReportController::class, 'inasistencias'])->name('inasistencias');
    Route::get('/tardanzas',       [ReportController::class, 'tardanzas'])->name('tardanzas');
    Route::get('/productividad',   [ReportController::class, 'productividad'])->name('productividad');
    Route::get('/valorizado',      [ReportController::class, 'valorizado'])->name('valorizado');
    Route::get('/consolidado',     [ReportController::class, 'consolidado'])->name('consolidado');
    Route::get('/marcas-diario',   [ReportController::class, 'marcasDiario'])->name('marcas_diario'); // opcional
});

require __DIR__.'/settings.php';
