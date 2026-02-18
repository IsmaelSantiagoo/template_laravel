<?php

namespace App\Http\Controllers;

use App\Models\Motorista;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MotoristasController extends Controller
{
    // Listar todos os motoristas
    public function index(Request $request)
    {

        // consultar dados dos motoristas e filtrar por nome ou cpf se os parâmetros forem fornecidos
        $query = Motorista::query();

        if ($request->has('search')) {
            $query->where('nome', 'like', '%' . $request->input('search') . '%')
                ->orWhere('cpf', 'like', '%' . $request->input('search') . '%');
        }

        $motoristas = $query->with(['filial', 'cluster'])
            ->get()->makeHidden(['filial_id', 'cluster_id']);

        try {
            return response()->json([
                'success' => true,
                'message' => 'Consulta de motoristas realizada com sucesso.',
                'data' => $motoristas
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao consultar motoristas.'
            ], 500);
        }
    }

    // função para atualizar os dados do motorista
    public function update(Request $request, $id)
    {
        // configurar regras de validação
        $rules = [
            'nome' => ['required'],
            'cpf' => ['nullable'],
        ];

        // validação dos dados recebidos
        $validator = Validator::make($request->all(), $rules, [
            'nome.required' => 'O nome é obrigatório.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ]);
        }

        // lógica para atualizar os dados do motorista com o ID fornecido
        try {
            // encontrar motorista pelo ID
            $motorista = Motorista::find($id);

            if (!$motorista) {
                return response()->json([
                    'success' => false,
                    'message' => 'Motorista não encontrado.'
                ]);
            }

            // atualizar dados do motorista
            $motorista->nome = $request->nome;
            $motorista->cpf = $request->cpf;
            $motorista->save();

            return response()->json([
                'success' => true,
                'message' => 'Dados do motorista atualizados com sucesso.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar os dados do motorista: ' . $e->getMessage()
            ]);
        }
    }

    // Exibir um motorista específico
    public function show($cpf)
    {
        $motorista = Motorista::where('cpf', $cpf)->first();

        try {
            if (!$motorista) {
                return response()->json([
                    'success' => false,
                    'message' => 'Motorista não encontrado.',
                ], 404);
            }
            // dados do motorista formatados
            $motoristaArray = $motorista->toArray();
            $motoristaArray['usuario_responsavel_id'] = $motorista->usuario_responsavel_id;
            return response()->json([
                'success' => true,
                'message' => 'Motorista encontrado com sucesso.',
                'data' => $motoristaArray
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao consultar motorista.',
                'data' => $e->getMessage()
            ], 500);
        }
    }

    // Deletar um motorista
    public function destroy($id)
    {
        $motorista = Motorista::find($id);
        try {
            if (!$motorista) {
                return response()->json([
                    'success' => false,
                    'message' => 'Motorista não encontrado para exclusão.',
                ], 404);
            }
            $motorista->delete();
            return response()->json([
                'success' => true,
                'message' => 'Motorista deletado com sucesso.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao deletar motorista.',
                'data' => $e->getMessage()
            ], 400);
        }
    }
}
