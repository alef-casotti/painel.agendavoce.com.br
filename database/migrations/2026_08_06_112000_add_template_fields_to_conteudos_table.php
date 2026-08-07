<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('conteudos', function (Blueprint $table) {
            $table->string('tipo_conteudo')->default('carrossel')->after('plataforma');
            $table->string('modelo')->nullable()->after('tipo_conteudo');
            $table->json('slides')->nullable()->after('modelo');
            $table->longText('html_gerado')->nullable()->after('slides');
        });
    }

    public function down(): void
    {
        Schema::table('conteudos', function (Blueprint $table) {
            $table->dropColumn(['tipo_conteudo', 'modelo', 'slides', 'html_gerado']);
        });
    }
};
