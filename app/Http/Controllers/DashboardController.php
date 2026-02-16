<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Ticket;
use Carbon\Carbon;
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
        $growthPercent = data_get($metrics, 'mrr.crescimento_percentual');
        $growthFormatted = $growthPercent !== null
            ? number_format((float) $growthPercent, 2, ',', '.') . '%'
            : 'N/A';

        return [
            'health' => [
                'mrr' => [
                    'label' => 'MRR Bruto',
                    'value' => $this->formatCurrency(data_get($metrics, 'mrr.valor'), $currency),
                    'description' => 'Receita recorrente mensal bruta.',
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
            ],
            'growth' => [
                'clientes_ativos' => [
                    'label' => 'Clientes Ativos',
                    'value' => $this->formatInteger(data_get($metrics, 'clientes_ativos.total')),
                    'description' => 'Clientes ativos no período atual.',
                ],
            ],
            'acquisition' => [
                'novos_clientes' => [
                    'label' => 'Novos Clientes',
                    'value' => $this->formatInteger($this->defaultZeroIfNull(data_get($metrics, 'novos_clientes.total'))),
                    'description' => 'Total de novos clientes adquiridos no período.',
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
                            $this->formatInteger($this->defaultZeroIfNull(data_get($metrics, 'trial_conversion.trial_encerrados_no_periodo'))),
                            $this->formatInteger($this->defaultZeroIfNull(data_get($metrics, 'trial_conversion.convertidos')))
                        ),
                        'hint' => 'Meta de conversão saudável: ' . (data_get($metrics, 'trial_conversion.meta_referencia_percentual') ?? '10-25%'),
                    ],
                ],
            ],
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

    private function defaultZeroIfNull($value): float
    {
        return $value === null ? 0.0 : (float) $value;
    }

}
