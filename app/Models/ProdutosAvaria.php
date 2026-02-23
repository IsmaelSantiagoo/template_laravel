<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class ProdutosAvaria extends Model
{
    use HasUuids;

    protected $table = 'produtos_avaria';

    public $timestamps = true;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'avaria_id',
        'produto_id',
        'tipo_avaria',
        'quantidade',
        'usuario_responsavel_id',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_responsavel_id');
    }

    public function avaria()
    {
        return $this->belongsTo(Avaria::class, 'avaria_id');
    }

    public function produto()
    {
        return $this->belongsTo(Produto::class, 'produto_id');
    }
}
