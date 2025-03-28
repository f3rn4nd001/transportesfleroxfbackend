<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Catalogo\tipoempresa;

Route::post('catalogo/tipoempresa/comprementos', [tipoempresa::class,'getcompremento'])->middleware(['sesionactiva']);

Auth::routes();