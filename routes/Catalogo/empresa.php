<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Catalogo\empresa;

Route::post('catalogo/empresa', [empresa::class,'getRegistro'])->middleware(['sesionactiva','Validadpermisos']);
Route::post('catalogo/empresa/detalles', [empresa::class,'getDetalles'])->middleware(['sesionactiva']);
Route::post('catalogo/empresa/registrar', [empresa::class,'postRegistro'])->middleware('sesionactiva','Validadpermisos'); 
Route::post('catalogo/empresa/eliminar', [empresa::class,'postEliminar'])->middleware('sesionactiva','Validadpermisos'); 
Route::post('Catalogo/empresa/comprementos', [empresa::class,'getcompremento'])->middleware(['sesionactiva']);

Auth::routes();