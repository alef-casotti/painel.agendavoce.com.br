<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Ticket;
use App\Models\Message;

class SuporteApiController extends Controller
{
    /**
     * Cliente envia mensagem (cria ticket se não existir)
     */
    public function enviarMensagem(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'assunto' => 'required|string|max:255',
            'mensagem' => 'required|string|min:10',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Verifica se cliente tem assinatura (alta prioridade)
        $temAssinatura = $this->clienteTemAssinatura($request->email);
        $prioridade = $temAssinatura ? 'alta' : 'normal';

        // Busca ticket existente do mesmo email e assunto, ou cria novo
        $ticket = Ticket::where('email', $request->email)
            ->where('assunto', $request->assunto)
            ->first();

        if (!$ticket) {
            // Cria novo ticket se não existir
            $ticket = Ticket::create([
                'email' => $request->email,
                'assunto' => $request->assunto,
                'status' => 'aberto',
                'prioridade' => $prioridade,
            ]);
        } else {
            // Se ticket já existe, atualiza prioridade se cliente tem assinatura
            if ($temAssinatura && $ticket->prioridade !== 'alta') {
                $ticket->update(['prioridade' => 'alta']);
            }
        }

        // Cria mensagem
        $message = Message::create([
            'ticket_id' => $ticket->id,
            'sender_email' => $request->email,
            'sender_type' => 'cliente',
            'message' => $request->mensagem,
            'sent_at' => now(),
            'received_at' => now(),
            'answered_at' => null,
            'viewed_at' => null,
        ]);

        // Atualiza status do ticket se estava fechado
        if ($ticket->status === 'fechado') {
            $ticket->update(['status' => 'aberto']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Mensagem enviada com sucesso',
            'data' => [
                'ticket_id' => $ticket->id,
                'message_id' => $message->id,
            ]
        ], 201);
    }

    /**
     * Verifica se o cliente tem assinatura ativa
     * TODO: Adaptar para consultar sua tabela de assinaturas/planos
     * Por enquanto retorna false - você deve implementar a lógica aqui
     */
    private function clienteTemAssinatura(string $email): bool
    {
        // Exemplo: você pode consultar uma tabela de assinaturas aqui
        // return Subscription::where('email', $email)
        //     ->where('status', 'ativo')
        //     ->exists();
        
        // Por enquanto, retornar false
        // Você pode implementar aqui a lógica de verificação de assinatura
        return false;
    }

    /**
     * Lista todos os tickets de um cliente (por email)
     */
    public function listarTickets($email)
    {
        $tickets = Ticket::where('email', $email)
            ->withCount('messages')
            ->with(['messages' => function($query) {
                $query->select('id', 'ticket_id', 'sender_type', 'created_at')
                      ->latest()
                      ->limit(1);
            }])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $tickets
        ]);
    }

    /**
     * Lista todas as mensagens de um ticket específico
     */
    public function listarMensagens($ticket_id)
    {
        $ticket = Ticket::find($ticket_id);

        if (!$ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket não encontrado'
            ], 404);
        }

        $messages = Message::where('ticket_id', $ticket_id)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'ticket' => $ticket,
                'messages' => $messages
            ]
        ]);
    }

    /**
     * Marcar mensagem específica como visualizada
     */
    public function marcarVisualizada($message_id)
    {
        $message = Message::find($message_id);

        if (!$message) {
            return response()->json([
                'success' => false,
                'message' => 'Mensagem não encontrada'
            ], 404);
        }

        // Só marca se ainda não foi visualizada
        if (!$message->viewed_at) {
            $message->update(['viewed_at' => now()]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Mensagem marcada como visualizada',
            'data' => [
                'message_id' => $message->id,
                'viewed_at' => $message->viewed_at
            ]
        ]);
    }

    /**
     * Marcar todas as mensagens de um ticket como visualizadas
     */
    public function marcarTicketVisualizado(Request $request, $ticket_id)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $ticket = Ticket::find($ticket_id);

        if (!$ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket não encontrado'
            ], 404);
        }

        // Verifica se o email corresponde ao ticket
        if ($ticket->email !== $request->email) {
            return response()->json([
                'success' => false,
                'message' => 'Acesso negado. Email não corresponde ao ticket.'
            ], 403);
        }

        // Marca todas as mensagens do suporte (não enviadas pelo cliente) como visualizadas
        $atualizadas = Message::where('ticket_id', $ticket_id)
            ->where('sender_type', 'suporte')
            ->whereNull('viewed_at')
            ->update(['viewed_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'Mensagens marcadas como visualizadas',
            'data' => [
                'ticket_id' => $ticket_id,
                'mensagens_atualizadas' => $atualizadas
            ]
        ]);
    }
}
