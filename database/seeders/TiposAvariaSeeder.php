<?php

namespace Database\Seeders;

use App\Models\TipoAvaria;
use Illuminate\Database\Seeder;

class TiposAvariaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $usuario_responsavel_id = config('auth.default_sys_uuid', '4be3c49f-7fe4-45db-a3b4-e80cf45e9247');

        $tiposAvaria = [
            'Avariado',
            'Faltante',
            'Inversão',
        ];

        foreach ($tiposAvaria as $descricao) {
            TipoAvaria::create([
                'descricao' => $descricao,
                'usuario_responsavel_id' => $usuario_responsavel_id
            ]);
        }
    }
}
