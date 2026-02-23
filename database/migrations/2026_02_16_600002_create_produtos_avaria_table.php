<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produtos_avaria', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('avaria_id')->nullable()->constrained('avarias')->nullOnDelete();
            $table->foreignUuid('produto_id')->nullable()->constrained('produtos')->nullOnDelete();
            $table->enum('tipo_avaria', ['avariado', 'faltante', 'inversao'])->nullable();
            $table->integer('quantidade')->nullable();
            $table->foreignUuid('usuario_responsavel_id')->constrained('usuarios')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produtos_avaria');
    }
};
