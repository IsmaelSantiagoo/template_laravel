<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('telefones_cliente', function (Blueprint $table) {
      $table->uuid('id')->primary();
      $table->string('numero')->nullable();
      $table->foreignUuid('cliente_id')->constrained('clientes')->cascadeOnDelete();
      $table->boolean('principal')->default(false);
      $table->foreignUuid('usuario_responsavel_id')->constrained('usuarios')->cascadeOnDelete();
      $table->timestamps();

      $table->unique(['cliente_id', 'numero']);
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('telefones_cliente');
  }
};
