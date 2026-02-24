<?php

namespace App\Http\Controllers;

use App\Http\Resources\ClienteResource;
use App\Models\Cliente;
use Illuminate\Http\Request;

class ClientesController extends Controller
{
    // Listar todos os clientes
    public function index(Request $request)
    {

        // consultar dados dos clientes e filtrar por nome ou cpf se os parâmetros forem fornecidos
        $query = Cliente::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($query) use ($search) {
                $query->where('codigo', 'like', '%' . $search . '%')
                    ->orWhere('nome_fantasia', 'like', '%' . $search . '%');
            });
        }

        // consultar quais detalhes devem ser carregados com base no parâmetro 'detalhar' (boolean) da requisição
        if ($request->filled('detalhar')) {
            $query->with([
                'notasFiscais.produtos',
                'notasFiscais.produtos.produto',
            ]);
        }

        try {
            return response()->json([
                'success' => true,
                'message' => 'Consulta de clientes realizada com sucesso.',
                'data' => ClienteResource::collection($query->get())
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao consultar clientes.'
            ], 500);
        }
    }
}
