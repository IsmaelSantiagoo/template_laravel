<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MotoristasController;

Route::get('', [MotoristasController::class, 'index']);
Route::get('/{id}', [MotoristasController::class, 'show']);
Route::patch('/{id}', [MotoristasController::class, 'update']);
Route::delete('/{id}', [MotoristasController::class, 'destroy']);
