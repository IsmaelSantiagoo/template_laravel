<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anexos_avaria', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('path');
            $table->foreignUuid('avaria_id')->nullable()->constrained('avarias')->nullOnDelete();
            $table->foreignUuid('usuario_responsavel_id')->constrained('usuarios')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anexos_avaria');
    }
};
