<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Catalogo\estado;

Route::post('catalogo/estado/comprementos', [estado::class,'getComprementos'])->middleware(['sesionactiva']);
Auth::routes();