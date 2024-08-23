<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Carreras;
use App\Models\Materias;
use Illuminate\Http\Request;

class materiasController extends Controller
{

    public function index(){

        $materia = Materias::All();

        return response()->json($materia);
    }

    public function show($idCarrera) {

        // Obtener todas las materias que pertenecen a la carrera especificada
        $materias = Materias::where('idCarrera', $idCarrera)->get();

        if ($materias->isEmpty()) {
            $data = [
                'message' => 'Carrera no encontrada o sin materias',
                'status' => 404
            ];
            return response()->json($data, 404);
        }

        $data = [
            'materias' => $materias,
            'status' => 200
        ];

        return response()->json($data, 200);
    }

}
