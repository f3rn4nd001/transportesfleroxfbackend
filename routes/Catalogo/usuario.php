<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Catalogo\usuario;

Route::post('catalogo/usuario', [usuario::class,'getRegistro'])->middleware(['sesionactiva','Validadpermisos']);
Route::post('Catalogo/usuario/detalles', [usuario::class,'getDetalles'])->middleware(['sesionactiva']);
Route::post('catalogo/usuario/registrar', [usuario::class,'postRegistro'])->middleware('sesionactiva','Validadpermisos'); 
Route::post('catalogo/usuario/eliminar', [usuario::class,'postEliminar'])->middleware('sesionactiva','Validadpermisos'); 
Route::post('catalogo/usuario/comprementos', [usuario::class,'getComprementos'])->middleware(['sesionactiva']);

Auth::routes();