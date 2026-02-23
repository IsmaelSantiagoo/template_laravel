<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class NotasFiscaisAvaria extends Model
{
    use HasUuids;

    protected $table = 'notas_fiscais_avaria';

    public $timestamps = true;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'avaria_id',
        'nota_fiscal_id',
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

    public function nota_fiscal()
    {
        return $this->belongsTo(NotaFiscal::class, 'nota_fiscal_id');
    }
}
