<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('produtos', function (Blueprint $table) {
      $table->uuid('id')->primary();
      $table->string('codigo')->unique();
      $table->string('nome');
      $table->string('descricao')->nullable();
      $table->integer('quantidade')->nullable();
      $table->foreignUuid('tipo_marca_id')->nullable()->constrained('tipos_marca')->nullOnDelete();
      $table->foreignUuid('embalagem_id')->nullable()->constrained('embalagens')->nullOnDelete();
      $table->string('ean')->nullable();
      $table->foreignUuid('usuario_responsavel_id')->constrained('usuarios')->cascadeOnDelete();
      $table->timestamps();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('produtos');
  }
};
