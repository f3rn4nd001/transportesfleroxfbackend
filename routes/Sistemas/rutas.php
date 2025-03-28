<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Sistemas\relrutas;

Route::post('sistemas/rutas', [relrutas::class,'getRegistro'])->middleware('sesionactiva','Validadpermisos'); 
Route::post('sistemas/rutas/registrar', [relrutas::class,'postRegistro'])->middleware('sesionactiva','Validadpermisos'); 
Route::post('sistemas/rutas/detalles', [relrutas::class,'getRegistro'])->middleware('sesionactiva'); 

Auth::routes();