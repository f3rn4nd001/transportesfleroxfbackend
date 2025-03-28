<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Finanzas\operadorpsp;

Route::post('finanzas/operadorpsp/detalles', [operadorpsp::class,'getDetalles'])->middleware(['sesionactiva']);
Route::post('finanzas/operadorpsp/registrar', [operadorpsp::class,'postRegistro'])->middleware(['sesionactiva']);

Auth::routes();