<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClienteResource extends JsonResource
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
            'codigo' => $this->codigo,
            'documento' => $this->documento,
            'nome_fantasia' => $this->nome_fantasia,
            'razao_social' => $this->razao_social,
            'endereco' => $this->endereco,
            'complemento' => $this->complemento,
            'bairro' => $this->bairro,
            'cidade' => $this->cidade,
            'uf' => $this->uf,
            'cep' => $this->cep,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'categoria' => $this->categoria,
            'tipo_pessoa' => $this->tipoPessoa,
            'pdv_ativo' => $this->pdv_ativo,
            'contatos' => $this->contatos,

            // retorna quantidade de notas fiscais se houver
            'qntd_notas_fiscais' => $this->whenLoaded('notasFiscais', function () {
                return $this->notasFiscais->count();
            }),
            // retorna quantidade de produtos associados às notas fiscais se houver
            'qntd_produtos' => $this->whenLoaded('notasFiscais', function () {
                return $this->notasFiscais->sum(function ($nota) {
                    return $nota->produtos->count();
                });
            }),

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
