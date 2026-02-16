<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('notas_fiscais', function (Blueprint $table) {
      $table->uuid('id')->primary();
      $table->string('numero')->unique();
      $table->string('pedido')->nullable();
      $table->string('mapa')->nullable();
      $table->foreignUuid('cliente_id')->nullable()->constrained('clientes')->nullOnDelete();
      $table->string('rota_nome')->nullable();
      $table->date('data_operacao')->nullable();
      $table->date('data_emissao')->nullable();
      $table->decimal('valor_bruto', 12, 2)->default(0);
      $table->decimal('total_desconto', 12, 2)->default(0);
      $table->decimal('valor_total', 12, 2)->default(0);
      $table->string('status')->default('ativa');
      $table->foreignUuid('usuario_responsavel_id')->constrained('usuarios')->cascadeOnDelete();
      $table->timestamps();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('notas_fiscais');
  }
};
