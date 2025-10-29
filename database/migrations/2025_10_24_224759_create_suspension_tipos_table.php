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
        Schema::create('suspension_tipos', function (Blueprint $table) {
            $table->id();
            $table->string('tipo', 2); // SP o SI
            $table->string('codigo', 5)->unique(); // ej. SA, HU, DM, FE
            $table->string('descripcion', 255);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suspension_tipos');
    }
};