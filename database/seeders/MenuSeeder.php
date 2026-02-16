<?php

namespace Database\Seeders;

use App\Models\Menus;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Step 1: Insert parent menus
        $avarias = Menus::create([
            'titulo' => 'Avarias',
            'icone' => 'TriangleAlert',
            'rota' => '/admin/avarias',
            'ordem' => 1,
            'menu_pai_id' => null,
            'usuario_responsavel_id' => 1,
        ]);

        $importacoes = Menus::create([
            'titulo' => 'Importações',
            'icone' => 'Upload',
            'rota' => '/admin/importacoes',
            'ordem' => 2,
            'menu_pai_id' => null,
            'usuario_responsavel_id' => 1,
        ]);

        $gerenciar = Menus::create([
            'titulo' => 'Gerenciar',
            'icone' => 'MonitorCog',
            'rota' => '/admin/gerenciar',
            'ordem' => 3,
            'menu_pai_id' => null,
            'usuario_responsavel_id' => 1,
        ]);

        // Step 2: Insert child menus using parent IDs
        Menus::create([
            'titulo' => 'Usuários',
            'icone' => 'Users',
            'rota' => '/admin/gerenciar/usuarios',
            'ordem' => 1,
            'menu_pai_id' => $gerenciar->id,
            'usuario_responsavel_id' => 1,
        ]);

        Menus::create([
            'titulo' => 'Motoristas',
            'icone' => 'Truck',
            'rota' => '/admin/gerenciar/motoristas',
            'ordem' => 2,
            'menu_pai_id' => $gerenciar->id,
            'usuario_responsavel_id' => 1,
        ]);

        Menus::create([
            'titulo' => 'Mapas',
            'icone' => 'Map',
            'rota' => '/admin/gerenciar/mapas',
            'ordem' => 3,
            'menu_pai_id' => $gerenciar->id,
            'usuario_responsavel_id' => 1,
        ]);
    }
}
