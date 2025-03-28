<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Catalogo\transportes;
Route::post('catalogo/transportes', [transportes::class,'getRegistro'])->middleware(['sesionactiva','Validadpermisos']);
Route::post('catalogo/transportes/comprementos', [transportes::class,'getComprementos'])->middleware(['sesionactiva']);

Auth::routes();