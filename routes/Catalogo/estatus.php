<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Catalogo\estatus;

Route::post('Catalogo/estatus', [estatus::class,'getRegistro'])->middleware(['sesionactiva','Validadpermisos']);
Route::post('Catalogo/estatus/comprementos', [estatus::class,'getcompremento'])->middleware(['sesionactiva']);

Auth::routes();