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
       Schema::create('horario_empleado', function (Blueprint $table) {
    $table->id();
    $table->foreignId('empleado_id')->constrained()->cascadeOnDelete();
    $table->foreignId('horario_id')->constrained()->cascadeOnDelete();
    $table->date('vigencia_desde')->nullable();
    $table->date('vigencia_hasta')->nullable(); // null = vigente
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('horario_empleados');
    }
};
