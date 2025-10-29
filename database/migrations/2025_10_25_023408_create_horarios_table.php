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
        Schema::create('horarios', function (Blueprint $table) {
    $table->id();
    $table->string('nombre');                  // p.ej. "Administrativo - Diurno"
    $table->time('ingreso');                   // 09:00
    $table->time('salida');                    // 18:00
    $table->unsignedTinyInteger('tolerancia_min')->default(5); // política global: 5 min
    $table->time('refrigerio_inicio')->nullable(); // 13:00
    $table->time('refrigerio_fin')->nullable();    // 14:00
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('horarios');
    }
};
