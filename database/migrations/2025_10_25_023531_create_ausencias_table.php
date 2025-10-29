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
       Schema::create('ausencias', function (Blueprint $table) {
    $table->id();
    $table->foreignId('empleado_id')->constrained()->cascadeOnDelete();
    $table->date('fecha');
    $table->enum('tipo', ['SP','SI','VACACIONES','DESCANSO_MEDICO','OTRO'])->default('OTRO');
    $table->text('motivo')->nullable();
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ausencias');
    }
};
