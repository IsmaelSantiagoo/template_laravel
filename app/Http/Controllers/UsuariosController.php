<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password as RulesPassword;

class UsuariosController extends Controller
{

    // Listar todos os usuarios
    public function index(Request $request)
    {

        // consultar dados dos usuários e filtrar por nome, cpf ou role se os parâmetros forem fornecidos
        $query = Usuario::query();

        if ($request->has('search')) {
            $query->where('nome', 'like', '%' . $request->input('search') . '%')
                ->orWhere('cpf', 'like', '%' . $request->input('search') . '%');
        }

        if ($request->has('role')) {
            $query->where('role', $request->input('role'));
        }

        $usuarios = $query->get();

        try {
            return response()->json([
                'success' => true,
                'message' => 'Consulta de usuários realizada com sucesso.',
                'data' => $usuarios
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao consultar usuários.'
            ], 500);
        }
    }

    // função para atualizar os dados do usuário
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

        // lógica para atualizar os dados do usuário com o ID fornecido
        try {
            // encontrar usuário pelo ID
            $usuario = Usuario::find($id);

            if (!$usuario) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuário não encontrado.'
                ]);
            }

            // atualizar dados do usuário
            $usuario->nome = $request->nome;
            $usuario->cpf = $request->cpf;
            $usuario->save();

            return response()->json([
                'success' => true,
                'message' => 'Dados do usuário atualizados com sucesso.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar os dados do usuário: ' . $e->getMessage()
            ]);
        }
    }

    // Exibir um usuário específico
    public function show($cpf)
    {
        $usuario = Usuario::where('cpf', $cpf)->first();

        try {
            if (!$usuario) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuário não encontrado.',
                ], 404);
            }
            // dados do usuário formatados
            $usuarioArray = $usuario->toArray();
            $usuarioArray['usuario_responsavel_id'] = $usuario->usuario_responsavel_id;
            return response()->json([
                'success' => true,
                'message' => 'Usuário encontrado com sucesso.',
                'data' => $usuarioArray
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao consultar usuário.',
                'data' => $e->getMessage()
            ], 500);
        }
    }

    // Deletar um usuário
    public function destroy($id)
    {
        $usuario = Usuario::find($id);
        try {
            if (!$usuario) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuário não encontrado para exclusão.',
                ], 404);
            }
            $usuario->delete();
            return response()->json([
                'success' => true,
                'message' => 'Usuário deletado com sucesso.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao deletar usuário.',
                'data' => $e->getMessage()
            ], 400);
        }
    }

    // função para alterar a senha do usuário
    public function alterarSenha(Request $request, $id)
    {

        // configurar regras de validação
        $rules = [
            'senha_antiga' => ['required'],
            'senha_nova' => ['required', 'confirmed:confirmar_senha_nova', RulesPassword::default()],
            'confirmar_senha_nova' => ['required'],
        ];

        // validação dos dados recebidos
        $validator = Validator::make($request->all(), $rules, [
            'senha_antiga.required' => 'A senha antiga é obrigatória.',
            'senha_antiga.password.mixed' => 'A senha deve conter letras maiúsculas, minúsculas, números e símbolos.',

            'senha_nova.min' => 'A senha deve ter no mínimo :min caracteres.',
            'senha_nova.password.mixed' => 'A senha deve conter letras maiúsculas, minúsculas, números e símbolos.',
            'senha_nova.confirmed' => 'As senhas não coincidem.',

            'confirmar_senha_nova.required' => 'A confirmação da senha é obrigatória.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ]);
        }

        // lógica para alterar a senha do usuário com o ID fornecido
        try {
            // encontrar usuário pelo ID
            $usuario = Usuario::find($id);

            if (!$usuario) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuário não encontrado.'
                ]);
            }

            // validar se a senha antiga está correta
            if (!Hash::check($request->senha_antiga, $usuario->senha)) {
                return response()->json([
                    'success' => false,
                    'message' => 'A senha antiga está incorreta.'
                ]);
            }

            // alterar primeiro_acesso para false se for true
            if ($usuario->primeiro_acesso) {
                $usuario->primeiro_acesso = false;
            }

            // atualizar senha do usuário
            $usuario->senha = $request->senha_nova;
            $usuario->save();

            return response()->json([
                'success' => true,
                'message' => 'Senha alterada com sucesso.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao alterar a senha: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Listar favoritos
     *
     * Retorna a lista de menus favoritos do usuário autenticado.
     */
    public function getMenusFavoritos(Request $request)
    {

        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        /** @var Usuario $user Usuário autenticado */
        $user = $request->user();

        $favoritos = $user
            ->menus_favoritos()
            ->with('menu_pai')
            ->get()
            ->setHidden(['pivot']);

        return response()->json([
            'success' => true,
            'data' => $favoritos,
        ]);
    }

    /**
     * Recebe o ID do menu e alterna seu status de favorito para o usuário autenticado.
     * Se o menu já estiver favoritado, ele será desfavoritado, e vice-versa.
     */
    public function favoritarMenu(Request $request, Menu $menu)
    {
        try {
            /** @var Usuario $user Usuário autenticado */
            $user = $request->user();

            $result = $user
                ->menus_favoritos()
                ->toggle($menu->id);

            $menu->load('menu_pai');

            $message = !empty($result['attached'])
                ? 'Menu favoritado com sucesso.'
                : 'Menu desfavoritado com sucesso.';

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'favorito' => !empty($result['attached']),
                    'menu' => $menu,
                ],
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao favoritar/desfavoritar o menu.',
                'debug_error' => $th->getMessage(),
            ], 500);
        }
    }
}
