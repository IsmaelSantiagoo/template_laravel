<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class ProdutoNotaFiscal extends Model
{
  use HasUuids;

  protected $table = 'produtos_nota_fiscal';

  public $incrementing = false;

  protected $keyType = 'string';

  protected $fillable = [
    'nota_fiscal_id',
    'produto_id',
    'quantidade',
    'usuario_responsavel_id',
  ];

  protected $casts = [
    'quantidade' => 'integer',
  ];

  public function notaFiscal()
  {
    return $this->belongsTo(NotaFiscal::class);
  }

  public function produto()
  {
    return $this->belongsTo(Produto::class);
  }

  public function usuario()
  {
    return $this->belongsTo(Usuarios::class, 'usuario_responsavel_id');
  }
}
