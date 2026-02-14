<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoMarca extends Model
{
    protected $table = 'tipo_marca';

    public $timestamps = true;

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'codigo',
        'descricao',
        'usuario_responsavel_id',
    ];

    public function usuario()
    {
        return $this->belongsTo('App\\Models\\Usuarios', 'usuario_responsavel_id');
    }
}
