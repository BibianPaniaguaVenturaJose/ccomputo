<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Carreras extends Model
{
    use HasFactory;

    protected $table = 'Carreras';// Nombre de la tabla

    protected $primaryKey = 'idCarrera'; // Especifica el nombre de la clave primaria

    public $timestamps = false; // Desactiva los timestamps si no se usan

    protected $fillable = [
        'carrera',
        'clave'
    ];

}
