<?php

namespace App\Http\Controllers;

use App\Http\Resources\MapaResource;
use App\Models\Mapa;
use Illuminate\Http\Request;

class MapasController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Mapa::query();

            if ($request->has('search')) {
                $query->Where('codigo', 'like', '%' . $request->input('search') . '%');
            }

            $mapas = $query->with([
                'notas_fiscais',
                'clientes.cliente',
                'motorista.filial',
                'motorista.cluster'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Mapas carregados com sucesso.',
                'data'    => MapaResource::collection($mapas->get())
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
