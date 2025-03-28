<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Catalogo\Autotransportes\modelo;

Route::post('catalogo/modelo', [modelo::class,'getRegistro'])->middleware(['sesionactiva','Validadpermisos']);
Route::post('catalogo/modelo/detalles', [modelo::class,'getDetalles'])->middleware(['sesionactiva']);
Route::post('catalogo/modelo/registrar', [modelo::class,'postRegistro'])->middleware('sesionactiva','Validadpermisos'); 
Route::post('catalogo/modelo/eliminar', [modelo::class,'postEliminar'])->middleware('sesionactiva','Validadpermisos'); 
Route::post('catalogo/modelo/comprementos', [modelo::class,'getComprementos'])->middleware(['sesionactiva']);

Auth::routes();