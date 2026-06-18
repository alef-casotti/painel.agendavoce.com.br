<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Ticket;
use App\Services\AgendaVoceUsuarioService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct(
        private readonly AgendaVoceUsuarioService $usuarioService
    ) {
    }
    /**
     * Exibir o dashboard principal
     */
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $periodStart = now()->copy()->startOfMonth();
        $periodEnd = now()->copy()->endOfMonth();

        // Estatísticas de atendimento (apenas para admin)
        $estatisticas = null;
        if ($user->isAdmin()) {
            $estatisticas = $this->getEstatisticasAtendimento($periodStart, $periodEnd);
        }

        $usuarioStats = null;
        $usuarioStatsHighlights = [];
        $usuarioStatsError = null;

        $usuarioStatsResponse = $this->usuarioService->fetchStats();
        $usuarioStats = $usuarioStatsResponse['data'];
        $usuarioStatsError = $usuarioStatsResponse['error'];

        if ($usuarioStats) {
            $usuarioStatsHighlights = $this->buildUsuarioStatsHighlights($usuarioStats);
        }

        return view('dashboard', [
            'user' => $user,
            'estatisticas' => $estatisticas,
            'usuarioStats' => $usuarioStats,
            'usuarioStatsHighlights' => $usuarioStatsHighlights,
            'usuarioStatsError' => $usuarioStatsError,
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
     * Construir os destaques para exibição das estatísticas de clientes
     */
    private function buildUsuarioStatsHighlights(array $stats): array
    {
        return [
            'total_clientes' => [
                'label' => 'Total de Clientes',
                'value' => $this->formatInteger($this->defaultZeroIfNull(data_get($stats, 'total_clientes'))),
            ],
            'em_operacao' => [
                'label' => 'Em Operação',
                'quantidade' => $this->formatInteger($this->defaultZeroIfNull(data_get($stats, 'em_operacao.quantidade'))),
                'valor' => $this->formatCurrency($this->defaultZeroIfNull(data_get($stats, 'em_operacao.valor'))),
            ],
            'em_trial' => [
                'label' => 'Em Trial',
                'quantidade' => $this->formatInteger($this->defaultZeroIfNull(data_get($stats, 'em_trial.quantidade'))),
                'valor' => $this->formatCurrency($this->defaultZeroIfNull(data_get($stats, 'em_trial.valor'))),
            ],
            'canceladas' => [
                'label' => 'Canceladas',
                'quantidade' => $this->formatInteger($this->defaultZeroIfNull(data_get($stats, 'canceladas.quantidade'))),
                'valor' => $this->formatCurrency($this->defaultZeroIfNull(data_get($stats, 'canceladas.valor'))),
            ],
            'total_geral' => [
                'label' => 'Total Geral',
                'quantidade' => $this->formatInteger($this->defaultZeroIfNull(data_get($stats, 'total_geral.quantidade'))),
                'valor' => $this->formatCurrency($this->defaultZeroIfNull(data_get($stats, 'total_geral.valor'))),
            ],
        ];
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

    private function defaultZeroIfNull($value): float
    {
        return $value === null ? 0.0 : (float) $value;
    }

}
