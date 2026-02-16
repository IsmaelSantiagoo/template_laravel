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
}
