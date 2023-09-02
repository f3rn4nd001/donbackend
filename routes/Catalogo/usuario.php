<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\catalogo\usuario;

Route::post('Catalogo/usuario/consulta', [usuario::class,'getRegistro'])->middleware('validadores');
Route::post('Catalogo/usuario/detalles', [usuario::class,'getDetalles'])->middleware('validadores');
Route::post('Catalogo/usuario/registrar', [usuario::class,'postRegistro'])->middleware('validadores');
Route::post('Catalogo/usuario/registrar/compremento', [usuario::class,'getcompremento'])->middleware('validadores');


Auth::routes();
