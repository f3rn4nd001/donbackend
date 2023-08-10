<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\operacion\viaje;
use App\Http\Controllers\catalogo\usuario;
use App\Http\Controllers\Login\loginController;
use App\Http\Controllers\catalogo\menuSubmenuControPermisos;
use App\Http\Controllers\catalogo\ciuddesEntidades;
use App\Http\Controllers\catalogo\tiff;
use App\Http\Controllers\sistemas\permisosController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
/*
Logins -------------------
*/

Auth::routes();
Route::get('/', function () {
    return view('welcome');
});

