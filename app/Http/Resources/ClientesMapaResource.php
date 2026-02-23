<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientesMapaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            // Aqui carregamos os objetos
            // Só adiciona esses campos se o "cliente" tiver sido carregado no Controller
            $this->mergeWhen($this->relationLoaded('cliente'), [
                'codigo'        => $this->cliente->codigo,
                'documento'     => $this->cliente->documento,
                'nome_fantasia' => $this->cliente->nome_fantasia,
                'razao_social'  => $this->cliente->razao_social,
                'endereco'      => $this->cliente->endereco,
                'complemento'   => $this->cliente->complemento,
                'bairro'        => $this->cliente->bairro,
                'cidade'        => $this->cliente->cidade,
                'uf'            => $this->cliente->uf,
                'cep'           => $this->cliente->cep,
                'latitude'      => $this->cliente->latitude,
                'longitude'     => $this->cliente->longitude,
                'categoria'     => $this->cliente->categoria,
                'tipo_pessoa'   => $this->cliente->tipo_pessoa,
                'pdv_ativo'     => $this->cliente->pdv_ativo,
                'contatos'      => $this->cliente->contatos,
            ]),

            'usuario_responsavel_id' => $this->usuario_responsavel_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
