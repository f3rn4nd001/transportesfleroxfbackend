<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Catalogo\menu;

Route::post('Catalogo/menu', [menu::class,'getRegistro'])->middleware(['sesionactiva','Validadpermisos']);
Route::post('Catalogo/menu/detalles', [menu::class,'getDetalles'])->middleware(['sesionactiva']);
Route::post('catalogo/menu/registrar', [menu::class,'postRegistro'])->middleware('sesionactiva','Validadpermisos'); 
Route::post('catalogo/menu/comprementos', [menu::class,'getComprementos'])->middleware(['sesionactiva']);

Auth::routes();