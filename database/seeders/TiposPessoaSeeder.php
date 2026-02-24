<?php

namespace Database\Seeders;

use App\Models\TipoPessoa;
use Illuminate\Database\Seeder;

class TiposPessoaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $usuario_responsavel_id = config('auth.default_sys_uuid', '4be3c49f-7fe4-45db-a3b4-e80cf45e9247');

        TipoPessoa::create([
            'codigo' => '001',
            'descricao' => 'Pessoa Física',
            'usuario_responsavel_id' => $usuario_responsavel_id
        ]);

        TipoPessoa::create([
            'codigo' => '002',
            'descricao' => 'Pessoa Jurídica',
            'usuario_responsavel_id' => $usuario_responsavel_id
        ]);
    }
}
