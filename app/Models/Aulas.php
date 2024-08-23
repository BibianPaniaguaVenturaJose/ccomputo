<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aulas extends Model
{
    use HasFactory;

    protected $table = 'aulas';// Nombre de la tabla

    protected $primaryKey = 'idAula'; // Especifica el nombre de la clave primaria

    public $timestamps = false; // Desactiva los timestamps si no se usan

    protected $fillable = [
        'nombre'
    ];
}
