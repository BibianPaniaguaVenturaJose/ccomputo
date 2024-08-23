<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Aulas;
use Illuminate\Http\Request;

class aulasController extends Controller
{
    //
    public function index(){
        // Obtén todos los registros de la tabla 'aulas'
        $aula = Aulas::All();


        // Retorna los datos como JSON (para uso en una API) o los pasa a una vista
        return response()->json($aula);
    }
}
