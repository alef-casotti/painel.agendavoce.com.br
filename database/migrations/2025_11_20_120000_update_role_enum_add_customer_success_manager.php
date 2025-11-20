<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Adiciona o papel Customer Success Manager ao enum de usuários.
     */
    public function up(): void
    {
        DB::statement("
            ALTER TABLE users
            MODIFY role ENUM('admin', 'financeiro', 'suporte', 'customer_success_manager')
            DEFAULT 'suporte'
        ");
    }

    /**
     * Reverte o enum para os papéis anteriores.
     */
    public function down(): void
    {
        DB::statement("
            ALTER TABLE users
            MODIFY role ENUM('admin', 'financeiro', 'suporte')
            DEFAULT 'suporte'
        ");
    }
};

