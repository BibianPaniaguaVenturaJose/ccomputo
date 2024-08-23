<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Docentes extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'Docentes';// Nombre de la tabla

    protected $primaryKey = 'idDocente'; // Especifica el nombre de la clave primaria

    public $timestamps = false; // Desactiva los timestamps si no se usan

    protected $fillable = [
        'clave',
        'nombre'
    ];

}
