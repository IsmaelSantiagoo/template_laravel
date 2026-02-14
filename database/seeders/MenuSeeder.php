<?php

namespace Database\Seeders;

use App\Models\Menus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // Menus principais
        $menusData = [
            [
                'id' => DB::raw('(UUID())'),
                'titulo' => 'Avarias',
                'icone' => 'TriangleAlert',
                'rota' => '/admin/avarias',
                'ordem' => 1,
                'menu_pai_id' => null,
            ],
        ];

        // Inserir os dados
        foreach ($menusData as $menu) {
            Menus::create([
                'id' => $menu['id'],
                'titulo' => $menu['titulo'],
                'icone' => $menu['icone'],
                'rota' => $menu['rota'],
                'ordem' => $menu['ordem'],
                'menu_pai_id' => $menu['menu_pai_id'],
                'usuario_responsavel_id' => 1,
            ]);
        }
    }
}
