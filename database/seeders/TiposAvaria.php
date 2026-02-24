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
                'usuario_responsavel_id' => '019c9022-246f-70d2-9c7e-51a46b3cd99a'
            ]);
        }
    }
}
