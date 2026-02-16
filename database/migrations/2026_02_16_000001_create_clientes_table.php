<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('codigo')->unique();
            $table->string('documento')->nullable();
            $table->string('nome_fantasia');
            $table->string('razao_social')->nullable();
            $table->string('endereco')->nullable();
            $table->string('complemento')->nullable();
            $table->string('bairro')->nullable();
            $table->string('cidade')->nullable();
            $table->string('uf', 2)->nullable();
            $table->string('cep', 10)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->foreignUuid('categoria_id')->nullable()->constrained('categorias')->nullOnDelete();
            $table->foreignUuid('tipo_pessoa_id')->nullable()->constrained('tipos_pessoa')->nullOnDelete();
            $table->boolean('pdv_ativo')->default(true);
            $table->string('telefone')->nullable();
            $table->boolean('telefone_principal')->default(false);
            $table->foreignUuid('usuario_responsavel_id')->constrained('usuarios')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
