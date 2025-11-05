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
     * Cliente envia mensagem (cria ticket novo ou adiciona a ticket existente)
     */
    public function enviarMensagem(Request $request)
    {
        // Validação condicional: se tem ticket_id, só precisa de mensagem
        // Se não tem ticket_id, precisa de email, assunto e mensagem
        $rules = [
            'mensagem' => 'required|string|min:10',
            'prioridade' => 'nullable|in:alta,normal',
        ];

        if ($request->has('ticket_id')) {
            // Se tem ticket_id, valida apenas ticket_id e mensagem
            $rules['ticket_id'] = 'required|integer|exists:tickets,id';
            $rules['email'] = 'nullable|email'; // Opcional para validação de segurança
        } else {
            // Se não tem ticket_id, precisa criar novo ticket
            $rules['email'] = 'required|email';
            $rules['assunto'] = 'required|string|max:255';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Usa a prioridade enviada na requisição, ou 'normal' como padrão
        $prioridade = $request->input('prioridade', 'normal');

        // Se ticket_id foi enviado, busca o ticket existente
        if ($request->has('ticket_id')) {
            $ticket = Ticket::find($request->ticket_id);
            
            if (!$ticket) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ticket não encontrado'
                ], 404);
            }

            // Validação de segurança: verifica se o email corresponde (se foi enviado)
            if ($request->has('email') && $ticket->email !== $request->email) {
                return response()->json([
                    'success' => false,
                    'message' => 'Acesso negado. Email não corresponde ao ticket.'
                ], 403);
            }

            // Atualiza a prioridade se foi enviada na requisição
            if ($request->has('prioridade')) {
                $ticket->update(['prioridade' => $prioridade]);
            }

            // Usa o email do ticket existente
            $email = $ticket->email;
        } else {
            // Cria novo ticket
            $ticket = Ticket::create([
                'email' => $request->email,
                'assunto' => $request->assunto,
                'status' => 'aberto',
                'prioridade' => $prioridade,
            ]);

            $email = $request->email;
        }

        // Cria mensagem
        $message = Message::create([
            'ticket_id' => $ticket->id,
            'sender_email' => $email,
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
