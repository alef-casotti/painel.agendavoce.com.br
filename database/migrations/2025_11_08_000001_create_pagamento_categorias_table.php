<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pagamento_categorias', function (Blueprint $table) {
            $table->id();
            $table->string('nome')->unique();
            $table->string('slug')->unique();
            $table->string('tipo')->default('variavel');
            $table->string('descricao')->nullable();
            $table->boolean('ativo')->default(true);
            $table->boolean('padrao')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagamento_categorias');
    }
};

