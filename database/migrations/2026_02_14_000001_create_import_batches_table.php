<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('import_batches', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained('usuarios')->cascadeOnDelete();
            $table->string('type');
            $table->string('status');
            $table->unsignedInteger('total_rows')->default(0);
            $table->unsignedInteger('processed_rows')->default(0);
            $table->unsignedTinyInteger('percentage')->default(0);
            $table->text('last_log')->nullable();
            $table->string('current_step')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('import_batches');
    }
};
