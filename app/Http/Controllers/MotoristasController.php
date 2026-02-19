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

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($query) use ($search) {
                $query->where('nome', 'like', '%' . $search . '%')
                    ->orWhere('cpf', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('filial')) {
            $query->where('filial_id', $request->input('filial'));
        }

        if ($request->filled('cluster')) {
            $query->where('cluster_id', $request->input('cluster'));
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
            'codigo' => ['nullable'],
            'nome' => ['required'],
            'cpf' => ['nullable'],
            'status' => ['nullable', 'in:ativo,inativo'],
            'celular_corporativo' => ['nullable'],
            'data_admissao' => ['nullable', 'date'],
            'filial_id' => ['nullable', 'exists:filiais,id'],
            'cluster_id' => ['nullable', 'exists:clusters,id'],
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
            $motorista->update([
                'codigo' => $request->input('codigo'),
                'nome' => $request->input('nome'),
                'cpf' => $request->input('cpf'),
                'status' => $request->input('status'),
                'celular_corporativo' => $request->input('celular_corporativo'),
                'data_admissao' => $request->input('data_admissao'),
                'filial_id' => $request->input('filial_id'),
                'cluster_id' => $request->input('cluster_id'),
            ]);

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
