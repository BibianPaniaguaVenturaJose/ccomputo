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
        Schema::create('registros', function (Blueprint $table) {
            $table->id('idRegistros');
            $table->string('control', 9);
            $table->year('year');
            $table->string('mes', 12);
            $table->integer('dia');
            $table->time('entrada');
            $table->time('salida');
            $table->time('duracion');
            $table->integer('maquina');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registros');
    }
};
