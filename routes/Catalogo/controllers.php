<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Catalogo\controlles;

Route::post('catalogo/controllers', [controlles::class,'getRegistro'])->middleware(['sesionactiva','Validadpermisos']);
Route::post('catalogo/controllers/detalles', [controlles::class,'getDetalles'])->middleware(['sesionactiva']);
Route::post('catalogo/controllers/registrar', [controlles::class,'postRegistro'])->middleware('sesionactiva','Validadpermisos'); 
Route::post('catalogo/controllers/comprementos', [controlles::class,'getComprementos'])->middleware(['sesionactiva']);
Route::post('catalogo/controllers/eliminar', [controlles::class,'postEliminar'])->middleware('sesionactiva','Validadpermisos'); 

Auth::routes();