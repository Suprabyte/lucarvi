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
        Schema::create('empleados', function (Blueprint $table) {
    $table->id();
    $table->string('dni', 8)->unique();              // Validable con RENIEC
    $table->string('apellidos');
    $table->string('nombres');
    $table->date('fecha_nacimiento')->nullable();
    $table->string('direccion')->nullable();
    $table->string('celular', 20)->nullable();
    $table->string('email')->nullable();
    $table->enum('sexo', ['M','F'])->nullable();

    $table->enum('estado', ['ACTIVO','CESADO'])->default('ACTIVO');
    $table->date('fecha_ingreso')->nullable();
    $table->string('tipo_trabajador')->nullable();   // planilla, practicante, etc.
    $table->decimal('sueldo', 10, 2)->default(0);

    $table->foreignId('area_id')->nullable()->constrained()->nullOnDelete();
    $table->foreignId('cargo_id')->nullable()->constrained()->nullOnDelete();

    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empleados');
    }
};
