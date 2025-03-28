<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Catalogo\municipio;

Route::post('catalogo/municipio/comprementos', [municipio::class,'getComprementos'])->middleware(['sesionactiva']);

Auth::routes();