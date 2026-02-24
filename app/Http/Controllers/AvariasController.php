<?php

namespace App\Http\Controllers;

use App\Http\Resources\AvariaResource;
use App\Models\Avaria;
use Illuminate\Http\Request;

class AvariasController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Avaria::query();

            if ($request->has('search')) {

                // consultar pelo código do mapa
                $query->whereHas('mapa', function ($q) use ($request) {
                    $q->where('codigo', 'like', '%' . $request->search . '%');
                });

                // consultar pelo nome ou código do cliente
                $query->orWhereHas('cliente', function ($q) use ($request) {
                    $q->where('nome_fantasia', 'like', '%' . $request->search . '%')->orWhere('codigo', 'like', '%' . $request->search . '%');
                });
            }

            $avarias = $query->with([
                'cliente',
                'mapa',
                'mapa.motorista.filial',
                'mapa.motorista.cluster',
                'notasFiscais.nota_fiscal',
                'produtos.produto',
                'produtos.tipoAvaria',
                'produtos.produto.tipoMarca',
                'anexos'
            ])->get();

            return response()->json([
                'success' => true,
                'message' => 'Avarias carregadas com sucesso.',
                'data' => AvariaResource::collection($avarias)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar avarias.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
