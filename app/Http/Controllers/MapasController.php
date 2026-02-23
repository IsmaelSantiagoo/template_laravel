<?php

namespace App\Http\Controllers;

use App\Models\Mapa;
use Illuminate\Http\Request;

class MapasController extends Controller
{
    public function index(Request $request)
    {
        try {
            $mapas = Mapa::query();

            if ($request->has('search')) {
                $mapas->where('descricao', 'like', '%' . $request->input('search') . '%')
                    ->orWhere('codigo', 'like', '%' . $request->input('search') . '%');
            }

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
