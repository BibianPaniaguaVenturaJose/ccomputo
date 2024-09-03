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

// informes

Route::get('inform/home', [InformController::class, 'generarGraficaAlumnosXAula']);
Route::get('inform/sol', [InformController::class, 'filtrarPorFecha']);

Route::get('inform/software', [InformController::class, 'generarGraficaSoftwareUsado']);
Route::get('inform/soft', [InformController::class, 'software']);


// login
Route::get('/login',[LoginController::class,'show']);
Route::post('/login', [LoginController::class,'login']);


//rutas protegidas
//  Route::middleware('auth')->group(function () {
//  });

// home, donde se crean los registros de aulas

Route::get('/home',[HomeController::class,'index']);
Route::post('/home',[HomeController::class,'store']);




