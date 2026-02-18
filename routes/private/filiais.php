<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FiliaisController;

Route::get('', [FiliaisController::class, 'index']);
Route::get('/{id}', [FiliaisController::class, 'show']);
Route::patch('/{id}', [FiliaisController::class, 'update']);
Route::delete('/{id}', [FiliaisController::class, 'destroy']);
