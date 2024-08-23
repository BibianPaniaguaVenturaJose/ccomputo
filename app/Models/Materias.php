<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Materias extends Model
{
    use HasFactory;

    protected $table = 'Materias';// Nombre de la tabla

    protected $primaryKey = 'idMateria'; // Especifica el nombre de la clave primaria

    public $timestamps = false; // Desactiva los timestamps si no se usan

    protected $fillable = [
        'idMateria',
        'nombre',
        'idCarrera'
    ];

}
