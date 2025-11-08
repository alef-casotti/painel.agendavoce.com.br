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
        Schema::create('pagamentos', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->text('descricao')->nullable();
            $table->foreignId('categoria_id')
                ->nullable()
                ->constrained('pagamento_categorias')
                ->nullOnDelete();
            $table->foreignId('centro_custo_id')
                ->nullable()
                ->constrained('centros_custo')
                ->nullOnDelete();
            $table->string('fornecedor')->nullable();
            $table->string('documento_referencia')->nullable();
            $table->decimal('valor_previsto', 12, 2);
            $table->decimal('valor_pago', 12, 2)->nullable();
            $table->date('data_competencia')->nullable();
            $table->date('data_vencimento')->nullable();
            $table->date('data_pagamento')->nullable();
            $table->string('status')->default('pendente');
            $table->string('metodo_pagamento')->nullable();
            $table->boolean('recorrente')->default(false);
            $table->unsignedInteger('parcela_atual')->nullable();
            $table->unsignedInteger('parcelas_total')->nullable();
            $table->json('metadados')->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'data_vencimento']);
            $table->index('recorrente');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagamentos');
    }
};

