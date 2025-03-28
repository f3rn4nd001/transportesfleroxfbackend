<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Catalogo\iconos;

Route::post('Catalogo/iconos/comprementos', [iconos::class,'getComprementos'])->middleware(['sesionactiva']);
Route::post('Catalogo/icono', [iconos::class,'getRegistro'])->middleware(['sesionactiva','Validadpermisos']);

Auth::routes();