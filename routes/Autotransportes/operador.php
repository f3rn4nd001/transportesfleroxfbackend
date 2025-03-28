<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Catalogo\usuario;
use App\Http\Controllers\Catalogo\Autotransportes\operador;

Route::post('autotransportes/operador', [usuario::class,'getRegistro'])->middleware(['sesionactiva','Validadpermisos']);
Route::post('autotransportes/operador/detalles', [usuario::class,'getDetalles'])->middleware(['sesionactiva']);
Route::post('autotransportes/operador/licencia/detalles', [operador::class,'getDetallesLicencia'])->middleware(['sesionactiva']);
Route::post('autotransportes/operador/licencia/registrar', [operador::class,'postRegistroLicencia'])->middleware('sesionactiva','Validadpermisos'); 


Auth::routes();