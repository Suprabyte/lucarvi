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
       Schema::create('marcacions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('empleado_id')->constrained()->cascadeOnDelete();
    $table->dateTime('timestamp');                 // marca bruta
    $table->enum('tipo', ['INGRESO','SALIDA','REFRIGIO_IN','REFRIGIO_OUT'])->nullable();
    $table->string('origen')->nullable();          // reloj, app, import
    $table->string('hash_seguridad')->nullable();  // para auditoría
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marcacions');
    }
};
