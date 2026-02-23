<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MotoristaResource extends JsonResource
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
            'codigo' => $this->codigo,
            'nome' => $this->nome,
            'cpf' => $this->cpf,
            'status' => $this->status,
            'celular_corporativo' => $this->celular_corporativo,
            'data_admissao' => $this->data_admissao,

            // Aqui carregamos os objetos, mas note que NÃO incluímos filial_id e cluster_id
            'filial' => $this->whenLoaded('filial'),
            'cluster' => $this->whenLoaded('cluster'),

            'usuario_responsavel_id' => $this->usuario_responsavel_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
