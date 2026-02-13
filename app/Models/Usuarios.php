<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

/**
 * Class Usuarios
 *
 * @property int $id
 * @property string $nome
 * @property string|null $senha
 * @property string|null $email
 * @property string|null $foto_perfil
 * @property bool $status
 * @property string|null $tipo
 */
class Usuarios extends Authenticatable
{
    use CanResetPassword, HasApiTokens, Notifiable;

    protected $primaryKey = 'id';

    protected $connection = 'mysql';

    protected $table = 'usuarios';

    protected $casts = [
        'status' => 'bool',
    ];

    protected $hidden = [
        'senha',
    ];

    protected $fillable = [
        'nome',
        'senha',
        'cpf',
        'role',
        'primeiro_acesso',
    ];

    // --- Validation Rules ---
    public static function createRules(): array
    {
        return [
            'nome' => ['required', 'string', 'max:100'],
            'cpf' => ['required', 'string', 'max:11', 'unique:usuarios,cpf'],
            'senha' => ['required', 'string'],
            'confirmar_senha' => ['required', 'same:senha'],
        ];
    }

    public static function messages(): array
    {
        return [
            'senha.required' => 'A senha é obrigatória.',
            'senha.string' => 'A senha deve ser do tipo texto.',
            'confirmar_senha.required' => 'A confirmação de senha é obrigatória.',
            'confirmar_senha.same' => 'As senhas não coincidem.',
            'cpf.required' => 'O CPF é obrigatório.',
            'cpf.string' => 'O CPF deve ser do tipo texto.',
            'cpf.max' => 'O CPF não pode ter mais de 11 caracteres.',
            'cpf.unique' => 'O CPF informado já está em uso.',
            'nome.required' => 'O nome é obrigatório.',
            'nome.string' => 'O nome deve ser do tipo texto.',
            'nome.max' => 'O nome não pode ter mais de 100 caracteres.',
            'role.required' => 'O tipo de usuário é obrigatório.',
        ];
    }

    // --- Accessors ---

    // --- Mutators ---

    public function setNomeAttribute($value)
    {
        $this->attributes['nome'] = mb_strtolower($value);
    }

    public function setSenhaAttribute($value)
    {
        $this->attributes['senha'] = Hash::make($value);
    }

    public function setCpfAttribute($value)
    {
        $this->attributes['cpf'] = mb_strtolower($value);
    }

    public function setRoleAttribute($value)
    {
        $this->attributes['role'] = mb_strtolower($value) ?: 1;
    }

    // --- JWT Methods ---

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getAuthPasswordName()
    {
        return 'senha';
    }

    public function getJWTCustomClaims()
    {
        return [
            'id' => (string) $this->id,
            'nome' => $this->nome,
            'cpf' => $this->cpf,
            'role' => $this->role ?? null,
            'first_access' => $this->first_access,
        ];
    }

    public function notificacoes()
    {
        return $this->hasMany(Notificacoes::class, 'usuario_id');
    }

    public function menus_favoritos()
    {
        return $this->belongsToMany(Menus::class, 'usuarios_menus_favoritos', 'usuario_id', 'menu_id');
    }
}
