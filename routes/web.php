<?php

use App\Http\Controllers\ExcelController;
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

//Metodos de Home
Route::get('inform/home', [InformController::class, 'show']);
Route::get('inform/filtrar', [InformController::class, 'filtrarDatos'])->name('filtrar');


// Metodos de inicio
Route::get('inform/inicio', [InformController::class, 'cargar']);
Route::get('inform/sol', [InformController::class, 'filtrarPorFecha'])->name('range');


// login
Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

// home, protegido por auth middleware
Route::middleware(['auth'])->group(function () {
    Route::get('/home', [HomeController::class, 'index']);
    Route::post('/home', [HomeController::class, 'store']);
    Route::get('/logout', [HomeController::class, 'logout']);
});


//Rutas para importar excel
Route::get('inform/excel', [ExcelController::class, 'form']);
Route::post('inform/excel', [ExcelController::class, 'import']);



