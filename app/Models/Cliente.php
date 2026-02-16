<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasUuids;

    protected $table = 'clientes';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'codigo',
        'documento',
        'nome_fantasia',
        'razao_social',
        'endereco',
        'complemento',
        'bairro',
        'cidade',
        'uf',
        'cep',
        'latitude',
        'longitude',
        'categoria_id',
        'tipo_pessoa_id',
        'pdv_ativo',
        'telefone',
        'telefone_principal',
        'usuario_responsavel_id',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'pdv_ativo' => 'boolean',
        'telefone_principal' => 'boolean',
    ];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function tipoPessoa()
    {
        return $this->belongsTo(TipoPessoa::class);
    }

    public function usuario()
    {
        return $this->belongsTo(Usuarios::class, 'usuario_responsavel_id');
    }
}
