<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\Api\registroAulasController;
use App\Http\Controllers\InformController;
use App\Http\Controllers\RecordsController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

//Todas las graficas juntas
Route::get('inform/home', [InformController::class, 'show']);

// informes
Route::get('inform/inicio', [InformController::class, 'generarGraficaAlumnosXAula']);
Route::get('inform/sol', [InformController::class, 'filtrarPorFecha']);

Route::get('inform/alumnos', [InformController::class, 'generarGraficaAlumnosXAulaXMes']);
Route::get('inform/mes', [InformController::class, 'filtrarFechaPorMes']);


Route::get('inform/software', [InformController::class, 'generarGraficaSoftwareUsado']);
Route::get('inform/soft', [InformController::class, 'filtrarSoftware']);

// login
Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

// home, protegido por auth middleware
Route::middleware(['auth'])->group(function () {
    Route::get('/home', [HomeController::class, 'index']);
    Route::post('/home', [HomeController::class, 'store']);
});


