<?php

namespace Database\Seeders;

use App\Models\TipoPessoa;
use Illuminate\Database\Seeder;

class TiposPessoa extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TipoPessoa::create([
            'codigo' => '001',
            'descricao' => 'Pessoa Física',
            'usuario_responsavel' => '1'
        ]);

        TipoPessoa::create([
            'codigo' => '002',
            'descricao' => 'Pessoa Jurídica',
            'usuario_responsavel' => '1'
        ]);
    }
}
