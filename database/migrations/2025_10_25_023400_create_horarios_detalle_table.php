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
        Schema::create('horarios_detalle', function (Blueprint $table) {
            $table->id();
            $table->foreignId('area_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('cargo_id')->nullable()->constrained()->nullOnDelete();

            $table->time('hora_ingreso')->nullable();
            $table->time('hora_salida')->nullable();

            $table->time('refrigerio_ingreso')->nullable();
            $table->time('refrigerio_salida')->nullable();

            $table->string('observacion', 255)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('horarios_detalle');
    }
};
