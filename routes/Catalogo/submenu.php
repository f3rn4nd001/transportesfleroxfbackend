<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Catalogo\submenu;

Route::post('catalogo/submenu', [submenu::class,'getRegistro'])->middleware(['sesionactiva','Validadpermisos']);
Route::post('catalogo/submenu/detalles', [submenu::class,'getDetalles'])->middleware(['sesionactiva']);
Route::post('catalogo/submenu/registrar', [submenu::class,'postRegistro'])->middleware('sesionactiva','Validadpermisos'); 
Route::post('catalogo/submenu/comprementos', [submenu::class,'getComprementos'])->middleware(['sesionactiva']);

Auth::routes();