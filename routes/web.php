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


Route::get('inform/pdf',[InformController::class,'pdf'])->name('inform.pdf');


Route::get('/login',[LoginController::class,'show']);

Route::post('/login', [LoginController::class,'login']);



//Route::middleware('auth')->group(function () {
   //rutas protegidas

Route::get('/home',[HomeController::class,'index']);

Route::post('/home',[HomeController::class,'store']);


//});

