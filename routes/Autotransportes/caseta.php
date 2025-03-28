<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Catalogo\Autotransportes\caseta;

Route::post('catalogo/casetas', [caseta::class,'getRegistro'])->middleware(['sesionactiva','Validadpermisos']);
Route::post('atalogo/caseta/comprementos', [caseta::class,'getcompremento'])->middleware(['sesionactiva']);
Route::post('catalogo/caseta/detalles', [caseta::class,'getDetalles'])->middleware(['sesionactiva']);
Route::post('catalogo/caseta/registrar', [caseta::class,'postRegistro'])->middleware('sesionactiva','Validadpermisos'); 
Route::post('catalogo/caseta/eliminar', [caseta::class,'postEliminar'])->middleware('sesionactiva','Validadpermisos'); 

Auth::routes();