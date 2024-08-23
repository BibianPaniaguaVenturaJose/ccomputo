<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistroAulas extends Model
{
    use HasFactory;

    protected $table = 'registrosaulas';// Nombre de la tabla

    protected $primaryKey = 'idRegistro'; // Especifica el nombre de la clave primaria

    public $timestamps = false; // Desactiva los timestamps si no se usan

    protected $fillable = [
        'docente',
        'aula',
        'carrera',
        'materia',
        'alumnos',
        'software',
        'comentario',
        'registro',
        'year',
        'mes',
        'dia',
        'idDocente',
        'idAula'
    ];
}
