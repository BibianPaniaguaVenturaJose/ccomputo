<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\RegistroAulas;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class InformController extends Controller
{
    // muestra la vista de home
    public function show(){

        return view('inform.home');
    }

    //muestra la grafica de alumnos por aula es la view
    public function generarGraficaAlumnosXAula()
    {
        $datos = DB::table('registrosaulas')
            ->select('aula', DB::raw('SUM(alumnos) AS total_alumnos'))
            ->groupBy('aula')
            ->orderBy('aula')
            ->get();

        $labels = $datos->pluck('aula');  // Nombres de las aulas
        $data = $datos->pluck('total_alumnos');  // Cantidad de alumnos en cada aula

        return view('inform.inicio', [
            'labels' => $labels,
            'data' => $data
        ]);
    }

    //Se usa para filtrar los reportes
    public function filtrarPorFecha(Request $request)
    {
        // Validar las fechas de entrada
        $request->validate([
            'fechaInicio' => 'required|date',
            'fechaFin' => 'required|date',
        ]);

        // Obtener las fechas de inicio y fin del formulario
        $fechaInicio = Carbon::parse($request->input('fechaInicio'))->startOfDay();
        $fechaFin = Carbon::parse($request->input('fechaFin'))->endOfDay();

        // Obtener los registros filtrados por fecha
        $registros = RegistroAulas::all()->filter(function ($registro) use ($fechaInicio, $fechaFin) {
            $fechaCompleta = Carbon::createFromDate(
                $registro->year,
                $this->convertirMesACadena($registro->mes),
                $registro->dia
            );

            return $fechaCompleta->between($fechaInicio, $fechaFin, true);
        });

        // Agrupar los registros por aula y sumar el número de alumnos
        $datosAgrupados = $registros->groupBy('aula')->map(function ($grupo) {
            return $grupo->sum('alumnos');
        });

        // Ordenar los datos de forma alfabetica
        $datosOrdenados = $datosAgrupados->sortKeys();

        // Extraer las etiquetas y los datos para la gráfica
        $labels = $datosOrdenados->keys()->toArray();
        $data = $datosOrdenados->values()->toArray();

        // Retornar la vista con los datos ordenados
        return view('inform.inicio', compact('labels', 'data'));
    }

    //transforma el mes escrito a numero
    private function convertirMesACadena($mes)
    {
        $meses = [
            'enero' => '01',
            'febrero' => '02',
            'marzo' => '03',
            'abril' => '04',
            'mayo' => '05',
            'junio' => '06',
            'julio' => '07',
            'agosto' => '08',
            'septiembre' => '09',
            'octubre' => '10',
            'noviembre' => '11',
            'diciembre' => '12'
        ];

        return $meses[strtolower($mes)] ?? null;
    }

    //Muestra la view de los software usados
    public function generarGraficaSoftwareUsado()
    {

        // Subconsulta para descomponer el JSON de software y obtener también los alumnos
        $subqueries = [];

        for ($i = 0; $i <= 15; $i++) {
            $subqueries[] = DB::table('registrosaulas')
                ->select(
                    DB::raw("JSON_UNQUOTE(JSON_EXTRACT(software, '$[$i]')) AS software_item"),
                    'alumnos',
                    'carrera'
                )
                ->whereNotNull(DB::raw("JSON_UNQUOTE(JSON_EXTRACT(software, '$[$i]'))"));
        }

        // Combina todas las subconsultas en una sola usando unionAll
        $subquery = $subqueries[0];
        for ($i = 1; $i < count($subqueries); $i++) {
            $subquery = $subquery->unionAll($subqueries[$i]);
        }


        // Agrupar los software y sumar el total de alumnos que usaron cada uno
        $softwareCounts = DB::table(DB::raw("({$subquery->toSql()}) as sub"))
            ->select(
                'software_item',
                'carrera',
                DB::raw('GROUP_CONCAT(DISTINCT carrera ORDER BY carrera ASC) AS carreras_agrupadas'),
                DB::raw('SUM(alumnos) AS total_alumnos')
            )
            ->groupBy('software_item', 'carrera',)
            ->orderBy('software_item')
            ->get();

        // Calcular el total general de alumnos
        $totalGeneral = $softwareCounts->sum('total_alumnos');

        // Añadir la columna de porcentaje calculado
        foreach ($softwareCounts as $software) {
            $software->porcentaje = ($software->total_alumnos / $totalGeneral) * 100;
        }

        // Extraer las etiquetas y los datos para la gráfica
        $labels = $softwareCounts->pluck('software_item');
        $data = $softwareCounts->pluck('total_alumnos');
        $porcentajes = $softwareCounts->pluck('porcentaje'); // Obtener los porcentajes
        $carreras = $softwareCounts->pluck('carrera');

        // Añadir la fila final con el total general si es necesario (opcional)
        $labels->push('Total General');
        $data->push($totalGeneral);
        $porcentajes->push(100); // El total general sería el 100%

        // Retornar la vista con los datos obtenidos
        return view('inform.software', [
            'labels' => $labels,
            'data' => $data,
            'porcentajes' => $porcentajes,
            'carreras' => $carreras
        ]);
    }

    //Se usa para filtrar los reportes (en proceso)
    public function filtrarSoftware(Request $request)
    {
        // Validar las fechas de entrada
        $request->validate([
            'fechaInicio' => 'required|date',
            'fechaFin' => 'required|date',
        ]);

        // Obtener las fechas de inicio y fin del formulario
        $fechaInicio = Carbon::parse($request->input('fechaInicio'))->startOfDay();
        $fechaFin = Carbon::parse($request->input('fechaFin'))->endOfDay();

        // Convertir las fechas a formato que MySQL pueda comparar
        $fechaInicioStr = $fechaInicio->format('Y-m-d');
        $fechaFinStr = $fechaFin->format('Y-m-d');

        // Crear la primera subconsulta
        $subquery = DB::table('registrosaulas')
        ->select(
            DB::raw("JSON_UNQUOTE(JSON_EXTRACT(software, '$[0]')) AS software_item"),
            'alumnos',
            'carrera',
            DB::raw("CONCAT(year, '-', (CASE mes
            WHEN 'enero' THEN '01'
            WHEN 'febrero' THEN '02'
            WHEN 'marzo' THEN '03'
            WHEN 'abril' THEN '04'
            WHEN 'mayo' THEN '05'
            WHEN 'junio' THEN '06'
            WHEN 'julio' THEN '07'
            WHEN 'agosto' THEN '08'
            WHEN 'septiembre' THEN '09'
            WHEN 'octubre' THEN '10'
            WHEN 'noviembre' THEN '11'
            WHEN 'diciembre' THEN '12'
        END), '-', dia) AS fecha_completa")
        )
            ->whereNotNull(DB::raw("JSON_UNQUOTE(JSON_EXTRACT(software, '$[0]'))"))
            ->whereBetween(
                DB::raw("CONCAT(year, '-', (CASE mes
            WHEN 'enero' THEN '01'
            WHEN 'febrero' THEN '02'
            WHEN 'marzo' THEN '03'
            WHEN 'abril' THEN '04'
            WHEN 'mayo' THEN '05'
            WHEN 'junio' THEN '06'
            WHEN 'julio' THEN '07'
            WHEN 'agosto' THEN '08'
            WHEN 'septiembre' THEN '09'
            WHEN 'octubre' THEN '10'
            WHEN 'noviembre' THEN '11'
            WHEN 'diciembre' THEN '12'
        END), '-', dia)"),
                [$fechaInicioStr, $fechaFinStr]
            ); // Aquí finaliza la primera subconsulta

        // Agregar subconsultas para otros índices del JSON
        for ($i = 1; $i <= 16; $i++) {
            $subquery = $subquery->unionAll(
                DB::table('registrosaulas')
                ->select(
                    DB::raw("JSON_UNQUOTE(JSON_EXTRACT(software, '$[$i]')) AS software_item"),
                    'alumnos',
                    'carrera',
                    DB::raw("CONCAT(year, '-', (CASE mes
                    WHEN 'enero' THEN '01'
                    WHEN 'febrero' THEN '02'
                    WHEN 'marzo' THEN '03'
                    WHEN 'abril' THEN '04'
                    WHEN 'mayo' THEN '05'
                    WHEN 'junio' THEN '06'
                    WHEN 'julio' THEN '07'
                    WHEN 'agosto' THEN '08'
                    WHEN 'septiembre' THEN '09'
                    WHEN 'octubre' THEN '10'
                    WHEN 'noviembre' THEN '11'
                    WHEN 'diciembre' THEN '12'
                END), '-', dia) AS fecha_completa")
                )
                ->whereNotNull(DB::raw("JSON_UNQUOTE(JSON_EXTRACT(software, '$[$i]'))"))
                ->whereBetween(
                    DB::raw("CONCAT(year, '-', (CASE mes
                    WHEN 'enero' THEN '01'
                    WHEN 'febrero' THEN '02'
                    WHEN 'marzo' THEN '03'
                    WHEN 'abril' THEN '04'
                    WHEN 'mayo' THEN '05'
                    WHEN 'junio' THEN '06'
                    WHEN 'julio' THEN '07'
                    WHEN 'agosto' THEN '08'
                    WHEN 'septiembre' THEN '09'
                    WHEN 'octubre' THEN '10'
                    WHEN 'noviembre' THEN '11'
                    WHEN 'diciembre' THEN '12'
                END), '-', dia)"),
                    [$fechaInicioStr, $fechaFinStr]
                )
            ); // Aquí finaliza cada subconsulta en el ciclo
        }


        // Ejecutar la subconsulta combinada y agrupar los resultados
        $softwareCounts = DB::table(DB::raw("({$subquery->toSql()}) as sub"))
        ->mergeBindings($subquery) // No uses getQuery(), solo pasa la consulta original
        ->select(
            'software_item',
            'carrera',
            DB::raw('GROUP_CONCAT(DISTINCT carrera ORDER BY carrera ASC) AS carreras_agrupadas'),
            DB::raw('SUM(alumnos) AS total_alumnos')
        )
        ->groupBy('software_item', 'carrera')
        ->orderBy('software_item')
        ->get();


        // Calcular el total general de alumnos
        $totalGeneral = $softwareCounts->sum('total_alumnos');

        // Añadir la columna de porcentaje calculado
        foreach ($softwareCounts as $software) {
            $software->porcentaje = ($software->total_alumnos / $totalGeneral) * 100;
        }

        // Extraer las etiquetas y los datos para la gráfica
        $labels = $softwareCounts->pluck('software_item');
        $data = $softwareCounts->pluck('total_alumnos');
        $porcentajes = $softwareCounts->pluck('porcentaje'); // Obtener los porcentajes
        $carreras = $softwareCounts->pluck('carrera');

        // Añadir la fila final con el total general si es necesario (opcional)
        $labels->push('Total General');
        $data->push($totalGeneral);
        $porcentajes->push(100); // El total general sería el 100%

        // Retornar la vista con los datos obtenidos
        return view('inform.software', [
            'labels' => $labels,
            'data' => $data,
            'porcentajes' => $porcentajes,
            'carreras' => $carreras
        ]);
    }


    // Codigo para la seccion alumnos de reportes

    //muestra la grafica de alumnos por aula es la view base
    public function generarGraficaAlumnosXAulaXMes()
    {

        // Mapa de nombres de meses a números
        $mesesMap = [
            'enero' => 1,
            'febrero' => 2,
            'marzo' => 3,
            'abril' => 4,
            'mayo' => 5,
            'junio' => 6,
            'julio' => 7,
            'agosto' => 8,
            'septiembre' => 9,
            'octubre' => 10,
            'noviembre' => 11,
            'diciembre' => 12
        ];

        // Transforma los nombres de los meses en números en la base de datos
        $tablaDatos = DB::table('registrosaulas')
            ->select(
                'aula',
                'mes',
                DB::raw('SUM(alumnos) as total_alumnos')
            )
            ->groupBy('aula', 'mes')
            ->orderByRaw('FIELD(mes, ' . implode(', ', array_map(function ($mes) {
                return "'$mes'";
            }, array_keys($mesesMap))) . ')')
            ->orderBy('aula', 'asc')
            ->get();

        if ($tablaDatos->isEmpty()) {
            return redirect()->back()->withErrors('No se encontraron resultados en el rango de fechas seleccionado.');
        }

        // Organiza los datos para la gráfica y la tabla
        $labels = $tablaDatos->pluck('mes')->unique()->sort(function ($a, $b) use ($mesesMap) {
            return $mesesMap[$a] <=> $mesesMap[$b];
        })->values()->toArray();

        $datosAulas = [];
        $totalesPorMes = array_fill_keys($labels, 0);

        foreach ($tablaDatos as $registro) {
            $aula = $registro->aula;
            $mes = $registro->mes;
            $totalAlumnos = $registro->total_alumnos;

            if (!isset($datosAulas[$aula])) {
                $datosAulas[$aula] = array_fill_keys($labels, 0);
            }

            $datosAulas[$aula][$mes] = $totalAlumnos;
            $totalesPorMes[$mes] += $totalAlumnos;
        }

        // Añade la fila de totales generales
        $datosAulas['Total'] = $totalesPorMes;

        return view('inform.alumnos', [
            'tablaDatos' => $datosAulas,
            'labels' => $labels,
        ]);
    }

    //Filtrar reportes por meses y aulas
    public function filtrarFechaPorMes(Request $request)
    {
        $fechaInicio = $request->input('fechaInicio');
        $fechaFin = $request->input('fechaFin');

        // Verifica que las fechas se hayan proporcionado
        if (!$fechaInicio || !$fechaFin) {
            return redirect()->back()->withErrors('Por favor selecciona ambas fechas.');
        }


        // Transforma los nombres de los meses en números en la base de datos

        $tablaDatos = DB::table('registrosaulas')
            ->select('aula', 'mes', DB::raw('SUM(alumnos) as total_alumnos'))
            ->whereBetween(DB::raw("STR_TO_DATE(CONCAT(year, '-',
            CASE mes
                WHEN 'enero' THEN '01'
                WHEN 'febrero' THEN '02'
                WHEN 'marzo' THEN '03'
                WHEN 'abril' THEN '04'
                WHEN 'mayo' THEN '05'
                WHEN 'junio' THEN '06'
                WHEN 'julio' THEN '07'
                WHEN 'agosto' THEN '08'
                WHEN 'septiembre' THEN '09'
                WHEN 'octubre' THEN '10'
                WHEN 'noviembre' THEN '11'
                WHEN 'diciembre' THEN '12'
            END, '-', LPAD(dia, 2, '0')), '%Y-%m-%d')"), [$fechaInicio, $fechaFin])
            ->groupBy('aula', 'mes')
            ->orderByRaw("CASE mes
            WHEN 'enero' THEN 1
            WHEN 'febrero' THEN 2
            WHEN 'marzo' THEN 3
            WHEN 'abril' THEN 4
            WHEN 'mayo' THEN 5
            WHEN 'junio' THEN 6
            WHEN 'julio' THEN 7
            WHEN 'agosto' THEN 8
            WHEN 'septiembre' THEN 9
            WHEN 'octubre' THEN 10
            WHEN 'noviembre' THEN 11
            WHEN 'diciembre' THEN 12
        END")
            ->orderBy('aula', 'asc')
            ->get();

        if ($tablaDatos->isEmpty()) {
            return redirect()->back()->withErrors('No se encontraron resultados en el rango de fechas seleccionado.');
        }

        // Organiza los datos para la gráfica y la tabla
        $labels = $tablaDatos->pluck('mes')->unique()->values()->toArray(); // Meses como etiquetas $labels = is_array($labels) ? $labels : [$labels];
        $datosAulas = [];
        $totalesPorMes = array_fill_keys($labels, 0);

        foreach ($tablaDatos as $registro) {
            $aula = $registro->aula;
            $mes = $registro->mes;
            $totalAlumnos = $registro->total_alumnos;

            if (!isset($datosAulas[$aula])) {
                $datosAulas[$aula] = array_fill_keys($labels, 0);
            }

            $datosAulas[$aula][$mes] = $totalAlumnos;
            $totalesPorMes[$mes] += $totalAlumnos;
        }

        // Añade la fila de totales generales
        $datosAulas['Total'] = $totalesPorMes;

        return view('inform.alumnos', [
            'tablaDatos' => $datosAulas,
            'labels' => $labels,
        ]);
    }

    // Devuelve las vistas parciales para el dashboard
    // Aun no funciona correctamente
    // En tu controlador
    public function home()
    {
        return view('partials.inicio'); // Vista parcial
    }

    public function laboratorios()
    {
        return view('partials.laboratorios'); // Vista parcial
    }

    public function alumnos()
    {
        return view('partials.alumnos'); // Vista parcial
    }

    public function software()
    {
        return view('partials.software'); // Vista parcial
    }

}
