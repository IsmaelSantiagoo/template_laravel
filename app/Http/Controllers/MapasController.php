<?php

namespace App\Http\Controllers;

use App\Models\Motorista;
use App\Models\NotaFiscal;
use Illuminate\Http\Request;

class MapasController extends Controller
{
    public function index(Request $request)
    {
        try {
            // 1. Inicia a query de motoristas
            $motoristasQuery = Motorista::with(['filial:id,descricao', 'cluster:id,descricao']);

            // 2. Aplica o filtro de filial no motorista, se enviado
            if ($request->filled('filial') && $request->input('filial') !== 'todos') {
                $motoristasQuery->where('filial_id', $request->input('filial'));
            }

            $motoristas = $motoristasQuery->get(['id', 'codigo', 'nome', 'cluster_id', 'filial_id', 'mapa']);

            // 3. Se não houver motoristas para a filial filtrada, retorna array vazio imediatamente
            if ($motoristas->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Nenhum mapa encontrado para os filtros selecionados.',
                    'data'    => []
                ]);
            }

            // 4. Pega a lista de IDs de mapas dos motoristas encontrados
            $mapasValidos = $motoristas->pluck('mapa')->unique();

            // 5. Busca as notas fiscais apenas dos mapas que pertencem aos motoristas filtrados
            $mapas = NotaFiscal::with(['cliente', 'produtos'])
                ->whereIn('mapa', $mapasValidos)
                ->get()
                ->groupBy('mapa')
                ->map(function ($notasNoMapa, $mapaId) use ($motoristas) {

                    // Busca o motorista correspondente a este mapa (já filtrado anteriormente)
                    $motoristaRaw = $motoristas->where('mapa', $mapaId)->first();

                    // Agrupar as notas por CLIENTE
                    $clientesNoMapa = $notasNoMapa->groupBy('cliente_id')->map(function ($notasDoCliente) {
                        $cliente = $notasDoCliente->first()->cliente;

                        return [
                            'id'    => $cliente->id ?? null,
                            'nome'  => $cliente->nome_fantasia ?? 'Cliente não identificado',
                            'notas_fiscais' => $notasDoCliente->map(function ($nota) {
                                return [
                                    'id'               => $nota->id,
                                    'numero_nota'      => $nota->numero,
                                    'valor_total_nota' => $nota->valor_total,
                                    'produtos'         => $nota->produtos->map(function ($produto) {
                                        return [
                                            'id'             => $produto->id,
                                            'descricao'      => $produto->descricao,
                                            'quantidade'     => $produto->pivot->quantidade ?? 0,
                                            'valor_unitario' => $produto->pivot->valor_unitario ?? 0,
                                            'valor_total'    => $produto->pivot->valor_total ?? 0,
                                        ];
                                    })
                                ];
                            })->values()
                        ];
                    })->values();

                    return [
                        'mapa'          => $mapaId,
                        'qntd_notas'    => $notasNoMapa->count(),
                        'qntd_clientes' => $clientesNoMapa->count(),
                        'motorista'     => $motoristaRaw ? [
                            'id'      => $motoristaRaw->id,
                            'codigo'  => $motoristaRaw->codigo,
                            'nome'    => $motoristaRaw->nome,
                            'filial'  => $motoristaRaw->filial->descricao ?? null,
                            'cluster' => $motoristaRaw->cluster->descricao ?? null,
                        ] : null,
                        'clientes'      => $clientesNoMapa,
                    ];
                })->values();

            return response()->json([
                'success' => true,
                'message' => 'Mapas carregados com sucesso.',
                'data'    => $mapas
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar mapas.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
