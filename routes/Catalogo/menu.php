<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\catalogo\menu;

Route::post('Catalogo/menu/consulta', [menu::class,'getRegistro'])->middleware('validadores');
Route::post('Catalogo/menu/registrar', [menu::class,'postRegistro'])->middleware('validadores');
Route::post('Catalogo/menu/detalles', [menu::class,'getDetalles'])->middleware('validadores');

Auth::routes();