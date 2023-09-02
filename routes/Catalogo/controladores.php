<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\catalogo\controladores;

Route::post('Catalogo/controllers/consulta', [controladores::class,'getRegistro'])->middleware('validadores');
Route::post('Catalogo/controllers/registrar', [controladores::class,'postRegistro'])->middleware('validadores');
Route::post('Catalogo/controllers/detalles', [controladores::class,'getDetalles'])->middleware('validadores');

Auth::routes();