<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ClienteController extends Controller
{
    /**
     * Lista principal de clientes.
     */
    public function index(Request $request)
    {
        // Buscar configurações da API
        $baseUrl = config('services.usuarios.base_url');
        $apiToken = config('services.usuarios.api_token');

        $busca = $request->get('busca', '');
        $statusFiltro = $request->get('status', ''); // operacao, trial, cancelado
        
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

        if (!$baseUrl || !$apiToken) {
            Log::warning('Configuração da API de usuários não encontrada');
            return view('clientes.index', compact('clientes', 'busca', 'statusFiltro'));
        }

        try {
            // Construir parâmetros para a API
            $params = [];
            if (!empty($busca)) {
                $params['busca'] = $busca;
            }
            if (!empty($statusFiltro)) {
                $params['status'] = $statusFiltro;
            }
            
            $response = Http::timeout(10)
                ->withToken($apiToken)
                ->acceptJson()
                ->get("{$baseUrl}/api/usuarios", $params);

            if ($response->successful()) {
                $data = $response->json('data', []);

                // Transformar os dados da API para o formato esperado pela view
                $clientes = collect($data)->map(function ($usuario) {
                    $status = $this->normalizarStatusConta($usuario);

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
                        'id' => $usuario['id'],
                        'telefone' => $this->formatarTelefone($usuario['telefone'] ?? ''),
                        'plano' => $usuario['plano'],
                        'total_agendamentos' => $usuario['total_agendamentos'] ?? 0,
                        'total_servicos' => $usuario['total_servicos'] ?? 0,
                        'total_clientes' => $usuario['total_clientes'] ?? 0,
                        'tem_pagina_publica' => $usuario['tem_pagina_publica'] ?? false,
                    ];
                });

                if (!empty($statusFiltro)) {
                    $clientes = $clientes->filter(function ($cliente) use ($statusFiltro) {
                        return $cliente['status'] === $statusFiltro;
                    })->values();
                }

                // Ordenar por nome em ordem alfabética
                $clientes = $clientes->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)->values();
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

        return view('clientes.index', compact('clientes', 'busca', 'statusFiltro'));
    }

    /**
     * Normalizar status da conta para operacao, trial ou cancelado.
     */
    private function normalizarStatusConta(array $usuario): string
    {
        $status = $usuario['status']
            ?? $usuario['status_conta']
            ?? $usuario['situacao']
            ?? null;

        if ($status !== null && $status !== '') {
            $status = strtolower((string) $status);

            return match (true) {
                in_array($status, ['operacao', 'em_operacao', 'operação', 'ativo', 'active'], true) => 'operacao',
                in_array($status, ['trial', 'em_trial'], true) => 'trial',
                in_array($status, ['cancelado', 'cancelada', 'canceladas', 'canceled', 'cancelled'], true) => 'cancelado',
                default => 'operacao',
            };
        }

        if (!empty($usuario['assinatura_cancelada']) || !empty($usuario['cancelado'])) {
            return 'cancelado';
        }

        if (($usuario['plano'] ?? '') === 'free') {
            return 'trial';
        }

        return 'operacao';
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
        $cliente['telefone_formatado'] = $this->formatarTelefone($cliente['telefone'] ?? '');

        $meses = [
            1 => 'Jan', 2 => 'Fev', 3 => 'Mar', 4 => 'Abr',
            5 => 'Mai', 6 => 'Jun', 7 => 'Jul', 8 => 'Ago',
            9 => 'Set', 10 => 'Out', 11 => 'Nov', 12 => 'Dez',
        ];

        if (!empty($cliente['email_verificado_em'])) {
            $dataCarbon = Carbon::parse($cliente['email_verificado_em']);
            $cliente['email_verificado_em_formatada'] = $dataCarbon->format('d') . ' ' . $meses[$dataCarbon->month] . ' ' . $dataCarbon->format('Y');
        }

        if (!empty($cliente['data_criacao'])) {
            $dataCarbon = Carbon::parse($cliente['data_criacao']);
            $cliente['data_criacao_formatada'] = $dataCarbon->format('d') . ' ' . $meses[$dataCarbon->month] . ' ' . $dataCarbon->format('Y');
        }

        if (!empty($cliente['data_atualizacao'])) {
            $dataCarbon = Carbon::parse($cliente['data_atualizacao']);
            $cliente['data_atualizacao_formatada'] = $dataCarbon->format('d') . ' ' . $meses[$dataCarbon->month] . ' ' . $dataCarbon->format('Y');
        }

        if (!empty($cliente['ultimo_acesso_em'])) {
            $dataCarbon = Carbon::parse($cliente['ultimo_acesso_em']);
            $cliente['ultimo_acesso_em_formatada'] = $dataCarbon->format('d') . ' ' . $meses[$dataCarbon->month] . ' ' . $dataCarbon->format('Y') . ' às ' . $dataCarbon->format('H:i');
        }

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

        $cliente['cancelamento_agendado'] = $this->temCancelamentoAgendado($cliente);

        if (!empty($cliente['suporte']['ultimo_ticket']['criado_em'])) {
            $dataCarbon = Carbon::parse($cliente['suporte']['ultimo_ticket']['criado_em']);
            $cliente['suporte']['ultimo_ticket']['criado_em_formatada'] = $dataCarbon->format('d') . ' ' . $meses[$dataCarbon->month] . ' ' . $dataCarbon->format('Y');
        }

        if (!empty($cliente['pagina_publica']['slug'])) {
            $publicBase = rtrim(config('services.agendavoce.public_url', 'https://agendavoce.com.br'), '/');
            $cliente['pagina_publica']['url'] = $publicBase . '/p/' . $cliente['pagina_publica']['slug'];
        }

        if (!empty($cliente['profissionais_equipe'])) {
            foreach ($cliente['profissionais_equipe'] as &$profissional) {
                $profissional['telefone_formatado'] = $this->formatarTelefone($profissional['telefone'] ?? '');
            }
        }

        return $cliente;
    }

    /**
     * Verifica se o cancelamento da assinatura já está agendado.
     */
    private function temCancelamentoAgendado(array $cliente): bool
    {
        if (!empty($cliente['assinatura_cancelada']) || !empty($cliente['cancelado'])) {
            return true;
        }

        $assinatura = $cliente['assinatura_ativa'] ?? null;
        if (empty($assinatura)) {
            return false;
        }

        if (!empty($assinatura['cancel_at_period_end'])) {
            return true;
        }

        $status = strtolower((string) ($assinatura['status'] ?? ''));

        return in_array($status, ['cancel_at_period_end', 'canceled', 'cancelled'], true);
    }

    /**
     * Agendar cancelamento da assinatura do cliente (admin).
     */
    public function cancelSubscription($id)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $baseUrl = config('services.usuarios.base_url');
        $apiToken = config('services.usuarios.api_token');

        if (!$baseUrl || !$apiToken) {
            return redirect()->route('clientes.show', $id)->with('error', 'Configuração da API não encontrada');
        }

        try {
            $response = Http::timeout(10)
                ->withToken($apiToken)
                ->acceptJson()
                ->patch("{$baseUrl}/api/usuarios/{$id}/cancel-subscription");

            $status = $response->status();

            $message = match ($status) {
                200 => $response->json('message', 'Cancelamento da assinatura agendado com sucesso.'),
                404 => 'Cliente não encontrado.',
                422 => 'Cliente não possui assinatura ativa.',
                409 => 'Cancelamento já estava agendado.',
                502 => 'Falha ao processar cancelamento no Stripe.',
                default => 'Erro ao agendar cancelamento da assinatura.',
            };

            $flashType = match ($status) {
                200 => 'success',
                409 => 'warning',
                default => $response->successful() ? 'success' : 'error',
            };

            return redirect()->route('clientes.show', $id)->with($flashType, $message);
        } catch (\Throwable $exception) {
            Log::error('Erro ao cancelar assinatura do cliente', [
                'message' => $exception->getMessage(),
                'cliente_id' => $id,
            ]);

            return redirect()->route('clientes.show', $id)->with('error', 'Erro ao conectar com a API');
        }
    }

    /**
     * Impersonate: gera URL de acesso ao perfil do cliente (admin e suporte).
     */
    public function impersonate(Request $request, $id)
    {
        $user = auth()->user();

        if (!$user->isAdmin() && !$user->isSuporte()) {
            abort(403);
        }

        $baseUrl = rtrim(config('services.usuarios.base_url') ?? '', '/');
        $apiToken = config('services.usuarios.api_token');

        if (!$baseUrl || !$apiToken) {
            return redirect()->route('clientes.show', $id)->with('error', 'Configuração da API não encontrada');
        }

        try {
            $response = Http::timeout(10)
                ->withToken($apiToken)
                ->acceptJson()
                ->post("{$baseUrl}/api/usuarios/{$id}/impersonate", [
                    'support_user_id' => $user->id,
                    'support_email' => $user->email,
                    'reason' => 'Acesso via painel',
                    'request_ip' => $request->ip(),
                    'request_user_agent' => $request->userAgent(),
                ]);

            if ($response->successful()) {
                $url = $response->json('url') ?? $response->json('data.url');

                if ($url) {
                    return redirect()->away($url);
                }
            }

            Log::warning('API impersonate retornou erro', [
                'status' => $response->status(),
                'body' => $response->json(),
                'cliente_id' => $id,
            ]);

            $message = match ($response->status()) {
                401 => 'Token de API ausente ou inválido.',
                404 => "Usuário {$id} não existe.",
                422 => $response->json('message', 'Dados inválidos para impersonate.'),
                default => $response->json('message', 'Não foi possível gerar acesso à conta do cliente.'),
            };

            return redirect()->route('clientes.show', $id)->with('error', $message);
        } catch (\Throwable $exception) {
            Log::error('Erro ao impersonar conta do cliente', [
                'message' => $exception->getMessage(),
                'cliente_id' => $id,
            ]);

            return redirect()->route('clientes.show', $id)->with('error', 'Erro ao conectar com a API');
        }
    }
}
