<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AvariasController;

Route::get('', [AvariasController::class, 'index']);
