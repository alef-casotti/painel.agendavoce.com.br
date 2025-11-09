<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Ticket;
use Carbon\Carbon;
use App\Models\Pagamento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    /**
     * Exibir o dashboard principal
     */
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $selectedFilterMonth = $request->input('filtro_mes');
        $selectedFilterYear = $request->input('filtro_ano');

        $now = now();

        $selectedFilterMonth = $selectedFilterMonth ?: $now->format('m');
        $selectedFilterYear = $selectedFilterYear ?: $now->format('Y');

        try {
            $periodStart = Carbon::createFromDate((int) $selectedFilterYear, (int) $selectedFilterMonth, 1)
                ->startOfMonth();
        } catch (\Throwable $exception) {
            Log::warning('Invalid dashboard period filters provided.', [
                'month' => $selectedFilterMonth,
                'year' => $selectedFilterYear,
                'message' => $exception->getMessage(),
            ]);

            $periodStart = $now->copy()->startOfMonth();
            $selectedFilterMonth = $periodStart->format('m');
            $selectedFilterYear = $periodStart->format('Y');
        }

        $periodEnd = $periodStart->copy()->endOfMonth();

        // Estatísticas de atendimento (apenas para admin)
        $estatisticas = null;
        $recalculatedPeriod = false;
        if ($user->isAdmin()) {
            $estatisticas = $this->getEstatisticasAtendimento($periodStart, $periodEnd);
        }

        $financeMetrics = null;
        $financeMetricsHighlights = [];
        $financeMetricsPeriodLabel = null;
        $financeMetricsError = null;
        $dashboardPeriodLabel = $periodStart->format('m/Y');

        if ($user->isAdmin() || $user->isFinanceiro()) {
            $filters = [
                'mes' => $periodStart->format('Y-m'),
            ];

            $financeMetricsResponse = $this->fetchFinanceMetrics($filters);

            $financeMetrics = $financeMetricsResponse['metrics'];
            $financeMetricsError = $financeMetricsResponse['error'];

            if ($financeMetrics) {
                $financeMetricsHighlights = $this->buildFinanceHighlights($financeMetrics);
                $financeMetricsPeriodLabel = $this->formatFinanceFilters($financeMetricsResponse['filters']);

                $apiAppliedFilters = $financeMetricsResponse['filters'] ?? null;
                if ($apiAppliedFilters) {
                    if (!empty($apiAppliedFilters['mes'])) {
                        [$apiYear, $apiMonth] = explode('-', $apiAppliedFilters['mes']) + [null, null];
                        if ($apiYear && $apiMonth && ($apiYear !== $selectedFilterYear || $apiMonth !== $selectedFilterMonth)) {
                            $selectedFilterYear = $apiYear;
                            $selectedFilterMonth = $apiMonth;
                            $periodStart = Carbon::createFromDate((int) $selectedFilterYear, (int) $selectedFilterMonth, 1)->startOfMonth();
                            $periodEnd = $periodStart->copy()->endOfMonth();
                            $recalculatedPeriod = true;
                        }
                    } elseif (!empty($apiAppliedFilters['data_inicio'])) {
                        try {
                            $apiStart = Carbon::parse($apiAppliedFilters['data_inicio'])->startOfMonth();
                            if ($apiStart->format('Y') !== $selectedFilterYear || $apiStart->format('m') !== $selectedFilterMonth) {
                                $periodStart = $apiStart;
                                $periodEnd = $apiAppliedFilters['data_fim']
                                    ? Carbon::parse($apiAppliedFilters['data_fim'])->endOfDay()
                                    : $apiStart->copy()->endOfMonth();
                                $selectedFilterYear = $periodStart->format('Y');
                                $selectedFilterMonth = $periodStart->format('m');
                                $recalculatedPeriod = true;
                            }
                        } catch (\Throwable $exception) {
                            Log::warning('Unable to parse API financial filters.', [
                                'filters' => $apiAppliedFilters,
                                'message' => $exception->getMessage(),
                            ]);
                        }
                    }
                }
            }

            if ($financeMetricsPeriodLabel) {
                $dashboardPeriodLabel = $financeMetricsPeriodLabel;
            }
        }

        $financeExpenseSummary = null;
        if ($user->isAdmin() || $user->isFinanceiro()) {
            $financeExpenseSummary = $this->getDespesasFixasVariaveis($periodStart, $periodEnd);

            if (!isset($financeMetricsHighlights['health'])) {
                $financeMetricsHighlights['health'] = [];
            }

            $financeMetricsHighlights['health']['burn_rate'] = $this->buildBurnRateHighlight(
                $financeExpenseSummary,
                $financeMetrics
            );

            $financeMetricsHighlights['health']['expenses_total'] = $this->buildTotalExpensesHighlight(
                $financeExpenseSummary
            );

            $financeMetricsHighlights['health']['net_profit'] = $this->buildNetProfitHighlight(
                $financeExpenseSummary,
                $financeMetrics
            );
        }

        if ($recalculatedPeriod && $user->isAdmin()) {
            $estatisticas = $this->getEstatisticasAtendimento($periodStart, $periodEnd);
        }

        $selectedFilterMonth = $periodStart->format('m');
        $selectedFilterYear = $periodStart->format('Y');

        $selectedFilterMonth = str_pad((string) $selectedFilterMonth, 2, '0', STR_PAD_LEFT);
        $selectedFilterYear = (string) $selectedFilterYear;

        $financeMetricsHighlights = array_replace([
            'health' => [],
            'growth' => [],
            'acquisition' => [],
        ], $financeMetricsHighlights);

        return view('dashboard', [
            'user' => $user,
            'estatisticas' => $estatisticas,
            'financeMetrics' => $financeMetrics,
            'financeMetricsHighlights' => $financeMetricsHighlights,
            'financeMetricsPeriodLabel' => $financeMetricsPeriodLabel,
            'financeMetricsError' => $financeMetricsError,
            'dashboardPeriodLabel' => $dashboardPeriodLabel,
            'periodStart' => $periodStart,
            'periodEnd' => $periodEnd,
            'financeExpenseSummary' => $financeExpenseSummary,
            'selectedFilterMonth' => $selectedFilterMonth,
            'selectedFilterYear' => $selectedFilterYear,
        ]);
    }

    /**
     * Obter estatísticas de atendimento
     */
    private function getEstatisticasAtendimento(Carbon $inicio, Carbon $fim)
    {
        // Quem atendeu mais tickets
        $atendentes = Ticket::whereNotNull('user_id')
            ->whereBetween('created_at', [$inicio, $fim])
            ->select('user_id', DB::raw('count(*) as total'))
            ->with('atendente:id,name')
            ->groupBy('user_id')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($ticket) {
                return [
                    'nome' => $ticket->atendente->name ?? 'Desconhecido',
                    'total' => $ticket->total
                ];
            });

        // Tempo médio de resposta (em minutos)
        // Para cada mensagem do suporte, calcula o tempo desde a última mensagem do cliente
        $mensagensSuporte = Message::where('sender_type', 'suporte')
            ->whereNotNull('user_id')
            ->whereBetween('created_at', [$inicio, $fim])
            ->with('ticket.messages')
            ->get();
        
        $temposResposta = [];
        foreach ($mensagensSuporte as $msgSuporte) {
            $ultimaMsgCliente = $msgSuporte->ticket->messages
                ->where('sender_type', 'cliente')
                ->where('created_at', '<', $msgSuporte->created_at)
                ->sortByDesc('created_at')
                ->first();
            
            if ($ultimaMsgCliente) {
                $temposResposta[] = $msgSuporte->created_at->diffInMinutes($ultimaMsgCliente->created_at);
            }
        }
        
        $tempoMedioResposta = count($temposResposta) > 0 
            ? array_sum($temposResposta) / count($temposResposta) 
            : 0;

        // Total de tickets atendidos
        $totalTicketsAtendidos = Ticket::whereNotNull('user_id')
            ->whereBetween('created_at', [$inicio, $fim])
            ->count();

        // Total de mensagens respondidas
        $totalMensagensRespondidas = Message::where('sender_type', 'suporte')
            ->whereNotNull('user_id')
            ->whereBetween('created_at', [$inicio, $fim])
            ->count();

        // Quem respondeu mais mensagens
        $respondentes = Message::where('sender_type', 'suporte')
            ->whereNotNull('user_id')
            ->whereBetween('created_at', [$inicio, $fim])
            ->select('user_id', DB::raw('count(*) as total'))
            ->with('usuario:id,name')
            ->groupBy('user_id')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($message) {
                return [
                    'nome' => $message->usuario->name ?? 'Desconhecido',
                    'total' => $message->total
                ];
            });

        // Tempo médio de resposta por atendente
        $tempoPorAtendente = [];
        foreach ($mensagensSuporte as $msgSuporte) {
            if (!$msgSuporte->usuario) continue;
            
            $ultimaMsgCliente = $msgSuporte->ticket->messages
                ->where('sender_type', 'cliente')
                ->where('created_at', '<', $msgSuporte->created_at)
                ->sortByDesc('created_at')
                ->first();
            
            if ($ultimaMsgCliente) {
                $userId = $msgSuporte->user_id;
                $nome = $msgSuporte->usuario->name ?? 'Desconhecido';
                $tempo = $msgSuporte->created_at->diffInMinutes($ultimaMsgCliente->created_at);
                
                if (!isset($tempoPorAtendente[$userId])) {
                    $tempoPorAtendente[$userId] = [
                        'nome' => $nome,
                        'tempos' => []
                    ];
                }
                $tempoPorAtendente[$userId]['tempos'][] = $tempo;
            }
        }
        
        $tempoMedioPorAtendente = collect($tempoPorAtendente)
            ->map(function ($item) {
                return [
                    'nome' => $item['nome'],
                    'tempo_medio' => count($item['tempos']) > 0 
                        ? round(array_sum($item['tempos']) / count($item['tempos']), 2)
                        : 0
                ];
            })
            ->sortBy('tempo_medio')
            ->take(5)
            ->values();

        return [
            'atendentes' => $atendentes,
            'tempo_medio_resposta' => round($tempoMedioResposta, 2),
            'total_tickets_atendidos' => $totalTicketsAtendidos,
            'total_mensagens_respondidas' => $totalMensagensRespondidas,
            'respondentes' => $respondentes,
            'tempo_medio_por_atendente' => $tempoMedioPorAtendente,
        ];
    }

    /**
     * Buscar métricas financeiras na API externa
     */
    private function fetchFinanceMetrics(array $filters = []): array
    {
        $baseUrl = rtrim(config('services.financeiro.base_url') ?? env('API_AGENDA_VOCE_URL'), '/');
        $apiKey = config('services.financeiro.api_key') ?? env('API_AGENDA_VOCE_KEY');

        if (!$baseUrl || !$apiKey) {
            return [
                'metrics' => null,
                'filters' => null,
                'error' => 'Configuração da API financeira não encontrada. Verifique as variáveis de ambiente.',
            ];
        }

        try {
            $response = Http::timeout(10)
                ->withToken($apiKey)
                ->acceptJson()
                ->get("{$baseUrl}/api/financeiro/metricas", array_filter($filters));

            if ($response->successful()) {
                return [
                    'metrics' => $response->json('data'),
                    'filters' => $response->json('applied_filters'),
                    'error' => null,
                ];
            }

            Log::warning('Finance metrics API returned an error response', [
                'status' => $response->status(),
                'body' => $response->json(),
            ]);

            return [
                'metrics' => null,
                'filters' => null,
                'error' => 'Não foi possível carregar as métricas financeiras no momento.',
            ];
        } catch (\Throwable $exception) {
            Log::error('Finance metrics API request failed', [
                'message' => $exception->getMessage(),
            ]);

            return [
                'metrics' => null,
                'filters' => null,
                'error' => 'Erro ao conectar com a API de métricas financeiras.',
            ];
        }
    }

    /**
     * Construir os destaques para exibição no card financeiro
     */
    private function buildFinanceHighlights(array $metrics): array
    {
        $currency = data_get($metrics, 'mrr.currency', 'BRL');
        $growthPercent = data_get($metrics, 'mrr.liquido.crescimento_percentual');
        $growthFormatted = $growthPercent !== null
            ? number_format((float) $growthPercent, 2, ',', '.') . '%'
            : 'N/A';

        return [
            'health' => [
                'mrr' => [
                    'label' => 'MRR Líquido',
                    'value' => $this->formatCurrency(data_get($metrics, 'mrr.liquido.valor'), $currency),
                    'description' => 'Receita recorrente mensal líquida.',
                    'meta' => [
                        'label' => 'Crescimento de MRR',
                        'value' => $growthFormatted,
                        'hint' => 'Meta saudável: +5% a +15% por mês.',
                    ],
                ],
                'churn' => [
                    'label' => 'Churn Rate',
                    'value' => $this->formatPercentage(
                        $this->defaultZeroIfNull(data_get($metrics, 'churn.clientes.taxa_percentual'))
                    ),
                    'description' => 'Percentual de clientes que cancelaram no período.',
                    'meta' => [
                        'label' => 'Receita perdida (Revenue Churn)',
                        'value' => $this->formatCurrency(
                            (float) data_get($metrics, 'churn.receita.receita_perdida', 0),
                            $currency
                        ),
                        'hint' => 'Ideal: < 5% de churn de clientes por mês.',
                    ],
                ],
                'receita_liquida' => [
                    'label' => 'Receita Líquida',
                    'value' => $this->formatCurrency(data_get($metrics, 'receita.total.liquida'), $currency),
                    'description' => 'Receita total líquida disponível no período.',
                    'meta' => [
                        'label' => 'Assinaturas | Agendamentos',
                        'value' => sprintf(
                            '%s | %s',
                            $this->formatCurrency(data_get($metrics, 'receita.assinaturas.liquida'), $currency),
                            $this->formatCurrency(data_get($metrics, 'receita.agendamentos.liquida'), $currency)
                        ),
                        'hint' => 'Receitas líquidas por fonte de receita recorrente vs agendamentos.',
                    ],
                ],
            ],
            'growth' => [
                'clientes_ativos' => [
                    'label' => 'Clientes Ativos',
                    'value' => $this->formatInteger(data_get($metrics, 'clientes_ativos.total')),
                    'description' => 'Clientes ativos no período atual.',
                ],
                'arpu' => [
                    'label' => 'ARPU',
                    'value' => $this->formatCurrency(
                        $this->defaultZeroIfNull(data_get($metrics, 'arpu.valor')),
                        data_get($metrics, 'arpu.currency', $currency)
                    ),
                    'description' => 'Ticket médio mensal.',
                ],
            ],
            'acquisition' => [
                'novos_clientes' => [
                    'label' => 'Novos Clientes',
                    'value' => $this->formatInteger($this->defaultZeroIfNull(data_get($metrics, 'novos_clientes.total'))),
                    'description' => 'Total de novos clientes adquiridos no período.',
                ],
                'cac' => [
                    'label' => 'CAC (Custo por Aquisição)',
                    'value' => $this->formatCurrency(
                        $this->calculateCac($metrics),
                        data_get($metrics, 'mrr.currency', $currency)
                    ),
                    'description' => 'Quanto custa adquirir um novo cliente.',
                    'meta' => [
                        'label' => 'Marketing | Novos Clientes',
                        'value' => sprintf(
                            '%s | %s',
                            $this->formatCurrency($this->defaultZeroIfNull(data_get($metrics, 'marketing.gastos'))),
                            $this->formatInteger($this->defaultZeroIfNull(data_get($metrics, 'novos_clientes.total')))
                        ),
                        'hint' => 'CAC = gastos em marketing ÷ novos clientes.',
                    ],
                ],
                'trial_conversion' => [
                    'label' => 'Taxa de Conversão (Trial)',
                    'value' => $this->formatPercentage(
                        $this->defaultZeroIfNull(data_get($metrics, 'trial_conversion.taxa_percentual'))
                    ),
                    'description' => 'Percentual de trials convertidos em clientes pagantes.',
                    'meta' => [
                        'label' => 'Trials | Convertidos',
                        'value' => sprintf(
                            '%s | %s',
                            $this->formatInteger($this->defaultZeroIfNull(data_get($metrics, 'trial_conversion.usuarios_trial'))),
                            $this->formatInteger($this->defaultZeroIfNull(data_get($metrics, 'trial_conversion.convertidos')))
                        ),
                        'hint' => 'Meta de conversão saudável: ' . (data_get($metrics, 'trial_conversion.meta_referencia_percentual') ?? '10-25%'),
                    ],
                ],
            ],
        ];
    }

    /**
     * Card de Burn Rate
     */
    private function buildBurnRateHighlight(?array $expenses, ?array $metrics): array
    {
        $totalExpenses = ($expenses['fixas'] ?? 0) + ($expenses['variaveis'] ?? 0);
        $liquidRevenue = $metrics ? (float) data_get($metrics, 'receita.total.liquida', 0) : 0;
        $burnRate = (float) $totalExpenses - (float) $liquidRevenue;

        return [
            'label' => '💸 Burn Rate',
            'description' => 'Quanto foi gasto além da receita líquida no período.',
            'value' => $this->formatCurrency($burnRate),
            'meta' => [
                'label' => 'Receita Líquida',
                'value' => $this->formatCurrency($liquidRevenue),
                'hint' => 'Se o burn rate permanecer positivo, você está queimando caixa neste período.',
            ],
            'style' => $burnRate > 0 ? 'negative' : 'positive',
        ];
    }

    /**
     * Card de Despesas Totais
     */
    private function buildTotalExpensesHighlight(?array $expenses): array
    {
        $fixed = $expenses['fixas'] ?? 0;
        $variable = $expenses['variaveis'] ?? 0;
        $total = (float) $fixed + (float) $variable;

        return [
            'label' => 'Despesas Fixas vs Variáveis',
            'description' => 'Total investido para manter a operação no período.',
            'value' => $this->formatCurrency($total),
            'meta' => [
                'label' => 'Detalhes',
                'value' => sprintf(
                    'Fixas: %s | Variáveis: %s',
                    $this->formatCurrency($fixed),
                    $this->formatCurrency($variable)
                ),
                'hint' => 'Acompanhe a proporção entre custos fixos e variáveis para otimizar o caixa.',
            ],
            'style' => 'neutral',
        ];
    }

    /**
     * Formatar o período aplicado para exibição
     */
    private function formatFinanceFilters(?array $filters): ?string
    {
        if (empty($filters)) {
            return null;
        }

        $start = $filters['data_inicio'] ?? null;
        $end = $filters['data_fim'] ?? null;
        $month = $filters['mes'] ?? null;

        if ($start && $end) {
            try {
                $startDate = Carbon::parse($start)->format('d/m/Y');
                $endDate = Carbon::parse($end)->format('d/m/Y');

                return "{$startDate} - {$endDate}";
            } catch (\Throwable $exception) {
                Log::warning('Não foi possível formatar o período das métricas financeiras.', [
                    'filters' => $filters,
                    'message' => $exception->getMessage(),
                ]);
            }
        }

        if ($month) {
            try {
                $date = Carbon::createFromFormat('Y-m', $month);

                return $date->format('m/Y');
            } catch (\Throwable $exception) {
                Log::warning('Não foi possível formatar o mês das métricas financeiras.', [
                    'filters' => $filters,
                    'message' => $exception->getMessage(),
                ]);
            }
        }

        return null;
    }

    /**
     * Formatar valores monetários para exibição
     */
    private function formatCurrency(?float $value, ?string $currency = 'BRL'): string
    {
        if ($value === null) {
            return 'N/A';
        }

        $symbol = $currency === 'BRL' ? 'R$' : $currency;

        return sprintf('%s %s', $symbol, number_format($value, 2, ',', '.'));
    }

    /**
     * Formatar números inteiros para exibição
     */
    private function formatInteger($value): string
    {
        if ($value === null) {
            return 'N/A';
        }

        return number_format((int) $value, 0, '', '.');
    }

    /**
     * Formatar percentuais para exibição
     */
    private function formatPercentage($value): string
    {
        if ($value === null) {
            return '0,00%';
        }

        return number_format((float) $value, 2, ',', '.') . '%';
    }

    private function calculateCac(array $metrics): float
    {
        $investimentoMarketing = $this->defaultZeroIfNull(data_get($metrics, 'marketing.gastos'));
        $novosClientes = $this->defaultZeroIfNull(data_get($metrics, 'novos_clientes.total'));

        if ($novosClientes <= 0) {
            return $investimentoMarketing;
        }

        return (float) $investimentoMarketing / (float) $novosClientes;
    }

    /**
     * Buscar resumo de despesas fixas e variáveis no banco
     */
    private function defaultZeroIfNull($value): float
    {
        return $value === null ? 0.0 : (float) $value;
    }

    /**
     * Buscar resumo de despesas fixas e variáveis no banco
     */
    private function getDespesasFixasVariaveis(Carbon $inicio, Carbon $fim): ?array
    {
        $pagamentos = Pagamento::query()
            ->with('categoria')
            ->whereBetween('data_pagamento', [$inicio->toDateString(), $fim->toDateString()])
            ->get();

        $totais = $pagamentos->groupBy(function ($pagamento) {
            return $pagamento->categoria?->tipo ?? 'variavel';
        })->map(function ($grupo) {
            return $grupo->sum(function ($pagamento) {
                $valor = $pagamento->valor_pago ?? $pagamento->valor_previsto ?? 0;
                return (float) $valor;
            });
        });

        return [
            'fixas' => (float) $totais->get('fixa', 0),
            'variaveis' => (float) $totais->get('variavel', 0),
            'quantidade_registros' => $pagamentos->count(),
        ];
    }

    /**
     * Card de Lucro Líquido
     */
    private function buildNetProfitHighlight(?array $expenses, ?array $metrics): array
    {
        $totalExpenses = ($expenses['fixas'] ?? 0) + ($expenses['variaveis'] ?? 0);
        $liquidRevenue = $metrics ? (float) data_get($metrics, 'receita.total.liquida', 0) : 0;
        $netProfit = $liquidRevenue - (float) $totalExpenses;

        return [
            'label' => '🧮 Lucro Líquido',
            'description' => 'Receita líquida menos todas as despesas registradas no período.',
            'value' => $this->formatCurrency($netProfit),
            'meta' => [
                'label' => 'Receita Líquida',
                'value' => $this->formatCurrency($liquidRevenue),
                'hint' => 'Lucro positivo indica geração de caixa. Negativo indica prejuízo no período.',
            ],
            'style' => $netProfit >= 0 ? 'positive' : 'negative',
        ];
    }
}
