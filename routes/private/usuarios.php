<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuariosController;

Route::patch('alterar-senha/{id}', [UsuariosController::class, 'alterarSenha']); // Rota para alterar a senha do usuário

Route::get('/menus-favoritos', [UsuariosController::class, 'getMenusFavoritos']);
Route::post('/favoritar-menu/{menu}', [UsuariosController::class, 'favoritarMenu']);
