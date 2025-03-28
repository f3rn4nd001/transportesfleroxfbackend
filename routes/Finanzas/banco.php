<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Catalogo\Finanzas\banco;

Route::post('catalogo/bancos', [banco::class,'getRegistro'])->middleware(['sesionactiva','Validadpermisos']);
Route::post('catalogo/bancos/detalles', [banco::class,'getDetalles'])->middleware(['sesionactiva']);
Route::post('catalogo/bancos/registrar', [banco::class,'postRegistro'])->middleware('sesionactiva','Validadpermisos'); 
Route::post('catalogo/bancos/eliminar', [banco::class,'postEliminar'])->middleware('sesionactiva','Validadpermisos'); 
Route::post('catalogo/bancos/comprementos', [banco::class,'getcompremento'])->middleware(['sesionactiva']);

Auth::routes();
