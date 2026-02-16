<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Motorista extends Model
{
    use HasUuids;

    protected $table = 'motoristas';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'codigo',
        'nome',
        'cpf',
        'status',
        'celular_corporativo',
        'data_admissao',
        'filial_id',
        'cluster_id',
        'senha',
        'usuario_responsavel_id',
    ];

    protected $casts = [
        'data_admissao' => 'date',
    ];

    protected $hidden = [
        'senha',
    ];

    public function filial()
    {
        return $this->belongsTo(Filial::class);
    }

    public function cluster()
    {
        return $this->belongsTo(Cluster::class);
    }

    public function usuario()
    {
        return $this->belongsTo(Usuarios::class, 'usuario_responsavel_id');
    }
}
