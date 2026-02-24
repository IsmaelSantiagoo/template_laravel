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
        $usuario_responsavel_id = config('auth.default_sys_uuid', '4be3c49f-7fe4-45db-a3b4-e80cf45e9247');

        // Step 1: Insert parent menus
        Menu::create([
            'titulo' => 'Avarias',
            'icone' => 'TriangleAlert',
            'rota' => '/admin/avarias',
            'ordem' => 1,
            'menu_pai_id' => null,
            'usuario_responsavel_id' => $usuario_responsavel_id,
        ]);

        Menu::create([
            'titulo' => 'Importações',
            'icone' => 'Upload',
            'rota' => '/admin/importacoes',
            'ordem' => 2,
            'menu_pai_id' => null,
            'usuario_responsavel_id' => $usuario_responsavel_id,
        ]);

        $gerenciar = Menu::create([
            'titulo' => 'Gerenciar',
            'icone' => 'MonitorCog',
            'rota' => '#',
            'ordem' => 3,
            'menu_pai_id' => null,
            'usuario_responsavel_id' => $usuario_responsavel_id,
        ]);

        // Step 2: Insert child menu using parent IDs
        Menu::create([
            'titulo' => 'Usuários',
            'icone' => 'Users',
            'rota' => '/admin/gerenciar/usuarios',
            'ordem' => 1,
            'menu_pai_id' => $gerenciar->id,
            'usuario_responsavel_id' => $usuario_responsavel_id,
        ]);

        Menu::create([
            'titulo' => 'Motoristas',
            'icone' => 'Truck',
            'rota' => '/admin/gerenciar/motoristas',
            'ordem' => 2,
            'menu_pai_id' => $gerenciar->id,
            'usuario_responsavel_id' => $usuario_responsavel_id,
        ]);

        Menu::create([
            'titulo' => 'Mapas',
            'icone' => 'Map',
            'rota' => '/admin/gerenciar/mapas',
            'ordem' => 3,
            'menu_pai_id' => $gerenciar->id,
            'usuario_responsavel_id' => $usuario_responsavel_id,
        ]);
    }
}
