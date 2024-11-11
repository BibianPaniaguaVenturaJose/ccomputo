<?php

namespace App\Http\Controllers;


use Carbon\Traits\ToStringFormat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Aulas;
use App\Models\Carreras;
use App\Models\Materias;
use App\Models\Software;
use App\Models\RegistroAulas;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class HomeController extends Controller
{
    //
    public function index()
    {
        // Obtén el usuario autenticado
        $user = Auth::user();

        // Pasa el usuario a la vista
        return view('home.index', ['user' => $user]);
    }

    public function store(Request $request) {
        // Configura la zona horaria y la localización
        Carbon::setLocale('es');
        $now = Carbon::now('America/Guatemala');
        $registroHora = $now->format('H:i:s');
        $year = $now->year;
        $month = $now->locale('es')->monthName;
        $day = $now->day;

        // Recibe y procesa los datos del formulario
        $nombreDocente = $request->input('nombreDocente');
        $aulaId = (int) $request->input('aula');
        $carreraId = $request->input('carrera');
        $materiaId = $request->input('materia');
        $numAlumnos = (int) $request->input('numAlumnos');
        $comentario = $request->input('comentario', '');
        $softwareIds = $request->input('software');

        //dd($request->all());

        // Busca los nombres correspondientes en las tablas relacionadas
        $aulaNombre = Aulas::find($aulaId)->nombre;
        $carreraNombre = Carreras::find($carreraId)->carrera;
        $materiaNombre = Materias::find($materiaId)->nombre;
        $softwareNombres = Software::whereIn('idSoftware', $softwareIds)->pluck('nombre')->toArray();
        $softwareNombresJson = json_encode($softwareNombres);


        // Construye los datos que se enviarán al método create()
        $registroData = [
            'docente' => $nombreDocente,
            'aula' => $aulaNombre,
            'carrera' => $carreraNombre,
            'materia' => $materiaNombre,
            'alumnos' => $numAlumnos,
            'software' => $softwareNombresJson, // Guarda directamente el string JSON
            'comentario' => $comentario,
            'registro' => $registroHora,
            'year' => $year,
            'mes' => $month,
            'dia' => $day,
            'idDocente' => auth()->user()->idDocente,
            'idAula' => $aulaId
        ];

        // Imprime los valores antes de enviarlos a la base de datos
        // dd($registroData);

        try {
            // Intenta crear el registro en la base de datos
            $registro = RegistroAulas::create($registroData);
            //redirecciona al metodo logout
            return $this->logoutAndRedirect();
        } catch (\Exception $e) {
            Log::error('Error al guardar el registro: ' . $e->getMessage());
            return response()->json(['error' => 'Error interno del servidor'], 500);
        }
    }

    //cierre de sesion
    private function logoutAndRedirect(){
        Session::flush();
        Auth::logout();
        return redirect()->to('/login');
    }

    public function logout(){
        Session::flush();
        Auth::logout();
        return redirect()->to('/login');
    }

}


