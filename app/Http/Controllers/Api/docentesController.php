<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Docentes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class docentesController extends Controller
{

    public function index(){

        $docente = Docentes::All();

        $data = [
            'docente' => $docente,
            'status' => 200
        ];


        return response()->json($data,200);
    }

    public function store(Request $request){

        $validator = Validator::make($request->all(), [
            'clave' => 'required',
            'nombre' => 'required'
        ]);

        if($validator->fails()) {
            $data = [
                'message' => 'Error en la validacion de los datos',
                'errors' => $validator->errors(),
                'status' => 400
            ];
            return response()->json($data, 400);
        }

        $docente = Docentes::create([
            'clave' => $request->clave,
            'nombre' => $request->nombre
        ]);

        if($validator->fails()) {
            $data = [
                'message' => 'Error al crear al estudiante',
                'status' => 500
            ];
            return response()->json($data, 500);
        }

        $data =[
            'docente' => $docente,
            'status' => 201
        ];

    }

    public function show($clave){

        $docente = Docentes::where('clave', $clave)->first();

        if(!$docente){
            $data =[
                'message' => 'Docente no encontrado',
                'status' => 404
            ];
            return response()->json($data,404);
        }

        $data =[
            'docente' => $docente,
            'status' => 200
        ];

        return response()->json($data, 200);

    }

    public function destroy($idDocente) {
        $docente = Docentes::find($idDocente);

        if(!$docente){
            $data =[
                'message' => 'Docente no encontrado',
                'status' => 404
            ];
            return response()->json($data,404);
        }

        $docente->delete();

        $data =[
            'docente' => $docente,
            'status' => 200
        ];

        return response()->json($data, 200);
    }

    public function update(Request $request, $idDocente){

        $docente = Docentes::find($idDocente);

        if(!$docente){
            $data =[
                'message' => 'Docente no encontrado',
                'status' => 404
            ];
            return response()->json($data,404);
        }

        $validator = Validator::make($request->all(), [
            'clave' => 'required',
            'nombre' => 'required'
        ]);

        if($validator->fails()) {
            $data = [
                'message' => 'Error en la validacion de los datos',
                'errors' => $validator->errors(),
                'status' => 400
            ];
            return response()->json($data, 400);
        }

        $docente->clave = $request->clave;
        $docente->nombre = $request->nombre;

        $docente->save();

        $data =[
            'message' => 'Docente actualizado',
            'docente' => $docente,
            'status' => 200
        ];

        return response()->json($data, 200);

    }

    public function updatePartial(Request $request, $idDocente){

        $docente = Docentes::find($idDocente);

        if(!$docente){
            $data = [
                'message' => 'Docente no encontrado',
                'status' => 404
            ];
            return response()->json($data, 404);
        }


        $validator = Validator::make($request->all(),[
            'clave' => 'digits_between:1,11',
            'nombre' => 'max:40'
        ]);

        if($validator->fails()) {
            $data = [
                'message' => 'Error en la validacion de los datos',
                'errors' => $validator->errors(),
                'status' => 400
            ];
            return response()->json($data, 400);
        }

        if($request->has('clave')){
            $docente->clave = $request->clave;
        }

        if($request->has('nombre')){
            $docente->nombre = $request->nombre;
        }

        $docente->save();

        $data = [
            'message' => 'Docente actualizado',
            'docente' => $docente,
            'status' => 200
        ];

        return response()->json($data, 200);
    }

}
