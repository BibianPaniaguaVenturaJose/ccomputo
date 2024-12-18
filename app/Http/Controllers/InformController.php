<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\RegistroAulas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InformController extends Controller
{
    // Muestra la vista de home
    public function show()
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
            ->groupBy('software_item', 'carrera', )
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

        // +++++++++++++++  Codigo para AlumnosXAulaXMes
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
        $labelsAulas = $tablaDatos->pluck('mes')->unique()->sort(function ($a, $b) use ($mesesMap) {
            return $mesesMap[$a] <=> $mesesMap[$b];
        })->values()->toArray();

        $datosAulas = [];
        $totalesPorMes = array_fill_keys($labelsAulas, 0);

        foreach ($tablaDatos as $registro) {
            $aula = $registro->aula;
            $mes = $registro->mes;
            $totalAlumnos = $registro->total_alumnos;

            if (!isset($datosAulas[$aula])) {
                $datosAulas[$aula] = array_fill_keys($labelsAulas, 0);
            }

            $datosAulas[$aula][$mes] = $totalAlumnos;
            $totalesPorMes[$mes] += $totalAlumnos;
        }

        // Añade la fila de totales generales
        $datosAulas['Total'] = $totalesPorMes;

        // Retornar la vista con los datos obtenidos a la vista principal
        return view('inform.home', [
            'labels' => $labels,
            'data' => $data,
            'porcentajes' => $porcentajes,
            'carreras' => $carreras,
            'tablaDatos' => $datosAulas,
            'labelsAulas' => $labelsAulas,
        ]);

    }

    public function filtrarDatos(Request $request)
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

        // Subconsulta para el filtrado de software
        $subqueries = [];
        for ($i = 0; $i <= 15; $i++) {
            $subqueries[] = DB::table('registrosaulas')
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
                );
        }

        // Combina todas las subconsultas en una sola usando unionAll
        $subquery = $subqueries[0];
        for ($i = 1; $i < count($subqueries); $i++) {
            $subquery = $subquery->unionAll($subqueries[$i]);
        }

        // Agrupar los software y sumar el total de alumnos que usaron cada uno
        $softwareCounts = DB::table(DB::raw("({$subquery->toSql()}) as sub"))
            ->mergeBindings($subquery)
            ->select(
                'software_item',
                'carrera',
                DB::raw('GROUP_CONCAT(DISTINCT carrera ORDER BY carrera ASC) AS carreras_agrupadas'),
                DB::raw('SUM(alumnos) AS total_alumnos')
            )
            ->groupBy('software_item', 'carrera')
            ->orderBy('software_item')
            ->get();

        // Calcular el total general de alumnos para el software
        $totalGeneralSoftware = $softwareCounts->sum('total_alumnos');

        // Añadir la columna de porcentaje calculado
        foreach ($softwareCounts as $software) {
            $software->porcentaje = ($software->total_alumnos / $totalGeneralSoftware) * 100;
        }

        // Extraer las etiquetas y los datos para la gráfica de software
        $labelsSoftware = $softwareCounts->pluck('software_item');
        $dataSoftware = $softwareCounts->pluck('total_alumnos');
        $porcentajesSoftware = $softwareCounts->pluck('porcentaje');
        $carrerasSoftware = $softwareCounts->pluck('carrera');

        // Añadir la fila final con el total general
        $labelsSoftware->push('Total General');
        $dataSoftware->push($totalGeneralSoftware);
        $porcentajesSoftware->push(100); // El total general sería el 100%

        // Filtrado de alumnos por aula y mes
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

        // Organizar los datos para la gráfica y tabla de alumnos
        $labelsAlumnos = $tablaDatos->pluck('mes')->unique()->values()->toArray();
        $datosAulas = [];
        $totalesPorMes = array_fill_keys($labelsAlumnos, 0);

        foreach ($tablaDatos as $registro) {
            $aula = $registro->aula;
            $mes = $registro->mes;
            $totalAlumnos = $registro->total_alumnos;

            if (!isset($datosAulas[$aula])) {
                $datosAulas[$aula] = array_fill_keys($labelsAlumnos, 0);
            }

            $datosAulas[$aula][$mes] = $totalAlumnos;
            $totalesPorMes[$mes] += $totalAlumnos;
        }

        // Añadir la fila de totales generales de alumnos
        $datosAulas['Total'] = $totalesPorMes;

        // Retornar la vista con los datos combinados
        return view('inform.home', [
            'labels' => $labelsSoftware,
            'data' => $dataSoftware,
            'porcentajes' => $porcentajesSoftware,
            'carreras' => $carrerasSoftware,
            'tablaDatos' => $datosAulas,
            'labelsAulas' => $labelsAlumnos,
        ]);
    }

    //Muestra la vista de inicio
    public function cargar()
    {
        // Obtener registros agrupados por año y mes
        $registros = DB::table('registrosaulas')
            ->select('year', 'mes', DB::raw('COUNT(*) as cantidad'))
            ->groupBy('year', 'mes')
            ->orderBy('year')
            ->orderByRaw("FIELD(mes, 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre')")
            ->get();

        // Crear un array para mapear los nombres de los meses a sus números
        $mesesNumericos = [
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

        // Preparar los datos para la gráfica
        $labels = $registros->map(function ($registro) use ($mesesNumericos) {
            // Obtener el número del mes usando el array
            $mesNumerico = $mesesNumericos[$registro->mes] ?? '00'; // Si no se encuentra, usa '00'
            return $registro->year . '-' . $mesNumerico; // Formato: año-mes
        });



        // Obtener días hábiles para cada mes presente en los registros
        $diasHabilesPorMes = [];

        $inicioYear = date('Y') . '-01-01';
        $finYear = date('Y') . '-12-31';

        // Define la consulta SQL
        $sql = "
        WITH fechas AS (
            SELECT DATE_ADD('$inicioYear', INTERVAL n DAY) AS fecha
                FROM (
            SELECT a.N + b.N * 10 + c.N * 100 AS n
                FROM (SELECT 0 AS N UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4
                    UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) a
                CROSS JOIN (SELECT 0 AS N UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4
                UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) b
                CROSS JOIN (SELECT 0 AS N UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4
                    UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) c
            ) n
            WHERE DATE_ADD('$inicioYear', INTERVAL n DAY) <= '$finYear'
            )
        SELECT
            YEAR(fecha) AS year,
                MONTH(fecha) AS month,
            COUNT(*) AS total_dias_habiles
        FROM fechas
        WHERE DAYOFWEEK(fecha) NOT IN (1, 7)  -- Excluir domingos y sábados
        GROUP BY YEAR(fecha), MONTH(fecha)
        ORDER BY year, month;
        ";

        // Ejecuta la consulta con DB::select
        $diasHabilesPorMes = DB::select($sql);

        // Transformar diasHabilesPorMes a un array indexado por año y mes
        $diasHabilesPorMesArray = [];
        foreach ($diasHabilesPorMes as $dia) {
            $key = $dia->year . '-' . str_pad($dia->month, 2, '0', STR_PAD_LEFT);
            $diasHabilesPorMesArray[$key] = $dia->total_dias_habiles * 65;
        }


        // Obtener los datos para la gráfica
        $data = $registros->pluck('cantidad');

        return view('inform.inicio', compact('labels', 'data', 'diasHabilesPorMesArray'));
    }

    public function filtrarPorFecha(Request $request)
    {
        // Validación de las fechas de entrada
        $request->validate([
            'fechaInicio' => 'required|date',
            'fechaFin' => 'required|date',
        ]);

        // Fechas de inicio y fin para el filtro
        $fechaInicio = Carbon::parse($request->input('fechaInicio'))->startOfDay();
        $fechaFin = Carbon::parse($request->input('fechaFin'))->endOfDay();

        // Consulta con el filtro por fechas
        $registros = DB::table('registrosaulas')
        ->select('year', 'mes', DB::raw('COUNT(*) as cantidad'))
        ->whereRaw("CONCAT(year, '-',
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
                END, '-',
                LPAD(dia, 2, '0')
            ) BETWEEN ? AND ?", [$fechaInicio, $fechaFin])
        ->groupBy('year', 'mes')
        ->orderBy('year')
        ->orderByRaw("FIELD(mes, 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre')")
        ->get();

    // Aquí puedes procesar $registros y pasar los datos a la vista
    // Por ejemplo:
    $labels = $registros->map(function ($registro) {
       return $registro->year . '-' . str_pad(array_search($registro->mes, ['enero' => '01', 'febrero' => '02',
    'marzo' => '03', 'abril' => '04', 'mayo' => '05', 'junio' => '06', 'julio' => '07', 'agosto' => '08', 'septiembre' => '09',
     'octubre' => '10', 'noviembre' => '11', 'diciembre' => '12']), 2, '0', STR_PAD_LEFT);
    });


        //dd($registros);

        // Crear un array para mapear los nombres de los meses a sus números
        $mesesNumericos = [
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

        // Preparar los datos para la gráfica
        $labels = $registros->map(function ($registro) use ($mesesNumericos) {
            $mesNumerico = $mesesNumericos[$registro->mes] ?? '00'; // Si no se encuentra, usa '00'
            return $registro->year . '-' . $mesNumerico; // Formato: año-mes
        });

        // Obtener días hábiles para cada mes presente en los registros filtrados
        $diasHabilesPorMes = [];

        // Consulta SQL para días hábiles dentro del rango de fechas
        $sql = "
    WITH fechas AS (
        SELECT DATE_ADD(?, INTERVAL n DAY) AS fecha
        FROM (
            SELECT a.N + b.N * 10 + c.N * 100 AS n
            FROM (SELECT 0 AS N UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4
                UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) a
            CROSS JOIN (SELECT 0 AS N UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4
                UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) b
            CROSS JOIN (SELECT 0 AS N UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4
                UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) c
        ) n
        WHERE DATE_ADD(?, INTERVAL n DAY) <= ?
    )
    SELECT
        YEAR(fecha) AS year,
        MONTH(fecha) AS month,
        COUNT(*) AS total_dias_habiles
    FROM fechas
    WHERE DAYOFWEEK(fecha) NOT IN (1, 7)  -- Excluir domingos y sábados
    GROUP BY YEAR(fecha), MONTH(fecha)
    ORDER BY year, month;
    ";

        // Ejecutar la consulta con DB::select, pasando las fechas de inicio y fin
        $diasHabilesPorMes = DB::select($sql, [$fechaInicio->toDateString(), $fechaInicio->toDateString(), $fechaFin->toDateString()]);

        // Transformar diasHabilesPorMes a un array indexado por año y mes
        $diasHabilesPorMesArray = [];
        foreach ($diasHabilesPorMes as $dia) {
            $key = $dia->year . '-' . str_pad($dia->month, 2, '0', STR_PAD_LEFT);
            $diasHabilesPorMesArray[$key] = $dia->total_dias_habiles * 65; // Multiplicando por 5 aulas 13 hrs dispobibles = 65
        }

        // Obtener los datos para la gráfica
        $data = $registros->pluck('cantidad');

        return view('inform.inicio', compact('labels', 'data', 'diasHabilesPorMesArray'));
    }


}
