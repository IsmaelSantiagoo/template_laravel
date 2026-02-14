<?php

use App\Http\Controllers\ImportController;
use Illuminate\Support\Facades\Route;

Route::get('', [ImportController::class, 'list']);
Route::post('', [ImportController::class, 'start']);
