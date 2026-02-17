<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Produto extends Model
{
  use HasUuids;

  protected $table = 'produtos';

  public $incrementing = false;

  protected $keyType = 'string';

  protected $fillable = [
    'codigo',
    'nome',
    'descricao',
    'quantidade',
    'tipo_marca_id',
    'embalagem_id',
    'ean',
    'usuario_responsavel_id',
  ];

  protected $casts = [
    'quantidade' => 'integer',
  ];

  public function tipoMarca()
  {
    return $this->belongsTo(TipoMarca::class);
  }

  public function embalagem()
  {
    return $this->belongsTo(Embalagem::class);
  }

  public function usuario()
  {
    return $this->belongsTo(Usuario::class, 'usuario_responsavel_id');
  }
}
