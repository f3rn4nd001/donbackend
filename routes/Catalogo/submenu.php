<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\catalogo\submenu;

Route::post('Catalogo/submenu/consulta', [submenu::class,'getRegistro'])->middleware('validadores');
Route::post('Catalogo/submenu/registrar', [submenu::class,'postRegistro'])->middleware('validadores');
Route::post('Catalogo/submenu/detalles', [submenu::class,'getDetalles'])->middleware('validadores');

Auth::routes();