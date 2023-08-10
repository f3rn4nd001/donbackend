
<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\catalogo\usuario;

Route::get('Catalogo/usuario/consulta', [usuario::class,'getRegistro']);
Route::post('Catalogo/usuario/registro', [usuario::class,'postRegistro']);
Route::post('Catalogo/usuario/detalles', [usuario::class,'getDetalles']);
Route::post('Catalogo/usuario/getRFC', [usuario::class,'getRFC']);
Route::post('Catalogo/usuario/delete', [usuario::class,'postEliminar']);

Auth::routes();