<?php

namespace App\Imports;

use App\Models\Materias;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;


class DataImport implements ToCollection, WithHeadingRow
{

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            try {
                // Intento de insertar o crear la materia
                Materias::firstOrCreate([
                    'nombre' => $row['materia'],
                    'idCarrera' => $row['idcarrera'],
                    'claveDocente' => $row['clave'],
                ]);
            }catch (\ErrorException $e){
                //Capturar errores por ingresar un archivo no leible, debe ser un .CSV
                Log::error("Error por el tipo de archivo o extención." . $e->getMessage());
            } catch (QueryException $e) {
                // Captura errores de consulta SQL
                Log::error("Error al insertar la materia: " . $e->getMessage(), [
                    'materia' => $row['materia'],
                    'idCarrera' => $row['idcarrera'],
                    'claveDocente' => $row['clave']
                ]);
            } catch (\Exception $e) {
                // Captura cualquier otro tipo de error
                Log::error("Error desconocido al insertar la materia: " . $e->getMessage(), [
                    'materia' => $row['materia'],
                    'idCarrera' => $row['idcarrera'],
                    'claveDocente' => $row['clave']
                ]);
            }
        }
    }


}
