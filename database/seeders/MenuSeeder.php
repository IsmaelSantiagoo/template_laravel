<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Step 1: Insert parent menus
        $avarias = Menu::create([
            'titulo' => 'Avarias',
            'icone' => 'TriangleAlert',
            'rota' => '/admin/avarias',
            'ordem' => 1,
            'menu_pai_id' => null,
            'usuario_responsavel_id' => 1,
        ]);

        $importacoes = Menu::create([
            'titulo' => 'Importações',
            'icone' => 'Upload',
            'rota' => '/admin/importacoes',
            'ordem' => 2,
            'menu_pai_id' => null,
            'usuario_responsavel_id' => 1,
        ]);

        $gerenciar = Menu::create([
            'titulo' => 'Gerenciar',
            'icone' => 'MonitorCog',
            'rota' => '#',
            'ordem' => 3,
            'menu_pai_id' => null,
            'usuario_responsavel_id' => 1,
        ]);

        // Step 2: Insert child menu using parent IDs
        Menu::create([
            'titulo' => 'Usuários',
            'icone' => 'Users',
            'rota' => '/admin/gerenciar/usuarios',
            'ordem' => 1,
            'menu_pai_id' => $gerenciar->id,
            'usuario_responsavel_id' => 1,
        ]);

        Menu::create([
            'titulo' => 'Motoristas',
            'icone' => 'Truck',
            'rota' => '/admin/gerenciar/motoristas',
            'ordem' => 2,
            'menu_pai_id' => $gerenciar->id,
            'usuario_responsavel_id' => 1,
        ]);

        Menu::create([
            'titulo' => 'Mapas',
            'icone' => 'Map',
            'rota' => '/admin/gerenciar/mapas',
            'ordem' => 3,
            'menu_pai_id' => $gerenciar->id,
            'usuario_responsavel_id' => 1,
        ]);
    }
}
