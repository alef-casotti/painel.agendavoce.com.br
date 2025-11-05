<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Ticket;
use Illuminate\Http\Request;

class SuporteController extends Controller
{
    /**
     * Exibir área de suporte - lista de tickets
     */
    public function index(Request $request)
    {
        $query = Ticket::with(['messages' => function($query) {
                $query->where('sender_type', 'suporte')
                      ->orderBy('created_at', 'desc');
            }])
            ->withCount([
                'messages as total_mensagens',
                'messages as mensagens_suporte' => function($query) {
                    $query->where('sender_type', 'suporte');
                },
                'messages as nao_visualizadas' => function($query) {
                    $query->where('sender_type', 'suporte')
                          ->whereNull('viewed_at');
                },
                'messages as mensagens_nao_respondidas' => function($query) {
                    $query->where('sender_type', 'cliente')
                          ->whereNull('answered_at');
                }
            ]);

        // Filtros
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        if ($request->has('email') && $request->email !== '') {
            $query->where('email', 'like', '%' . $request->email . '%');
        }

        if ($request->has('assunto') && $request->assunto !== '') {
            $query->where('assunto', 'like', '%' . $request->assunto . '%');
        }

        // Ordenação: 
        // 1. Alta prioridade com mensagens não respondidas
        // 2. Normal prioridade com mensagens não respondidas  
        // 3. Demais tickets (alta prioridade sem não respondidas)
        // 4. Demais tickets (normal prioridade sem não respondidas)
        // Sempre ordena por data de atualização dentro de cada grupo
        $tickets = $query->orderByRaw("
            CASE 
                WHEN prioridade = 'alta' AND mensagens_nao_respondidas > 0 THEN 0
                WHEN prioridade = 'normal' AND mensagens_nao_respondidas > 0 THEN 1
                WHEN prioridade = 'alta' THEN 2
                ELSE 3
            END
        ")
            ->orderBy('updated_at', 'desc')
            ->paginate(20);

        // Conta mensagens de clientes que ainda não foram respondidas
        $novasMensagens = Message::where('sender_type', 'cliente')
            ->whereNull('answered_at')
            ->count();

        // Busca a última mensagem não respondida para obter o timestamp
        $ultimaMensagemNaoRespondida = Message::where('sender_type', 'cliente')
            ->whereNull('answered_at')
            ->latest('created_at')
            ->first();

        $ultimoTimestamp = $ultimaMensagemNaoRespondida ? $ultimaMensagemNaoRespondida->created_at->timestamp : null;

        // Se for requisição AJAX para atualizar tabela
        if (($request->ajax() || $request->has('ajax')) && $request->has('updateTable')) {
            // Mantém os filtros na paginação
            $tickets->appends($request->except('page'));
            
            $ticketsHtml = view('suporte.partials.tickets-table', compact('tickets'))->render();
            $paginationHtml = $tickets->hasPages() ? $tickets->links()->render() : '';
            
            return response()->json([
                'novasMensagens' => $novasMensagens,
                'ultimaMensagemTimestamp' => $ultimoTimestamp,
                'ticketsHtml' => $ticketsHtml,
                'paginationHtml' => $paginationHtml,
            ]);
        }

        // Se for requisição AJAX apenas para notificações
        if ($request->ajax() || $request->has('ajax')) {
            return response()->json([
                'novasMensagens' => $novasMensagens,
                'ultimaMensagemTimestamp' => $ultimoTimestamp,
            ]);
        }

        return view('suporte.index', compact('tickets', 'novasMensagens', 'ultimoTimestamp'));
    }

    /**
     * Visualizar detalhes de um ticket
     */
    public function visualizar($id)
    {
        $ticket = Ticket::with('messages')
            ->findOrFail($id);

        // Marca todas as mensagens do cliente como visualizadas pelo suporte
        Message::where('ticket_id', $ticket->id)
            ->where('sender_type', 'cliente')
            ->whereNull('viewed_at')
            ->update(['viewed_at' => now()]);

        // Ordena mensagens por data (mais antiga primeiro)
        $ticket->messages = $ticket->messages->sortBy('created_at');

        return view('suporte.show', compact('ticket'));
    }

    /**
     * Responder um ticket
     */
    public function responder(Request $request, $id)
    {
        $request->validate([
            'mensagem' => 'required|string|min:10',
        ]);

        $ticket = Ticket::findOrFail($id);

        // Bloqueia resposta em tickets fechados
        if ($ticket->status === 'fechado') {
            return redirect()->back()
                ->with('error', 'Não é possível responder a um ticket fechado. Tickets fechados não podem ser reabertos.');
        }

        // Define o atendente se ainda não tiver um
        if (!$ticket->user_id) {
            $ticket->update(['user_id' => auth()->id()]);
        }

        // Cria mensagem do suporte
        $message = Message::create([
            'ticket_id' => $ticket->id,
            'sender_email' => auth()->user()->email ?? 'suporte@agendavoce.com.br',
            'sender_type' => 'suporte',
            'message' => $request->mensagem,
            'sent_at' => now(),
            'received_at' => now(),
            'answered_at' => now(),
            'viewed_at' => null, // Será marcado quando cliente visualizar
            'user_id' => auth()->id(), // Usuário que respondeu
        ]);

        // Marca TODAS as mensagens do cliente como visualizadas pelo suporte
        Message::where('ticket_id', $ticket->id)
            ->where('sender_type', 'cliente')
            ->whereNull('viewed_at')
            ->update(['viewed_at' => now()]);

        // Marca TODAS as mensagens do cliente não respondidas como respondidas
        Message::where('ticket_id', $ticket->id)
            ->where('sender_type', 'cliente')
            ->whereNull('answered_at')
            ->update(['answered_at' => now()]);

        // Atualiza status do ticket para aguardando resposta do cliente
        $ticket->update(['status' => 'aguardando_cliente']);

        return redirect()->route('suporte.visualizar', $ticket->id)
            ->with('success', 'Mensagem enviada com sucesso!');
    }

    /**
     * Fechar um ticket
     */
    public function fechar($id)
    {
        $ticket = Ticket::findOrFail($id);

        if (!$ticket->podeFechar()) {
            return redirect()->back()
                ->with('error', 'Este ticket não pode ser fechado.');
        }

        // Marca TODAS as mensagens do ticket como visualizadas
        Message::where('ticket_id', $ticket->id)
            ->whereNull('viewed_at')
            ->update(['viewed_at' => now()]);

        // Marca TODAS as mensagens do cliente não respondidas como respondidas
        Message::where('ticket_id', $ticket->id)
            ->where('sender_type', 'cliente')
            ->whereNull('answered_at')
            ->update(['answered_at' => now()]);

        // Define o atendente se ainda não tiver um
        if (!$ticket->user_id) {
            $ticket->update(['user_id' => auth()->id()]);
        }

        // Cria mensagem padrão informando que o ticket foi fechado
        Message::create([
            'ticket_id' => $ticket->id,
            'sender_email' => auth()->user()->email ?? 'suporte@agendavoce.com.br',
            'sender_type' => 'suporte',
            'message' => 'Este ticket foi fechado pelo suporte. Se você tiver mais dúvidas ou precisar de ajuda adicional, por favor, abra um novo ticket. Obrigado por entrar em contato conosco!',
            'sent_at' => now(),
            'received_at' => now(),
            'answered_at' => now(),
            'viewed_at' => null, // Será marcado quando cliente visualizar
            'user_id' => auth()->id(), // Usuário que fechou o ticket
        ]);

        // Fecha o ticket
        $ticket->update(['status' => 'fechado']);

        return redirect()->back()
            ->with('success', 'Ticket fechado com sucesso! Uma mensagem foi enviada ao cliente.');
    }
}
