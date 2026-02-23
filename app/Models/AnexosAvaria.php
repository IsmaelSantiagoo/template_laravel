<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class AnexosAvaria extends Model
{
    use HasUuids;

    protected $table = 'anexos_avaria';

    public $timestamps = true;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'path',
        'avaria_id',
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
}
