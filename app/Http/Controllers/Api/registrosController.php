<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Registros;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class registrosController extends Controller
{
    //
    public function index(){

        $registro = Registros::All();

        $data = [
            'registro' => $registro,
            'status' => 200
        ];


        return response()->json($data,200);
    }

    public function store(Request $request){
        // Establecer el locale a español
        Carbon::setLocale('es');

        // Validar la entrada
        $validator = Validator::make($request->all(), [
            'control' => 'required|string|max:9'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error en la validación de los datos',
                'errors' => $validator->errors(),
                'status' => 400
            ], 400);
        }

        // Obtener la fecha y hora actual
        $now = Carbon::now('America/Guatemala'); // Ajusta según tu ubicación, en este caso la zona horaria de Mexico pone +1 hr.
        $year = $now->year;
        $month = $now->locale('es')->monthName; // Nombre completo del mes en español
        $day = $now->day;
        $entrada = $now->format('H:i:s');
        $salida = $now->format('H:i:s'); // Aquí podrías ajustarlo si se necesita otro valor para 'salida'
        $duracion = $now->diff($now)->format('%H:%I:%S'); // Duración como '00:00:00' por defecto
        $maquina = 1; // Valor predeterminado para 'maquina'

        // Crear el registro
        $registro = Registros::create([
            'control' => $request->control,
            'year' => $year,
            'mes' => $month,
            'dia' => $day,
            'entrada' => $entrada,
            'salida' => $salida,
            'duracion' => $duracion,
            'maquina' => $maquina
        ]);

        return response()->json([
            'registro' => $registro,
            'status' => 201
        ], 201);

    }

    public function update(Request $request, $control) {

    $registro = Registros::where('control', $control)
                        ->orderBy('entrada', 'desc')
                        ->first();

    if (!$registro) {
        return response()->json([
            'message' => 'No se encontró ningún registro con el control proporcionado',
            'status' => 404
        ], 404);
    }

    if ($registro->duracion === '00:00:00') {
        $registro->salida = Carbon::now();

        if ($registro->entrada && $registro->salida) {
            $entrada = Carbon::parse($registro->entrada);
            $salida = Carbon::parse($registro->salida);
            $registro->duracion = $salida->diff($entrada)->format('%H:%I:%S');
        }

        $registro->save();

        return response()->json([
            'message' => 'Registro actualizado con éxito',
            'registro' => $registro,
            'status' => 200
        ], 200);
    } else {
        return response()->json([
            'message' => 'La duración ya ha sido actualizada previamente',
            'status' => 400
        ], 400);
    }
    }


}
