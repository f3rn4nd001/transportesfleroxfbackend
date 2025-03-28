<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Catalogo\tipousuario;

Route::post('catalogo/tipousuario/comprementos', [tipousuario::class,'getComprementos'])->middleware(['sesionactiva']);
Auth::routes();