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
        'mapa',
        'cliente_id',
        'rota_nome',
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
        return $this->belongsToMany(Produto::class, 'produtos_nota_fiscal', 'nota_fiscal_id', 'produto_id')
            ->withPivot('quantidade')
            ->withTimestamps();
    }

    public function usuario()
    {
        return $this->belongsTo(Usuarios::class, 'usuario_responsavel_id');
    }
}
