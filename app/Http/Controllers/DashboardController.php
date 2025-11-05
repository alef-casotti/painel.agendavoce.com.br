<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Exibir o dashboard principal
     */
    public function index()
    {
        $user = auth()->user();
        
        // Estatísticas de atendimento (apenas para admin)
        $estatisticas = null;
        if ($user->isAdmin()) {
            $estatisticas = $this->getEstatisticasAtendimento();
        }
        
        return view('dashboard', compact('user', 'estatisticas'));
    }

    /**
     * Obter estatísticas de atendimento
     */
    private function getEstatisticasAtendimento()
    {
        // Quem atendeu mais tickets
        $atendentes = Ticket::whereNotNull('user_id')
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
        $totalTicketsAtendidos = Ticket::whereNotNull('user_id')->count();

        // Total de mensagens respondidas
        $totalMensagensRespondidas = Message::where('sender_type', 'suporte')
            ->whereNotNull('user_id')
            ->count();

        // Quem respondeu mais mensagens
        $respondentes = Message::where('sender_type', 'suporte')
            ->whereNotNull('user_id')
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
}
