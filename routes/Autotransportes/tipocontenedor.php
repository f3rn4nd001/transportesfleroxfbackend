<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Catalogo\Autotransportes\tipocontenedor;

Route::post('catalogo/tipocontenedor', [tipocontenedor::class,'getRegistro'])->middleware(['sesionactiva','Validadpermisos']);
Route::post('catalogo/tipocontenedor/comprementos', [tipocontenedor::class,'getcompremento'])->middleware(['sesionactiva']);

Auth::routes();