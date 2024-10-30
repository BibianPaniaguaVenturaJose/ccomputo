<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Software;
use Illuminate\Http\Request;

class softwareController extends Controller
{
    public function index(){

        $software = Software::All();

        return response()->json($software);
    }

    public function showSoftware(){
        $softwareList = Software::all();
        return view('home.index', ['softwareList' => $softwareList]);
    }

    // Método para obtener el software de un aula específica
    public function getSoftwarePorAula($idAula)
    {
        // Obtener todo el software asociado a la aula seleccionada
        $software = Software::where('idAula', $idAula)->get();

        // Verificar si hay software para esa aula
        if ($software->isEmpty()) {
            return response()->json([
                'message' => 'No se encontró software para esta aula.'
            ], 404);
        }

        // Retornar el software en formato JSON
        return response()->json($software);
    }
}
