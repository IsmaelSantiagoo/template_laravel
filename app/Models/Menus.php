<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Menus extends Model
{
    use HasUuids;

    protected $table = 'menus';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'titulo',
        'icone',
        'rota',
        'ordem',
        'menu_pai_id',
        'usuario_responsavel_id',
    ];

    public function usuario()
    {
        return $this->belongsTo('App\\Models\\Usuarios', 'usuario_responsavel_id');
    }

    public function menu_pai()
    {
        return $this->belongsTo(Menus::class, 'menu_pai_id');
    }

    /**
     * Build a hierarchical tree structure from a flat array of menus.
     *
     * @param array $menus Flat array of menu items
     * @return array Hierarchical tree structure with 'sub_menus' property
     */
    public static function buildMenuTree(array $menus): array
    {
        $menusById = [];
        foreach ($menus as $menu) {
            $menu['sub_menus'] = [];
            $menusById[$menu['id']] = $menu;
        }

        $roots = [];
        foreach ($menusById as $id => &$menu) {
            $parentId = $menu['menu_pai_id'] ?? null;

            if ($parentId !== null && isset($menusById[$parentId])) {
                $menusById[$parentId]['sub_menus'][] = &$menu;
                continue;
            }

            $roots[] = &$menu;
        }
        unset($menu);

        return $roots;
    }
}
