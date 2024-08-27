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
        Schema::create('registrosaulas', function (Blueprint $table) {
            $table->id('idRegistro');
            $table->string('docente', 40);
            $table->string('aula', 10);
            $table->string('carrera', 15);
            $table->string('materia', 45);
            $table->integer('alumnos');
            $table->string('software', 255);
            $table->string('comentario', 75)->nullable();
            $table->time('registro');
            $table->year('year');
            $table->string('mes', 12);
            $table->integer('dia');
            $table->foreignId('idDocente')->constrained('docentes', 'idDocente');
            $table->foreignId('idAula')->constrained('aulas', 'idAula');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registrosaulas');
    }
};



