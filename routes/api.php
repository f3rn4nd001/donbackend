<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Login\loginController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::post('Login', [loginController::class,'posLogin']);
Route::post('logout', [loginController::class,'logout']);
Route::post('Login/postValidadContrasena', [loginController::class,'postValidadContrasena'])->middleware('validadores');
Route::post('Login/postValidadVista', [loginController::class,'postValidadVista'])->middleware('validadores');
Route::post('Login/postLogout', [loginController::class,'postLogout'])->middleware('validadores');

Auth::routes();