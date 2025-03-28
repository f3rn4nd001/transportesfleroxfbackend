<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Catalogo\Finanzas\tipopago;

Route::post('catalogo/tipoupago/comprementos', [tipopago::class,'getcompremento'])->middleware(['sesionactiva']);

Auth::routes();