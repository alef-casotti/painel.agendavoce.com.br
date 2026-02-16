<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ClienteController extends Controller
{
    /**
     * Lista principal de clientes com métricas resumidas.
     */
    public function index(Request $request)
    {
        // Buscar configurações da API
        $baseUrl = config('services.usuarios.base_url');
        $apiToken = config('services.usuarios.api_token');

        $filtroAtivo = $request->get('filtro', 'todos'); // 'todos', 'onboarding', 'risco'
        $busca = $request->get('busca', '');
        $statusFiltro = $request->get('status', ''); // Filtro por status (ativo, onboarding, risco)
        
        // Remover formatação do telefone se a busca parecer ser um telefone
        // Remove parênteses, traços, espaços e outros caracteres não numéricos
        if (!empty($busca)) {
            $buscaLimpa = preg_replace('/\D/', '', $busca);
            // Se após remover caracteres não numéricos, tiver 10 ou 11 dígitos, é provavelmente um telefone
            // Usar a versão limpa para buscar na API
            if (strlen($buscaLimpa) >= 10 && strlen($buscaLimpa) <= 11 && preg_match('/^\d+$/', $buscaLimpa)) {
                $busca = $buscaLimpa;
            }
        }
        
        $clientes = collect([]);
        $metrics = [
            [
                'label' => 'Total Clientes',
                'value' => 0,
                'trend' => 'Carregando...',
                'trend_positive' => true,
            ],
            [
                'label' => 'Onboardings em andamento',
                'value' => 0,
                'trend' => 'Carregando...',
                'trend_positive' => true,
            ],
            [
                'label' => 'Risco de churn',
                'value' => 0,
                'trend' => 'Carregando...',
                'trend_positive' => false,
            ],
        ];

        if (!$baseUrl || !$apiToken) {
            Log::warning('Configuração da API de usuários não encontrada');
            return view('clientes.index', compact('metrics', 'clientes', 'filtroAtivo', 'busca', 'statusFiltro'));
        }

        try {
            // Construir parâmetros para a API
            $params = [];
            if (!empty($busca)) {
                $params['busca'] = $busca;
            }
            
            $response = Http::timeout(10)
                ->withToken($apiToken)
                ->acceptJson()
                ->get("{$baseUrl}/api/usuarios", $params);

            if ($response->successful()) {
                $data = $response->json('data', []);
                $meta = $response->json('meta', []);

                // Transformar os dados da API para o formato esperado pela view
                $clientes = collect($data)->map(function ($usuario) {
                    // Determinar status baseado no plano e dados do endpoint
                    $status = 'ativo';
                    
                    // Onboarding: plano free sem agendamentos OU plano qualquer sem agendamentos mas com serviços cadastrados
                    if ($usuario['total_agendamentos'] === 0) {
                        if ($usuario['plano'] === 'free' || ($usuario['total_servicos'] > 0)) {
                            $status = 'onboarding';
                        } else {
                            // Risco: sem agendamentos e sem serviços
                            $status = 'risco';
                        }
                    }
                    
                    // Risco adicional: cliente com pouca ou nenhuma atividade
                    if ($usuario['total_agendamentos'] === 0 && $usuario['total_servicos'] === 0 && $usuario['total_clientes'] === 0) {
                        $status = 'risco';
                    }

                    // Formatar data em português
                    $meses = [
                        1 => 'Jan', 2 => 'Fev', 3 => 'Mar', 4 => 'Abr',
                        5 => 'Mai', 6 => 'Jun', 7 => 'Jul', 8 => 'Ago',
                        9 => 'Set', 10 => 'Out', 11 => 'Nov', 12 => 'Dez'
                    ];
                    $dataCarbon = Carbon::parse($usuario['data_criacao']);
                    $dataCriacao = $dataCarbon->format('d') . ' ' . $meses[$dataCarbon->month] . ' ' . $dataCarbon->format('Y');

                    return [
                        'name' => $usuario['nome'],
                        'owner' => $usuario['email'],
                        'segment' => $usuario['tipo_negocio_label'] ?? $usuario['tipo_negocio'],
                        'status' => $status,
                        'since' => $dataCriacao,
                        'mrr' => $this->formatarPlano($usuario['plano']),
                        'id' => $usuario['id'],
                        'telefone' => $this->formatarTelefone($usuario['telefone'] ?? ''),
                        'plano' => $usuario['plano'],
                        'total_agendamentos' => $usuario['total_agendamentos'] ?? 0,
                        'total_servicos' => $usuario['total_servicos'] ?? 0,
                        'total_clientes' => $usuario['total_clientes'] ?? 0,
                        'tem_pagina_publica' => $usuario['tem_pagina_publica'] ?? false,
                    ];
                });

                // Calcular métricas baseadas nos dados do endpoint
                $totalClientes = $meta['total'] ?? count($data);
                $clientesOnboarding = $clientes->where('status', 'onboarding')->count();
                $clientesRisco = $clientes->where('status', 'risco')->count();
                
                // Calcular métricas adicionais para trends
                $onboardingComServicos = $clientes->where('status', 'onboarding')
                    ->where('total_servicos', '>', 0)
                    ->count();
                
                $riscoSemAtividade = $clientes->where('status', 'risco')
                    ->where('total_servicos', 0)
                    ->where('total_clientes', 0)
                    ->count();
                
                // Gerar mensagens de trend baseadas nos dados reais
                $trendOnboarding = $this->gerarTrendOnboarding($onboardingComServicos, $clientesOnboarding);
                $trendRisco = $this->gerarTrendRisco($riscoSemAtividade, $clientesRisco);

                // Aplicar filtro de card (filtro) se solicitado
                if ($filtroAtivo !== 'todos') {
                    $clientes = $clientes->filter(function ($cliente) use ($filtroAtivo) {
                        return $cliente['status'] === $filtroAtivo;
                    })->values(); // Reindexar a coleção após filtrar
                }
                
                // Aplicar filtro de status (select) se solicitado
                if (!empty($statusFiltro)) {
                    $clientes = $clientes->filter(function ($cliente) use ($statusFiltro) {
                        return $cliente['status'] === $statusFiltro;
                    })->values();
                }

                // Ordenar por nome em ordem alfabética
                $clientes = $clientes->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)->values();

                $metrics = [
                    [
                        'label' => 'Total Clientes',
                        'value' => $totalClientes,
                        'trend' => $this->gerarTrendTotal($totalClientes),
                        'trend_positive' => true,
                    ],
                    [
                        'label' => 'Onboardings em andamento',
                        'value' => $clientesOnboarding,
                        'trend' => $trendOnboarding,
                        'trend_positive' => $clientesOnboarding > 0 && $onboardingComServicos > 0,
                    ],
                    [
                        'label' => 'Risco de churn',
                        'value' => $clientesRisco,
                        'trend' => $trendRisco,
                        'trend_positive' => false,
                    ],
                ];
            } else {
                Log::warning('API de usuários retornou erro', [
                    'status' => $response->status(),
                    'body' => $response->json(),
                ]);
            }
        } catch (\Throwable $exception) {
            Log::error('Erro ao buscar usuários da API', [
                'message' => $exception->getMessage(),
            ]);
        }

        return view('clientes.index', compact('metrics', 'clientes', 'filtroAtivo', 'busca', 'statusFiltro'));
    }

    /**
     * Formatar o plano para exibição
     */
    private function formatarPlano($plano)
    {
        $planos = [
            'free' => 'Plano Grátis',
            'pro' => 'Plano Pro',
            'premium' => 'Plano Premium',
        ];

        return $planos[$plano] ?? ucfirst($plano);
    }

    /**
     * Formatar telefone brasileiro
     */
    private function formatarTelefone($telefone)
    {
        if (empty($telefone)) {
            return '-';
        }

        // Remove todos os caracteres não numéricos
        $telefone = preg_replace('/\D/', '', $telefone);

        // Formata telefone celular (11 dígitos) ou fixo (10 dígitos)
        if (strlen($telefone) == 11) {
            // Celular: (XX) XXXXX-XXXX
            return '(' . substr($telefone, 0, 2) . ') ' . substr($telefone, 2, 5) . '-' . substr($telefone, 7);
        } elseif (strlen($telefone) == 10) {
            // Fixo: (XX) XXXX-XXXX
            return '(' . substr($telefone, 0, 2) . ') ' . substr($telefone, 2, 4) . '-' . substr($telefone, 6);
        }

        // Se não tiver 10 ou 11 dígitos, retorna como está
        return $telefone;
    }

    /**
     * Gerar mensagem de trend para total de clientes
     */
    private function gerarTrendTotal($totalClientes)
    {
        if ($totalClientes === 0) {
            return 'Nenhum cliente cadastrado';
        }
        
        // Por enquanto retorna uma mensagem genérica, pode ser melhorada quando tiver dados históricos
        return "Total de {$totalClientes} cliente(s)";
    }

    /**
     * Gerar mensagem de trend para onboardings baseada nos dados do endpoint
     */
    private function gerarTrendOnboarding($onboardingComServicos, $totalOnboarding)
    {
        if ($totalOnboarding === 0) {
            return 'Nenhum onboarding em andamento';
        }

        if ($onboardingComServicos > 0) {
            if ($onboardingComServicos === 1) {
                return "1 cliente pronto para primeiro agendamento";
            }
            return "{$onboardingComServicos} cliente(s) pronto(s) para primeiro agendamento";
        }

        return "{$totalOnboarding} cliente(s) em configuração inicial";
    }

    /**
     * Gerar mensagem de trend para risco de churn baseada nos dados do endpoint
     */
    private function gerarTrendRisco($riscoSemAtividade, $totalRisco)
    {
        if ($totalRisco === 0) {
            return 'Nenhum cliente em risco';
        }

        if ($riscoSemAtividade > 0) {
            if ($riscoSemAtividade === 1) {
                return "1 cliente sem atividade cadastrada";
            }
            return "{$riscoSemAtividade} cliente(s) sem atividade cadastrada";
        }

        if ($totalRisco === 1) {
            return "1 cliente precisa de follow-up";
        }

        return "{$totalRisco} cliente(s) precisam de follow-up";
    }

    /**
     * Exibir detalhes de um cliente específico
     */
    public function show($id)
    {
        // Buscar configurações da API
        $baseUrl = config('services.usuarios.base_url');
        $apiToken = config('services.usuarios.api_token');

        if (!$baseUrl || !$apiToken) {
            Log::warning('Configuração da API de usuários não encontrada');
            return redirect()->route('clientes.index')->with('error', 'Configuração da API não encontrada');
        }

        try {
            $response = Http::timeout(10)
                ->withToken($apiToken)
                ->acceptJson()
                ->get("{$baseUrl}/api/usuarios/{$id}");

            if ($response->successful()) {
                $cliente = $response->json('data', []);
                
                if (empty($cliente)) {
                    return redirect()->route('clientes.index')->with('error', 'Cliente não encontrado');
                }

                // Formatar dados para exibição
                $cliente = $this->formatarDadosCliente($cliente);

                return view('clientes.show', compact('cliente'));
            } else {
                if ($response->status() === 404) {
                    return redirect()->route('clientes.index')->with('error', 'Cliente não encontrado');
                }

                Log::warning('API de usuários retornou erro ao buscar detalhes', [
                    'status' => $response->status(),
                    'body' => $response->json(),
                ]);

                return redirect()->route('clientes.index')->with('error', 'Erro ao carregar detalhes do cliente');
            }
        } catch (\Throwable $exception) {
            Log::error('Erro ao buscar detalhes do cliente da API', [
                'message' => $exception->getMessage(),
                'cliente_id' => $id,
            ]);

            return redirect()->route('clientes.index')->with('error', 'Erro ao conectar com a API');
        }
    }

    /**
     * Formatar dados do cliente para exibição
     */
    private function formatarDadosCliente($cliente)
    {
        // Garantir que campos de texto sejam strings
        if (isset($cliente['tipo_negocio_label']) && is_array($cliente['tipo_negocio_label'])) {
            $cliente['tipo_negocio_label'] = is_string($cliente['tipo_negocio'] ?? '') 
                ? $cliente['tipo_negocio'] 
                : '-';
        }
        if (isset($cliente['tipo_negocio']) && is_array($cliente['tipo_negocio'])) {
            $cliente['tipo_negocio'] = '-';
        }
        
        // Formatar telefone
        $cliente['telefone_formatado'] = $this->formatarTelefone($cliente['telefone'] ?? '');

        // Formatar datas
        $meses = [
            1 => 'Jan', 2 => 'Fev', 3 => 'Mar', 4 => 'Abr',
            5 => 'Mai', 6 => 'Jun', 7 => 'Jul', 8 => 'Ago',
            9 => 'Set', 10 => 'Out', 11 => 'Nov', 12 => 'Dez'
        ];

        if (!empty($cliente['data_criacao'])) {
            $dataCarbon = Carbon::parse($cliente['data_criacao']);
            $cliente['data_criacao_formatada'] = $dataCarbon->format('d') . ' ' . $meses[$dataCarbon->month] . ' ' . $dataCarbon->format('Y');
        }

        if (!empty($cliente['data_atualizacao'])) {
            $dataCarbon = Carbon::parse($cliente['data_atualizacao']);
            $cliente['data_atualizacao_formatada'] = $dataCarbon->format('d') . ' ' . $meses[$dataCarbon->month] . ' ' . $dataCarbon->format('Y');
        }

        // Formatar assinatura ativa
        if (!empty($cliente['assinatura_ativa'])) {
            $assinatura = $cliente['assinatura_ativa'];
            if (!empty($assinatura['started_at'])) {
                $dataCarbon = Carbon::parse($assinatura['started_at']);
                $assinatura['started_at_formatada'] = $dataCarbon->format('d') . ' ' . $meses[$dataCarbon->month] . ' ' . $dataCarbon->format('Y');
            }
            if (!empty($assinatura['expires_at'])) {
                $dataCarbon = Carbon::parse($assinatura['expires_at']);
                $assinatura['expires_at_formatada'] = $dataCarbon->format('d') . ' ' . $meses[$dataCarbon->month] . ' ' . $dataCarbon->format('Y');
            }
            $cliente['assinatura_ativa'] = $assinatura;
        }

        // Formatar histórico de assinaturas
        if (!empty($cliente['historico_assinaturas'])) {
            foreach ($cliente['historico_assinaturas'] as &$historico) {
                if (!empty($historico['started_at'])) {
                    $dataCarbon = Carbon::parse($historico['started_at']);
                    $historico['started_at_formatada'] = $dataCarbon->format('d') . ' ' . $meses[$dataCarbon->month] . ' ' . $dataCarbon->format('Y');
                }
                if (!empty($historico['expires_at'])) {
                    $dataCarbon = Carbon::parse($historico['expires_at']);
                    $historico['expires_at_formatada'] = $dataCarbon->format('d') . ' ' . $meses[$dataCarbon->month] . ' ' . $dataCarbon->format('Y');
                }
                if (!empty($historico['canceled_at'])) {
                    $dataCarbon = Carbon::parse($historico['canceled_at']);
                    $historico['canceled_at_formatada'] = $dataCarbon->format('d') . ' ' . $meses[$dataCarbon->month] . ' ' . $dataCarbon->format('Y');
                }
                if (!empty($historico['criado_em'])) {
                    $dataCarbon = Carbon::parse($historico['criado_em']);
                    $historico['criado_em_formatada'] = $dataCarbon->format('d') . ' ' . $meses[$dataCarbon->month] . ' ' . $dataCarbon->format('Y');
                }
            }
        }

        // Formatar agendamentos recentes
        if (!empty($cliente['agendamentos_recentes'])) {
            foreach ($cliente['agendamentos_recentes'] as &$agendamento) {
                if (!empty($agendamento['data'])) {
                    $dataCarbon = Carbon::parse($agendamento['data']);
                    $agendamento['data_formatada'] = $dataCarbon->format('d') . ' ' . $meses[$dataCarbon->month] . ' ' . $dataCarbon->format('Y');
                }
                if (!empty($agendamento['criado_em'])) {
                    $dataCarbon = Carbon::parse($agendamento['criado_em']);
                    $agendamento['criado_em_formatada'] = $dataCarbon->format('d/m/Y H:i');
                }
            }
        }

        // Formatar horários de funcionamento
        if (!empty($cliente['horarios_funcionamento']) && is_array($cliente['horarios_funcionamento'])) {
            $diasSemana = [
                0 => 'Domingo',
                1 => 'Segunda-feira',
                2 => 'Terça-feira',
                3 => 'Quarta-feira',
                4 => 'Quinta-feira',
                5 => 'Sexta-feira',
                6 => 'Sábado'
            ];
            
            usort($cliente['horarios_funcionamento'], function($a, $b) {
                return ($a['dia_semana'] ?? 0) <=> ($b['dia_semana'] ?? 0);
            });
        }
        
        // Garantir que próximo_agendamento seja null se for array vazio ou não definido corretamente
        if (isset($cliente['engajamento']['proximo_agendamento']) && 
            is_array($cliente['engajamento']['proximo_agendamento']) && 
            empty($cliente['engajamento']['proximo_agendamento'])) {
            $cliente['engajamento']['proximo_agendamento'] = null;
        }

        return $cliente;
    }
}
