<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conteudos', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->text('descricao')->nullable();
            $table->string('criador')->nullable();
            $table->string('plataforma')->default('outro');
            $table->date('data_publicacao');
            $table->time('horario')->nullable();
            $table->string('status')->default('agendado');
            $table->string('link')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();

            $table->index('data_publicacao');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conteudos');
    }
};
