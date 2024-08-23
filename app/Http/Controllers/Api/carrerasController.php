<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Carreras;
use Illuminate\Http\Request;

class carrerasController extends Controller
{
    //
    public function index(){

        $carrera = Carreras::All();

        return response()->json($carrera);
    }
}
