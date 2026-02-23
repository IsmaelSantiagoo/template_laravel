<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Mapa extends Model
{
    use HasUuids;

    protected $table = 'mapas';

    public $timestamps = true;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'codigo',
        'status',
        'motorista_id',
        'usuario_responsavel_id',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_responsavel_id');
    }

    public function motorista()
    {
        return $this->belongsTo(Motorista::class, 'motorista_id');
    }

    public function clientes()
    {
        return $this->hasMany(ClientesMapa::class, 'mapa_id');
    }

    public function notas_fiscais()
    {
        // aqui usamos o código do mapa para criar o relacionamento, já que as notas fiscais estão associadas ao código do mapa e não ao ID
        return $this->hasMany(NotaFiscal::class, 'mapa', 'codigo');
    }
}
