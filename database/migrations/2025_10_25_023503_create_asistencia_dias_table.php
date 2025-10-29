<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('asistencia_dias', function (Blueprint $table) {
    $table->id();
    $table->foreignId('empleado_id')->constrained()->cascadeOnDelete();
    $table->date('fecha');
    // SP/SI/AUSENTE
    $table->enum('tipo_suspension', ['NINGUNA','SP','SI'])->default('NINGUNA');

    // marcas “normalizadas” del día
    $table->time('hora_ingreso')->nullable();
    $table->time('hora_salida_refrigerio')->nullable();
    $table->time('hora_retorno_refrigerio')->nullable();
    $table->time('hora_salida')->nullable();

    // cálculos
    $table->unsignedInteger('min_trabajados')->default(0);
    $table->unsignedInteger('min_tardanza')->default(0);
    $table->unsignedInteger('min_extra_25')->default(0);
    $table->unsignedInteger('min_extra_35')->default(0);
    $table->unsignedInteger('min_extra_100')->default(0);

    $table->boolean('bloqueado')->default(false);  // “guardado con clave”
    $table->string('bloqueo_hash')->nullable();

    $table->unique(['empleado_id','fecha']);
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asistencia_dias');
    }
};
