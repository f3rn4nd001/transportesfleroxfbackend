<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Catalogo\Autotransportes\tipotransportes;

Route::post('catalogo/tipotransportes', [tipotransportes::class,'getRegistro'])->middleware(['sesionactiva','Validadpermisos']);
Route::post('catalogo/tipotransportes/detalles', [tipotransportes::class,'getDetalles'])->middleware(['sesionactiva']);

Auth::routes();