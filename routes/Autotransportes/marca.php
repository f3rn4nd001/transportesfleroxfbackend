<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Catalogo\Autotransportes\marca;

Route::post('catalogo/marca', [marca::class,'getRegistro'])->middleware(['sesionactiva','Validadpermisos']);
Route::post('catalogo/marca/detalles', [marca::class,'getDetalles'])->middleware(['sesionactiva']);
Route::post('catalogo/marca/registrar', [marca::class,'postRegistro'])->middleware('sesionactiva','Validadpermisos'); 
Route::post('catalogo/marca/comprementos', [marca::class,'getComprementos'])->middleware(['sesionactiva']);
Route::post('catalogo/marca/eliminar', [marca::class,'postEliminar'])->middleware('sesionactiva','Validadpermisos'); 
Route::post('catalogo/relmarcamodelo/registrar', [marca::class,'postRegistroRelMarcaModelo'])->middleware('sesionactiva','Validadpermisos'); 

Auth::routes();