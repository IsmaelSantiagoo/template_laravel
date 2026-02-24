<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientesController;

Route::post('', [ClientesController::class, 'store']);
Route::get('', [ClientesController::class, 'index']);
Route::get('/{id}', [ClientesController::class, 'show']);
Route::patch('/{id}', [ClientesController::class, 'update']);
Route::delete('/{id}', [ClientesController::class, 'destroy']);
