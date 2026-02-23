<?php

namespace Database\Seeders;

use App\Models\TipoAvaria;
use Illuminate\Database\Seeder;

class TiposAvaria extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tiposAvaria = [
            'Avariado',
            'Faltante',
            'Inversão',
        ];

        foreach ($tiposAvaria as $descricao) {
            TipoAvaria::create([
                'descricao' => $descricao,
                'usuario_responsavel_id' => '019c8ba5-db8b-704f-879d-2b4765c3ce9c'
            ]);
        }
    }
}
