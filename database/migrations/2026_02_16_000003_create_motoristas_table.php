<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('motoristas', function (Blueprint $table) {
      $table->uuid('id')->primary();
      $table->string('codigo')->unique();
      $table->string('nome');
      $table->string('cpf', 14)->nullable();
      $table->string('status')->default('ativo');
      $table->string('celular_corporativo')->nullable();
      $table->date('data_admissao')->nullable();
      $table->foreignUuid('filial_id')->nullable()->constrained('filiais')->nullOnDelete();
      $table->foreignUuid('cluster_id')->nullable()->constrained('clusters')->nullOnDelete();
      $table->string('senha')->nullable();
      $table->foreignUuid('usuario_responsavel_id')->constrained('usuarios')->cascadeOnDelete();
      $table->timestamps();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('motoristas');
  }
};
