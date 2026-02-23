<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MapaResource extends JsonResource
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
            'status' => $this->status,

            // Aqui carregamos os objetos
            'motorista' => new MotoristaResource($this->whenLoaded('motorista')),
            'clientes' => ClientesMapaResource::collection($this->whenLoaded('clientes')),
            'notas_fiscais' => NotaFiscalResource::collection($this->whenLoaded('notas_fiscais')),

            'usuario_responsavel_id' => $this->usuario_responsavel_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
