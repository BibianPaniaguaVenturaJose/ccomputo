<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Carreras;
use App\Models\Materias;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class materiasController extends Controller
{

    public function index()
    {

        $materia = Materias::All();

        return response()->json($materia);
    }

    public function getMateriasPorDocente($claveDocente)
    {
        // Consulta las materias usando la clave del docente
        $materias = DB::table('materias')
            ->where('claveDocente', $claveDocente)
            ->get();

        return response()->json(['materias' => $materias]);
    }

    public function obtenerCarreraPorMateria($idMateria)
    {
        $materia = Materias::find($idMateria);

        if ($materia) {
            $carrera = Carreras::find($materia->idCarrera); // Obtener la carrera asociada
            return response()->json(['carrera' => $carrera ? $carrera->carrera : null, 'ID' => $carrera ? $carrera->idCarrera : null]);
        } else {
            return response()->json(['carrera' => null], 404);
        }
    }


}
