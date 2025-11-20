@extends('layouts.app')

@section('content')
@php
    $statusLabels = [
        'active' => 'Ativo',
        'canceled' => 'Cancelado',
        'pending' => 'Pendente',
        'expired' => 'Expirado'
    ];
    
    $statusStyles = [
        'active' => 'bg-emerald-100 text-emerald-700',
        'canceled' => 'bg-gray-100 text-gray-700',
        'pending' => 'bg-yellow-100 text-yellow-700',
        'expired' => 'bg-red-100 text-red-700'
    ];
    
    $statusAgendamentoStyles = [
        'completed' => 'bg-emerald-100 text-emerald-700',
        'canceled' => 'bg-red-100 text-red-700',
        'pending' => 'bg-yellow-100 text-yellow-700',
        'confirmed' => 'bg-blue-100 text-blue-700'
    ];
    
    $diasSemana = [
        0 => 'Domingo',
        1 => 'Segunda-feira',
        2 => 'Terça-feira',
        3 => 'Quarta-feira',
        4 => 'Quinta-feira',
        5 => 'Sexta-feira',
        6 => 'Sábado'
    ];
@endphp

<div class="flex min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <x-sidebar />
    <x-header />

    <main class="flex-1 lg:ml-3 mt-16 overflow-y-auto">
        <div class="p-4 lg:p-8 space-y-6">
            <!-- Header -->
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $cliente['nome'] ?? 'Cliente' }}</h1>
                    <p class="text-gray-600">{{ $cliente['email'] ?? '' }}</p>
                </div>
                <div>
                    <a href="{{ route('clientes.index') }}" class="btn-primary">
                        Voltar
                    </a>
                </div>
            </div>

            <!-- Informações principais -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Card de Informações de Contato -->
                <div class="card p-5">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Informações de Contato</h3>
                    <div class="space-y-3">
                        <div>
                            <p class="text-sm text-gray-500">Email</p>
                            <p class="text-sm font-medium text-gray-900">{{ $cliente['email'] ?? '-' }}</p>
                            @if(isset($cliente['email_verificado']) && $cliente['email_verificado'])
                                <span class="text-xs text-emerald-600">✓ Verificado</span>
                            @else
                                <span class="text-xs text-gray-500">Não verificado</span>
                            @endif
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Telefone</p>
                            <p class="text-sm font-medium text-gray-900">{{ $cliente['telefone_formatado'] ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Tipo de Negócio</p>
                            <p class="text-sm font-medium text-gray-900">
                                @if(!empty($cliente['tipo_negocio_label']) && is_string($cliente['tipo_negocio_label']))
                                    {{ $cliente['tipo_negocio_label'] }}
                                @elseif(!empty($cliente['tipo_negocio']) && is_string($cliente['tipo_negocio']))
                                    {{ $cliente['tipo_negocio'] }}
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Card de Assinatura -->
                <div class="card p-5">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Assinatura</h3>
                    @if(!empty($cliente['assinatura_ativa']))
                        @php $assinatura = $cliente['assinatura_ativa']; @endphp
                        <div class="space-y-3">
                            <div>
                                <p class="text-sm text-gray-500">Plano</p>
                                <p class="text-lg font-bold text-gray-900 uppercase">{{ $assinatura['plan'] ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Valor</p>
                                <p class="text-lg font-bold text-gray-900">R$ {{ number_format($assinatura['price'] ?? 0, 2, ',', '.') }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Status</p>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $statusStyles[$assinatura['status']] ?? 'bg-gray-100 text-gray-700' }}">
                                    {{ $statusLabels[$assinatura['status']] ?? ucfirst($assinatura['status'] ?? '-') }}
                                </span>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Expira em</p>
                                <p class="text-sm font-medium text-gray-900">{{ $assinatura['expires_at_formatada'] ?? '-' }}</p>
                            </div>
                        </div>
                    @else
                        <p class="text-sm text-gray-500">Sem assinatura ativa</p>
                    @endif
                </div>

                <!-- Card de Estatísticas Rápidas -->
                <div class="card p-5">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Estatísticas</h3>
                    @if(!empty($cliente['estatisticas']))
                        @php $stats = $cliente['estatisticas']; @endphp
                        <div class="space-y-3">
                            <div>
                                <p class="text-sm text-gray-500">Total Agendamentos</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $stats['total_agendamentos'] ?? 0 }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Receita Total</p>
                                <p class="text-xl font-bold text-gray-900">R$ {{ number_format($stats['receita_total'] ?? 0, 2, ',', '.') }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Taxa de Conclusão</p>
                                <p class="text-lg font-bold text-gray-900">{{ number_format($stats['taxa_conclusao'] ?? 0, 1, ',', '.') }}%</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Informações detalhadas em tabs ou seções -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Estatísticas Detalhadas -->
                <div class="card p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Estatísticas Detalhadas</h3>
                    @if(!empty($cliente['estatisticas']))
                        @php $stats = $cliente['estatisticas']; @endphp
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-500">Agendamentos Completados</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $stats['agendamentos_completados'] ?? 0 }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Agendamentos Cancelados</p>
                                <p class="text-2xl font-bold text-red-600">{{ $stats['agendamentos_cancelados'] ?? 0 }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Total Serviços</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $stats['total_servicos'] ?? 0 }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Total Clientes</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $stats['total_clientes'] ?? 0 }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Receita Último Mês</p>
                                <p class="text-xl font-bold text-gray-900">R$ {{ number_format($stats['receita_ultimo_mes'] ?? 0, 2, ',', '.') }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Taxa de Cancelamento</p>
                                <p class="text-xl font-bold text-red-600">{{ number_format($stats['taxa_cancelamento'] ?? 0, 1, ',', '.') }}%</p>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Engajamento -->
                <div class="card p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Engajamento</h3>
                    @if(!empty($cliente['engajamento']))
                        @php $engajamento = $cliente['engajamento']; @endphp
                        <div class="space-y-4">
                            @if(!empty($engajamento['ultimo_agendamento']) && is_array($engajamento['ultimo_agendamento']))
                                <div>
                                    <p class="text-sm text-gray-500">Último Agendamento</p>
                                    <p class="text-sm font-medium text-gray-900">
                                        {{ $engajamento['ultimo_agendamento']['data'] ?? '-' }}
                                        @if(!empty($engajamento['ultimo_agendamento']['status']))
                                            <span class="ml-2 px-2 py-1 text-xs font-semibold rounded-full {{ $statusAgendamentoStyles[$engajamento['ultimo_agendamento']['status']] ?? 'bg-gray-100 text-gray-700' }}">
                                                {{ ucfirst($engajamento['ultimo_agendamento']['status']) }}
                                            </span>
                                        @endif
                                    </p>
                                </div>
                            @endif
                            @if(!empty($engajamento['proximo_agendamento']) && !is_array($engajamento['proximo_agendamento']))
                                <div>
                                    <p class="text-sm text-gray-500">Próximo Agendamento</p>
                                    <p class="text-sm font-medium text-gray-900">{{ $engajamento['proximo_agendamento'] }}</p>
                                </div>
                            @elseif(!empty($engajamento['proximo_agendamento']) && is_array($engajamento['proximo_agendamento']))
                                <div>
                                    <p class="text-sm text-gray-500">Próximo Agendamento</p>
                                    <p class="text-sm font-medium text-gray-900">
                                        {{ $engajamento['proximo_agendamento']['data'] ?? '' }} 
                                        {{ isset($engajamento['proximo_agendamento']['hora_inicio']) ? substr($engajamento['proximo_agendamento']['hora_inicio'], 0, 5) : '' }}
                                    </p>
                                </div>
                            @else
                                <div>
                                    <p class="text-sm text-gray-500">Próximo Agendamento</p>
                                    <p class="text-sm font-medium text-gray-500">Nenhum agendamento agendado</p>
                                </div>
                            @endif
                            <div>
                                <p class="text-sm text-gray-500">Dias desde último agendamento</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $engajamento['dias_desde_ultimo_agendamento'] ?? '-' }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Serviços -->
            @if(!empty($cliente['servicos']) && count($cliente['servicos']) > 0)
            <div class="card p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Serviços</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descrição</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duração</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Preço</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($cliente['servicos'] as $servico)
                                <tr>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $servico['nome'] ?? '-' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">{{ $servico['descricao'] ?? '-' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">{{ $servico['duracao_minutos'] ?? '-' }} min</td>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900">R$ {{ number_format($servico['preco'] ?? 0, 2, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            <!-- Agendamentos Recentes -->
            @if(!empty($cliente['agendamentos_recentes']) && count($cliente['agendamentos_recentes']) > 0)
            <div class="card p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Agendamentos Recentes</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hora</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valor</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($cliente['agendamentos_recentes'] as $agendamento)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $agendamento['data_formatada'] ?? $agendamento['data'] ?? '-' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">{{ substr($agendamento['hora_inicio'] ?? '', 0, 5) }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $agendamento['cliente']['nome'] ?? '-' }}</td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $statusAgendamentoStyles[$agendamento['status']] ?? 'bg-gray-100 text-gray-700' }}">
                                            {{ $agendamento['status_label'] ?? ucfirst($agendamento['status'] ?? '-') }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900">R$ {{ number_format($agendamento['valor_total'] ?? 0, 2, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            <!-- Horários de Funcionamento -->
            @if(!empty($cliente['horarios_funcionamento']) && count($cliente['horarios_funcionamento']) > 0)
            <div class="card p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Horários de Funcionamento</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($cliente['horarios_funcionamento'] as $horario)
                        @if(!$horario['fechado'])
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <span class="text-sm font-medium text-gray-900">{{ $horario['dia_semana_nome'] ?? $diasSemana[$horario['dia_semana']] ?? '-' }}</span>
                                <span class="text-sm text-gray-600">
                                    {{ substr($horario['hora_inicio'] ?? '', 0, 5) }} - {{ substr($horario['hora_fim'] ?? '', 0, 5) }}
                                </span>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Histórico de Assinaturas -->
            @if(!empty($cliente['historico_assinaturas']) && count($cliente['historico_assinaturas']) > 0)
            <div class="card p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Histórico de Assinaturas</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plano</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valor</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Início</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expira</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($cliente['historico_assinaturas'] as $historico)
                                <tr>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900 uppercase">{{ $historico['plan'] ?? '-' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">R$ {{ number_format($historico['price'] ?? 0, 2, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">{{ $historico['started_at_formatada'] ?? '-' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">{{ $historico['expires_at_formatada'] ?? '-' }}</td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $statusStyles[$historico['status']] ?? 'bg-gray-100 text-gray-700' }}">
                                            {{ $statusLabels[$historico['status']] ?? ucfirst($historico['status'] ?? '-') }}
                                        </span>
                                        @if(!empty($historico['canceled_at']))
                                            <p class="text-xs text-gray-500 mt-1">Cancelado em: {{ $historico['canceled_at_formatada'] ?? '' }}</p>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            <!-- Informações Adicionais -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Stripe -->
                @if(!empty($cliente['stripe']))
                <div class="card p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Informações Stripe</h3>
                    @php $stripe = $cliente['stripe']; @endphp
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">Tem conta Stripe</span>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $stripe['tem_conta'] ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-700' }}">
                                {{ $stripe['tem_conta'] ? 'Sim' : 'Não' }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">Onboarding Completo</span>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $stripe['onboarding_completo'] ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-700' }}">
                                {{ $stripe['onboarding_completo'] ? 'Sim' : 'Não' }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">Pode Receber Pagamentos</span>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $stripe['pode_receber_pagamentos'] ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-700' }}">
                                {{ $stripe['pode_receber_pagamentos'] ? 'Sim' : 'Não' }}
                            </span>
                        </div>
                        @if(!empty($stripe['stripe_account_id']))
                            <div>
                                <p class="text-sm text-gray-500">Stripe Account ID</p>
                                <p class="text-sm font-mono text-gray-900">{{ $stripe['stripe_account_id'] }}</p>
                            </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Página Pública -->
                @if(!empty($cliente['pagina_publica']))
                <div class="card p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Página Pública</h3>
                    @php $pagina = $cliente['pagina_publica']; @endphp
                    <div class="space-y-3">
                        <div>
                            <p class="text-sm text-gray-500">Título</p>
                            <p class="text-sm font-medium text-gray-900">{{ $pagina['title'] ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Slug</p>
                            <p class="text-sm font-mono text-gray-900">{{ $pagina['slug'] ?? '-' }}</p>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">Status</span>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $pagina['active'] ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-700' }}">
                                {{ $pagina['active'] ? 'Ativa' : 'Inativa' }}
                            </span>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Notificações -->
                @if(!empty($cliente['notificacoes']))
                <div class="card p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Notificações</h3>
                    @php $notificacoes = $cliente['notificacoes']; @endphp
                    <div class="space-y-3">
                        <div>
                            <p class="text-sm text-gray-500">Total</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $notificacoes['total'] ?? 0 }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Não Lidas</p>
                            <p class="text-xl font-bold text-blue-600">{{ $notificacoes['total_nao_lidas'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </main>
</div>
@endsection

