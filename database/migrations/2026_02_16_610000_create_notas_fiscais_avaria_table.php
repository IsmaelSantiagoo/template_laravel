<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notas_fiscais_avaria', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('avaria_id')->nullable()->constrained('avarias')->nullOnDelete();
            $table->foreignUuid('nota_fiscal_id')->nullable()->constrained('notas_fiscais')->nullOnDelete();
            $table->foreignUuid('usuario_responsavel_id')->constrained('usuarios')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notas_fiscais_avaria');
    }
};
