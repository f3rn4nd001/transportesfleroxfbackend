<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Catalogo\Finanzas\moneda;

Route::post('catalogo/moneda', [moneda::class,'getRegistro'])->middleware(['sesionactiva','Validadpermisos']);
Route::post('catalogo/moneda/detalles', [moneda::class,'getDetalles'])->middleware(['sesionactiva']);
Route::post('catalogo/moneda/registrar', [moneda::class,'postRegistro'])->middleware('sesionactiva','Validadpermisos'); 
Route::post('catalogo/moneda/eliminar', [moneda::class,'postEliminar'])->middleware('sesionactiva','Validadpermisos'); 
Route::post('catalogo/moneda/comprementos', [moneda::class,'getcompremento'])->middleware(['sesionactiva']);

Auth::routes();