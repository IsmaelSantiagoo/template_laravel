<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class NotaFiscal extends Model
{
    use HasUuids;

    protected $table = 'notas_fiscais';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'numero',
        'pedido',
        'cliente_id',
        'mapa',
        'data_operacao',
        'data_emissao',
        'valor_bruto',
        'total_desconto',
        'valor_total',
        'status',
        'usuario_responsavel_id',
    ];

    protected $casts = [
        'data_operacao' => 'date',
        'data_emissao' => 'date',
        'valor_bruto' => 'decimal:2',
        'total_desconto' => 'decimal:2',
        'valor_total' => 'decimal:2',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function produtos()
    {
        return $this->hasMany(ProdutoNotaFiscal::class, 'nota_fiscal_id');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_responsavel_id');
    }
}
