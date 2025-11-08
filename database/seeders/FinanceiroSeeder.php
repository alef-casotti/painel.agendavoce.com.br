<?php

namespace Database\Seeders;

use App\Models\CentroCusto;
use App\Models\PagamentoCategoria;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class FinanceiroSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categorias = [
            ['nome' => 'Serviços em Nuvem', 'tipo' => 'fixa', 'descricao' => 'Gastos recorrentes com infraestrutura e servidores', 'padrao' => true],
            ['nome' => 'Marketing e Aquisição', 'tipo' => 'variavel', 'descricao' => 'Investimentos para aquisição de clientes'],
            ['nome' => 'Ferramentas e Softwares', 'tipo' => 'fixa', 'descricao' => 'Assinaturas e ferramentas de apoio'],
            ['nome' => 'Financeiro e Bancário', 'tipo' => 'variavel', 'descricao' => 'Tarifas bancárias e custos financeiros'],
        ];

        foreach ($categorias as $categoria) {
            PagamentoCategoria::updateOrCreate(
                ['slug' => Str::slug($categoria['nome'])],
                $categoria
            );
        }

        $centrosCusto = [
            ['nome' => 'Operações'],
            ['nome' => 'Tecnologia'],
            ['nome' => 'Marketing'],
            ['nome' => 'Administrativo'],
        ];

        foreach ($centrosCusto as $centro) {
            CentroCusto::updateOrCreate(
                ['slug' => Str::slug($centro['nome'])],
                $centro
            );
        }
    }
}

