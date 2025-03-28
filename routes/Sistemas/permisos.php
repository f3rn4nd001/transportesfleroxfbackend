<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Sistemas\relpermisos;

Route::post('sistemas/permisos/detalles', [relpermisos::class,'getDetalles'])->middleware('sesionactiva'); 
Route::post('sistemas/permisos/registrar', [relpermisos::class,'postRegistro'])->middleware('sesionactiva','Validadpermisos'); 

Auth::routes();