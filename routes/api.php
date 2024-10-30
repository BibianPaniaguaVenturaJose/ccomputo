<?php

use App\Http\Controllers\Api\aulasController;
use App\Http\Controllers\Api\carrerasController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


use App\Http\Controllers\Api\docentesController;
use App\Http\Controllers\Api\materiasController;
use App\Http\Controllers\Api\registroAulasController;
use App\Http\Controllers\Api\registrosController;
use App\Http\Controllers\Api\softwareController;
use App\Models\RegistroAulas;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

//docentes

Route::get('/docente', [docentesController::class, 'index']);

Route::get('/docente/{clave}', [docentesController::class, 'show']);

Route::post('/docente', [docentesController::class, 'store']);

Route::put('/docente/{idDocente}', [docentesController::class, 'update']);

Route::patch('/docente/{idDocente}', [docentesController::class, 'updatePartial']);

Route::delete('/docente/{idDocente}', [docentesController::class, 'destroy']);

// software

Route::get('/software', [softwareController::class, 'index']);

Route::get('/software-options', [softwareController::class, 'showSoftwareOptions']); //devuelve la vista de todos los software

Route::get('/software/{idAula}', [SoftwareController::class, 'getSoftwarePorAula']);


//carreras

Route::get('/carreras', [carrerasController::class, 'index']);

//materias

Route::get('/materias', [materiasController::class, 'index']);

Route::get('/materias/{claveDocente}', [materiasController::class, 'getMateriasPorDocente']);

Route::get('/materias/carrera/{idMateria}', [materiasController::class,'obtenerCarreraPorMateria']);


//aulas

Route::get('/aulas', [aulasController::class, 'index']);

//registros aulas

Route::get('/registrosaulas', [registroAulasController::class, 'index']);

Route::post('/registrosaulas', [registroAulasController::class, 'store']);




