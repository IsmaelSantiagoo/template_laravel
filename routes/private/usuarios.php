<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuariosController;

Route::patch('alterar-senha/{id}', [UsuariosController::class, 'alterarSenha']); // Rota para alterar a senha do usuário

Route::get('/menus-favoritos', [UsuariosController::class, 'getMenusFavoritos']);
Route::post('/favoritar-menu/{menu}', [UsuariosController::class, 'favoritarMenu']);

Route::get('', [UsuariosController::class, 'index']);
Route::get('/{cpf}', [UsuariosController::class, 'show']);
Route::patch('/{id}', [UsuariosController::class, 'update']);
Route::delete('/{id}', [UsuariosController::class, 'destroy']);
