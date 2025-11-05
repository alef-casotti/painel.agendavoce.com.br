<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ticket;
use App\Models\Message;
use App\Models\User;
use Carbon\Carbon;

class TicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buscar usuários de suporte/admin para atribuir aos tickets
        $suporteUser = User::where('role', 'suporte')->first();
        $adminUser = User::where('role', 'admin')->first();
        $userAtendente = $suporteUser ?? $adminUser;
        // Ticket 1: Aberto com prioridade normal - mensagem inicial do cliente
        $ticket1 = Ticket::create([
            'email' => 'cliente1@example.com',
            'assunto' => 'Problema ao fazer login no sistema',
            'status' => 'aberto',
            'prioridade' => 'normal',
        ]);

        Message::create([
            'ticket_id' => $ticket1->id,
            'sender_email' => 'cliente1@example.com',
            'sender_type' => 'cliente',
            'message' => 'Olá, não consigo fazer login no sistema. Minha senha não está funcionando.',
            'sent_at' => Carbon::now()->subDays(2),
            'received_at' => Carbon::now()->subDays(2),
        ]);

        // Ticket 2: Em andamento com prioridade alta - conversa entre cliente e suporte
        $ticket2 = Ticket::create([
            'email' => 'cliente2@example.com',
            'assunto' => 'Erro ao processar pagamento',
            'status' => 'em_andamento',
            'prioridade' => 'alta',
            'user_id' => $userAtendente->id ?? null,
        ]);

        Message::create([
            'ticket_id' => $ticket2->id,
            'sender_email' => 'cliente2@example.com',
            'sender_type' => 'cliente',
            'message' => 'Preciso de ajuda urgente! O pagamento não está sendo processado.',
            'sent_at' => Carbon::now()->subDays(1),
            'received_at' => Carbon::now()->subDays(1),
            'viewed_at' => Carbon::now()->subDays(1)->addHours(2),
        ]);

        Message::create([
            'ticket_id' => $ticket2->id,
            'sender_email' => 'suporte@agendavoce.com.br',
            'sender_type' => 'suporte',
            'message' => 'Olá! Estamos investigando o problema. Pode nos enviar o número do pedido?',
            'sent_at' => Carbon::now()->subDays(1)->addHours(3),
            'received_at' => Carbon::now()->subDays(1)->addHours(3),
            'answered_at' => Carbon::now()->subDays(1)->addHours(3),
            'viewed_at' => Carbon::now()->subDays(1)->addHours(4),
            'user_id' => $userAtendente->id ?? null,
        ]);

        Message::create([
            'ticket_id' => $ticket2->id,
            'sender_email' => 'cliente2@example.com',
            'sender_type' => 'cliente',
            'message' => 'O número do pedido é #12345. Desde já, obrigado!',
            'sent_at' => Carbon::now()->subDays(1)->addHours(5),
            'received_at' => Carbon::now()->subDays(1)->addHours(5),
            'viewed_at' => Carbon::now()->subDays(1)->addHours(6),
        ]);

        // Ticket 3: Aguardando cliente - suporte respondeu e está esperando
        $ticket3 = Ticket::create([
            'email' => 'cliente3@example.com',
            'assunto' => 'Dúvida sobre configuração de agendamento',
            'status' => 'aguardando_cliente',
            'prioridade' => 'normal',
            'user_id' => $userAtendente->id ?? null,
        ]);

        Message::create([
            'ticket_id' => $ticket3->id,
            'sender_email' => 'cliente3@example.com',
            'sender_type' => 'cliente',
            'message' => 'Como configuro os horários de atendimento?',
            'sent_at' => Carbon::now()->subHours(12),
            'received_at' => Carbon::now()->subHours(12),
            'viewed_at' => Carbon::now()->subHours(11),
        ]);

        Message::create([
            'ticket_id' => $ticket3->id,
            'sender_email' => 'suporte@agendavoce.com.br',
            'sender_type' => 'suporte',
            'message' => 'Olá! Para configurar os horários, acesse Configurações > Horários de Atendimento. Você prefere que eu envie um tutorial passo a passo?',
            'sent_at' => Carbon::now()->subHours(10),
            'received_at' => Carbon::now()->subHours(10),
            'answered_at' => Carbon::now()->subHours(10),
            'viewed_at' => Carbon::now()->subHours(9),
            'user_id' => $userAtendente->id ?? null,
        ]);

        // Ticket 4: Fechado - ticket resolvido
        $ticket4 = Ticket::create([
            'email' => 'cliente4@example.com',
            'assunto' => 'Solicitação de reembolso',
            'status' => 'fechado',
            'prioridade' => 'normal',
            'user_id' => $userAtendente->id ?? null,
        ]);

        Message::create([
            'ticket_id' => $ticket4->id,
            'sender_email' => 'cliente4@example.com',
            'sender_type' => 'cliente',
            'message' => 'Gostaria de solicitar o reembolso do último pagamento.',
            'sent_at' => Carbon::now()->subDays(5),
            'received_at' => Carbon::now()->subDays(5),
            'viewed_at' => Carbon::now()->subDays(5)->addHours(1),
        ]);

        Message::create([
            'ticket_id' => $ticket4->id,
            'sender_email' => 'suporte@agendavoce.com.br',
            'sender_type' => 'suporte',
            'message' => 'Olá! Vamos processar seu reembolso. Pode nos enviar o número da transação?',
            'sent_at' => Carbon::now()->subDays(5)->addHours(2),
            'received_at' => Carbon::now()->subDays(5)->addHours(2),
            'answered_at' => Carbon::now()->subDays(5)->addHours(2),
            'viewed_at' => Carbon::now()->subDays(5)->addHours(3),
            'user_id' => $userAtendente->id ?? null,
        ]);

        Message::create([
            'ticket_id' => $ticket4->id,
            'sender_email' => 'cliente4@example.com',
            'sender_type' => 'cliente',
            'message' => 'A transação é #TRX-789456. Obrigado!',
            'sent_at' => Carbon::now()->subDays(5)->addHours(4),
            'received_at' => Carbon::now()->subDays(5)->addHours(4),
            'viewed_at' => Carbon::now()->subDays(5)->addHours(5),
        ]);

        Message::create([
            'ticket_id' => $ticket4->id,
            'sender_email' => 'suporte@agendavoce.com.br',
            'sender_type' => 'suporte',
            'message' => 'Reembolso processado com sucesso! O valor será creditado em até 5 dias úteis. Ticket resolvido.',
            'sent_at' => Carbon::now()->subDays(4),
            'received_at' => Carbon::now()->subDays(4),
            'answered_at' => Carbon::now()->subDays(4),
            'viewed_at' => Carbon::now()->subDays(4)->addHours(1),
            'user_id' => $userAtendente->id ?? null,
        ]);

        // Ticket 5: Em andamento com prioridade alta - múltiplas mensagens
        $ticket5 = Ticket::create([
            'email' => 'cliente5@example.com',
            'assunto' => 'Sistema não está carregando',
            'status' => 'em_andamento',
            'prioridade' => 'alta',
            'user_id' => $userAtendente->id ?? null,
        ]);

        Message::create([
            'ticket_id' => $ticket5->id,
            'sender_email' => 'cliente5@example.com',
            'sender_type' => 'cliente',
            'message' => 'O sistema não está carregando. Aparece um erro 500.',
            'sent_at' => Carbon::now()->subHours(6),
            'received_at' => Carbon::now()->subHours(6),
        ]);

        Message::create([
            'ticket_id' => $ticket5->id,
            'sender_email' => 'suporte@agendavoce.com.br',
            'sender_type' => 'suporte',
            'message' => 'Estamos verificando o problema. Qual navegador você está usando?',
            'sent_at' => Carbon::now()->subHours(5),
            'received_at' => Carbon::now()->subHours(5),
            'answered_at' => Carbon::now()->subHours(5),
            'viewed_at' => Carbon::now()->subHours(4),
            'user_id' => $userAtendente->id ?? null,
        ]);

        Message::create([
            'ticket_id' => $ticket5->id,
            'sender_email' => 'cliente5@example.com',
            'sender_type' => 'cliente',
            'message' => 'Estou usando Chrome versão mais recente.',
            'sent_at' => Carbon::now()->subHours(3),
            'received_at' => Carbon::now()->subHours(3),
            'viewed_at' => Carbon::now()->subHours(2),
        ]);

        // Ticket 6: Aberto com prioridade alta - mensagem não visualizada
        $ticket6 = Ticket::create([
            'email' => 'cliente6@example.com',
            'assunto' => 'Urgente: Problema crítico no sistema',
            'status' => 'aberto',
            'prioridade' => 'alta',
        ]);

        Message::create([
            'ticket_id' => $ticket6->id,
            'sender_email' => 'cliente6@example.com',
            'sender_type' => 'cliente',
            'message' => 'Preciso de ajuda urgente! O sistema está completamente inacessível para meus clientes.',
            'sent_at' => Carbon::now()->subHours(1),
            'received_at' => Carbon::now()->subHours(1),
        ]);

        // Ticket 7: Fechado - ticket simples resolvido rapidamente
        $ticket7 = Ticket::create([
            'email' => 'cliente7@example.com',
            'assunto' => 'Como alterar minha senha?',
            'status' => 'fechado',
            'prioridade' => 'normal',
            'user_id' => $userAtendente->id ?? null,
        ]);

        Message::create([
            'ticket_id' => $ticket7->id,
            'sender_email' => 'cliente7@example.com',
            'sender_type' => 'cliente',
            'message' => 'Esqueci minha senha, como posso alterá-la?',
            'sent_at' => Carbon::now()->subDays(3),
            'received_at' => Carbon::now()->subDays(3),
            'viewed_at' => Carbon::now()->subDays(3)->addMinutes(30),
        ]);

        Message::create([
            'ticket_id' => $ticket7->id,
            'sender_email' => 'suporte@agendavoce.com.br',
            'sender_type' => 'suporte',
            'message' => 'Clique em "Esqueci minha senha" na tela de login ou acesse: Configurações > Segurança > Alterar senha. Problema resolvido!',
            'sent_at' => Carbon::now()->subDays(3)->addHours(1),
            'received_at' => Carbon::now()->subDays(3)->addHours(1),
            'answered_at' => Carbon::now()->subDays(3)->addHours(1),
            'viewed_at' => Carbon::now()->subDays(3)->addHours(2),
            'user_id' => $userAtendente->id ?? null,
        ]);

        // Ticket 8: Aguardando cliente - com mensagem não visualizada pelo cliente
        $ticket8 = Ticket::create([
            'email' => 'cliente8@example.com',
            'assunto' => 'Dúvida sobre integração com API',
            'status' => 'aguardando_cliente',
            'prioridade' => 'normal',
            'user_id' => $userAtendente->id ?? null,
        ]);

        Message::create([
            'ticket_id' => $ticket8->id,
            'sender_email' => 'cliente8@example.com',
            'sender_type' => 'cliente',
            'message' => 'Preciso integrar minha aplicação com a API do sistema.',
            'sent_at' => Carbon::now()->subDays(2),
            'received_at' => Carbon::now()->subDays(2),
            'viewed_at' => Carbon::now()->subDays(2)->addHours(1),
        ]);

        Message::create([
            'ticket_id' => $ticket8->id,
            'sender_email' => 'suporte@agendavoce.com.br',
            'sender_type' => 'suporte',
            'message' => 'Enviaremos a documentação da API por email. Você pode também acessar nossa documentação em: docs.agendavoce.com.br/api. Aguardamos seu retorno.',
            'sent_at' => Carbon::now()->subDays(1)->addHours(4),
            'received_at' => Carbon::now()->subDays(1)->addHours(4),
            'answered_at' => Carbon::now()->subDays(1)->addHours(4),
            'user_id' => $userAtendente->id ?? null,
        ]);
    }
}

