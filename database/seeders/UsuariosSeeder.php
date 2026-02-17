<?php

namespace Database\Seeders;

use App\Models\Usuario;
use Illuminate\Database\Seeder;

class UsuariosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultPassword = config('auth.default_sys_pass');

        if ($defaultPassword === null) {
            return;
        }

        Usuario::create([
            'nome' => 'Ismael Santiago',
            'cpf' => '16627182688',
            'senha' => $defaultPassword,
            'role' => 'monitoramento',
            'primeiro_acesso' => true
        ]);
    }
}
