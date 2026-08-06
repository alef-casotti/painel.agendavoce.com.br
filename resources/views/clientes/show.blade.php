@extends('layouts.app')

@section('content')
@php
    $statusLabels = [
        'active' => 'Ativo',
        'canceled' => 'Cancelado',
        'cancelled' => 'Cancelado',
        'cancel_at_period_end' => 'Cancelamento agendado',
        'pending' => 'Pendente',
        'expired' => 'Expirado',
    ];

    $statusStyles = [
        'active' => 'bg-emerald-100 text-emerald-700 ring-emerald-600/20',
        'canceled' => 'bg-gray-100 text-gray-700 ring-gray-500/20',
        'cancelled' => 'bg-gray-100 text-gray-700 ring-gray-500/20',
        'cancel_at_period_end' => 'bg-amber-100 text-amber-700 ring-amber-600/20',
        'pending' => 'bg-yellow-100 text-yellow-700 ring-yellow-600/20',
        'expired' => 'bg-red-100 text-red-700 ring-red-600/20',
    ];

    $ticketStatusLabels = [
        'open' => 'Aberto',
        'aberto' => 'Aberto',
        'fechado' => 'Fechado',
        'closed' => 'Fechado',
        'em_andamento' => 'Em andamento',
        'aguardando_cliente' => 'Aguardando cliente',
    ];

    $ticketStatusStyles = [
        'open' => 'bg-blue-100 text-blue-700',
        'aberto' => 'bg-blue-100 text-blue-700',
        'fechado' => 'bg-gray-100 text-gray-700',
        'closed' => 'bg-gray-100 text-gray-700',
        'em_andamento' => 'bg-yellow-100 text-yellow-700',
        'aguardando_cliente' => 'bg-amber-100 text-amber-700',
    ];

    $planoLabels = [
        'free' => 'Free',
        'pro' => 'Pro',
        'premium' => 'Premium',
    ];

    $iniciais = collect(explode(' ', $cliente['nome'] ?? 'C'))
        ->filter()
        ->take(2)
        ->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))
        ->implode('');

    $assinatura = $cliente['assinatura_ativa'] ?? null;
    $cancelamentoAgendado = !empty($cliente['cancelamento_agendado']);
    $podeCancelar = auth()->user()->isAdmin()
        && !empty($assinatura)
        && ($assinatura['status'] ?? '') === 'active'
        && !$cancelamentoAgendado;
    $suporte = $cliente['suporte'] ?? [];
    $equipe = $cliente['profissionais_equipe'] ?? [];
    $pagina = $cliente['pagina_publica'] ?? null;
    $configInicial = $cliente['configuracao_inicial'] ?? null;
    $aptoAgendamentos = $cliente['apto_para_receber_agendamentos'] ?? null;

    $passosConfigInicial = [
        'servicos' => 'Serviços',
        'profissionais' => 'Profissionais',
        'horarios' => 'Horários',
        'pagina_personalizada' => 'Página personalizada',
        'link_compartilhado' => 'Link compartilhado',
    ];
@endphp

<div class="flex min-h-screen bg-gradient-to-br from-slate-50 via-gray-50 to-blue-50/30">
    <x-sidebar />
    <x-header />

    <main class="flex-1 lg:ml-3 mt-16 overflow-y-auto">
        <div class="p-4 lg:p-8 space-y-6 max-w-7xl">

            @if(session('success'))
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800 shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800 shadow-sm">
                    {{ session('error') }}
                </div>
            @endif

            @if(session('warning'))
                <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-medium text-amber-800 shadow-sm">
                    {{ session('warning') }}
                </div>
            @endif

            {{-- Hero --}}
            <section class="relative overflow-hidden rounded-2xl bg-[#eff4ff] border border-blue-100 shadow-sm">
                <div class="absolute -top-24 -right-24 w-64 h-64 rounded-full bg-blue-200/30 blur-3xl"></div>
                <div class="absolute -bottom-16 -left-16 w-48 h-48 rounded-full bg-blue-100/50 blur-2xl"></div>

                <div class="relative p-6 lg:p-8">
                    <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                        <div class="flex items-start gap-5">
                            <div class="flex h-16 w-16 lg:h-20 lg:w-20 shrink-0 items-center justify-center rounded-2xl bg-blue-600 text-2xl lg:text-3xl font-bold text-white ring-2 ring-blue-200 shadow-md">
                                {{ $iniciais ?: 'C' }}
                            </div>
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2 mb-2">
                                    <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 tracking-tight">{{ $cliente['nome'] ?? 'Cliente' }}</h1>
                                    @if(isset($cliente['ativo']))
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 text-xs font-semibold rounded-full ring-1 ring-inset {{ $cliente['ativo'] ? 'bg-emerald-100 text-emerald-700 ring-emerald-600/20' : 'bg-gray-100 text-gray-600 ring-gray-500/20' }}">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $cliente['ativo'] ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                                            {{ $cliente['ativo'] ? 'Ativo' : 'Inativo' }}
                                        </span>
                                    @endif
                                    @if(!empty($cliente['plano']))
                                        <span class="px-2.5 py-0.5 text-xs font-bold rounded-full bg-blue-100 text-blue-700 ring-1 ring-blue-200 uppercase tracking-wide">
                                            {{ $planoLabels[$cliente['plano']] ?? $cliente['plano'] }}
                                        </span>
                                    @endif
                                    @if($aptoAgendamentos !== null)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 text-xs font-semibold rounded-full ring-1 ring-inset {{ $aptoAgendamentos ? 'bg-emerald-100 text-emerald-700 ring-emerald-600/20' : 'bg-amber-100 text-amber-700 ring-amber-600/20' }}">
                                            {{ $aptoAgendamentos ? 'Apto para agendamentos' : 'Não apto para agendamentos' }}
                                        </span>
                                    @endif
                                </div>
                                <p class="text-gray-600 text-sm lg:text-base">{{ $cliente['email'] ?? '' }}</p>
                                <p class="text-gray-500 text-xs mt-1">ID #{{ $cliente['id'] ?? '-' }} · {{ $cliente['tipo_negocio_label'] ?? $cliente['tipo_negocio'] ?? 'Sem segmento' }}</p>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-2 lg:justify-end">
                            @if(auth()->user()->isAdmin() || auth()->user()->isSuporte())
                                <form method="POST" action="{{ route('clientes.impersonate', $cliente['id']) }}" target="_blank" rel="noopener noreferrer" class="inline">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-white bg-blue-600 rounded-xl hover:bg-blue-700 ring-1 ring-blue-500/50 transition-all">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        Acessar perfil
                                    </button>
                                </form>
                            @endif
                            @if($podeCancelar)
                                <x-cancel-subscription-button :cliente-id="$cliente['id']" variant="hero" />
                            @endif
                            <a href="{{ route('clientes.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-gray-700 bg-white rounded-xl hover:bg-gray-50 ring-1 ring-gray-200 transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                                Voltar
                            </a>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Métricas rápidas --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
                <x-client-stat-card
                    title="Assinatura"
                    :value="$assinatura ? 'R$ ' . number_format($assinatura['price'] ?? 0, 2, ',', '.') : 'Sem plano'"
                    :subtitle="$assinatura ? strtoupper($assinatura['plan'] ?? '') . ' · ' . ($statusLabels[$assinatura['status']] ?? ucfirst($assinatura['status'] ?? '')) : 'Nenhuma assinatura ativa'"
                    theme="blue"
                    icon="wallet"
                    compact
                />
                <x-client-stat-card
                    title="Tickets de suporte"
                    :value="(string) ($suporte['total_tickets'] ?? 0)"
                    :subtitle="($suporte['tickets_abertos'] ?? 0) . ' aberto(s)'"
                    theme="purple"
                    icon="ticket"
                    compact
                />
                <x-client-stat-card
                    title="Equipe"
                    :value="(string) count($equipe)"
                    :subtitle="count($equipe) === 1 ? 'profissional' : 'profissionais'"
                    theme="teal"
                    icon="users"
                    compact
                />
                <x-client-stat-card
                    title="Cliente desde"
                    :value="$cliente['data_criacao_formatada'] ?? '-'"
                    :subtitle="'Atualizado em ' . ($cliente['data_atualizacao_formatada'] ?? '-')"
                    theme="slate"
                    icon="clock"
                    compact
                />
                <x-client-stat-card
                    title="Último acesso"
                    :value="$cliente['ultimo_acesso_em_formatada'] ?? 'Sem registro'"
                    subtitle="Data e hora do último login"
                    theme="amber"
                    icon="clock"
                    compact
                />
                <x-client-stat-card
                    title="Receber agendamentos"
                    :value="$aptoAgendamentos === null ? 'Indefinido' : ($aptoAgendamentos ? 'Apto' : 'Não apto')"
                    :subtitle="$aptoAgendamentos === null ? 'Status não informado' : ($aptoAgendamentos ? 'Página pronta para receber' : 'Configuração incompleta')"
                    :theme="$aptoAgendamentos ? 'green' : ($aptoAgendamentos === null ? 'slate' : 'amber')"
                    icon="trending"
                    compact
                />
            </div>

            {{-- Grid principal --}}
            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 xl:items-stretch">
                <x-client-detail-section title="Contato" icon="mail" theme="blue" class="h-full xl:col-span-1">
                    <div class="space-y-3 h-full">
                            <div class="flex items-start gap-3 p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                                <div class="mt-0.5 text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Email</p>
                                    <p class="text-sm font-semibold text-gray-900 break-all">{{ $cliente['email'] ?? '-' }}</p>
                                    @if(!empty($cliente['email_verificado']))
                                        <span class="inline-flex items-center gap-1 mt-1 text-xs font-medium text-emerald-600">
                                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                            Verificado{{ !empty($cliente['email_verificado_em_formatada']) ? ' em ' . $cliente['email_verificado_em_formatada'] : '' }}
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-400 mt-1 inline-block">Não verificado</span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-start gap-3 p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                                <div class="mt-0.5 text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Telefone</p>
                                    <p class="text-sm font-semibold text-gray-900">{{ $cliente['telefone_formatado'] ?? '-' }}</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3 p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                                <div class="mt-0.5 text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Segmento</p>
                                    <p class="text-sm font-semibold text-gray-900">{{ $cliente['tipo_negocio_label'] ?? $cliente['tipo_negocio'] ?? '-' }}</p>
                                </div>
                            </div>
                        </div>
                </x-client-detail-section>

                <x-client-detail-section title="Assinatura" icon="credit-card" theme="violet" class="h-full xl:col-span-2">
                        @if(!empty($assinatura))
                            @if($cancelamentoAgendado)
                                <div class="mb-4 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                                    <span class="font-semibold">Cancelamento agendado</span>
                                    @if(!empty($assinatura['expires_at_formatada']))
                                        — a assinatura permanece ativa até {{ $assinatura['expires_at_formatada'] }}.
                                    @else
                                        — a assinatura será encerrada ao fim do período atual.
                                    @endif
                                </div>
                            @endif
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="relative overflow-hidden rounded-xl bg-[#eff4ff] border border-blue-100 p-5 shadow-sm">
                                    <p class="text-blue-600 text-xs font-medium uppercase tracking-wider">Plano atual</p>
                                    <p class="text-3xl font-bold mt-1 uppercase text-gray-900">{{ $assinatura['plan'] ?? '-' }}</p>
                                    <p class="text-4xl font-bold mt-3 text-gray-900">R$ {{ number_format($assinatura['price'] ?? 0, 2, ',', '.') }}<span class="text-lg font-normal text-blue-600">/mês</span></p>
                                    <div class="mt-4">
                                        <span class="inline-flex px-2.5 py-1 text-xs font-semibold rounded-full ring-1 ring-inset {{ $statusStyles[$assinatura['status']] ?? 'bg-blue-100 text-blue-700 ring-blue-600/20' }}">
                                            {{ $statusLabels[$assinatura['status']] ?? ucfirst($assinatura['status'] ?? '-') }}
                                        </span>
                                    </div>
                                </div>
                                <div class="space-y-3">
                                    <div class="flex justify-between items-center p-3.5 rounded-xl bg-violet-50 border border-violet-100">
                                        <span class="text-sm text-violet-600 font-medium">Início</span>
                                        <span class="text-sm font-semibold text-gray-900">{{ $assinatura['started_at_formatada'] ?? '-' }}</span>
                                    </div>
                                    <div class="flex justify-between items-center p-3.5 rounded-xl bg-violet-50 border border-violet-100">
                                        <span class="text-sm text-violet-600 font-medium">Expira em</span>
                                        <span class="text-sm font-semibold text-gray-900">{{ $assinatura['expires_at_formatada'] ?? '-' }}</span>
                                    </div>
                                    @if(!empty($assinatura['stripe_subscription_id']))
                                        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Stripe ID</p>
                                            <p class="text-xs font-mono text-gray-700 break-all">{{ $assinatura['stripe_subscription_id'] }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            @if($podeCancelar)
                                <div class="mt-4 pt-4 border-t border-gray-100">
                                    <x-cancel-subscription-button :cliente-id="$cliente['id']" variant="section" />
                                </div>
                            @endif
                        @else
                            <div class="flex flex-col items-center justify-center h-full min-h-[200px] py-10 text-center">
                                <div class="w-14 h-14 rounded-2xl bg-violet-50 flex items-center justify-center text-violet-400 mb-3">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                </div>
                                <p class="text-sm font-medium text-gray-500">Sem assinatura ativa</p>
                            </div>
                        @endif
                </x-client-detail-section>
            </div>

            @if(!empty($configInicial))
                @php
                    $progresso = (int) ($configInicial['progresso_percentual'] ?? 0);
                    $passosConcluidos = (int) ($configInicial['passos_concluidos'] ?? 0);
                    $totalPassos = (int) ($configInicial['total_passos'] ?? count($passosConfigInicial));
                @endphp
                <x-client-detail-section title="Configuração inicial" icon="clipboard" theme="rose">
                    <div class="space-y-5">
                        <div class="rounded-xl bg-rose-50 border border-rose-100 p-5">
                            <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3 mb-3">
                                <div>
                                    <p class="text-xs font-medium text-rose-600 uppercase tracking-wide">Progresso do onboarding</p>
                                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $progresso }}%</p>
                                </div>
                                <p class="text-sm font-medium text-gray-600">
                                    {{ $passosConcluidos }}/{{ $totalPassos }} passos concluídos
                                </p>
                            </div>
                            <div class="h-2.5 rounded-full bg-white/80 overflow-hidden">
                                <div class="h-full rounded-full bg-rose-500 transition-all" style="width: {{ min(100, max(0, $progresso)) }}%"></div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                            @foreach($passosConfigInicial as $campo => $label)
                                @php $concluido = !empty($configInicial[$campo]); @endphp
                                <div class="flex items-center justify-between gap-3 p-3.5 rounded-xl border {{ $concluido ? 'bg-emerald-50 border-emerald-100' : 'bg-slate-50 border-slate-100' }}">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg {{ $concluido ? 'bg-emerald-100 text-emerald-600' : 'bg-white text-gray-400 ring-1 ring-gray-200' }}">
                                            @if($concluido)
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                            @else
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            @endif
                                        </span>
                                        <p class="text-sm font-medium text-gray-900 truncate">{{ $label }}</p>
                                    </div>
                                    <span class="shrink-0 text-xs font-semibold {{ $concluido ? 'text-emerald-700' : 'text-gray-500' }}">
                                        {{ $concluido ? 'Concluído' : 'Pendente' }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </x-client-detail-section>
            @endif

            {{-- Conta, Página Pública e Suporte --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <x-client-detail-section title="Conta" icon="shield" theme="emerald" class="h-full">
                    <div class="divide-y divide-gray-100 rounded-xl border border-gray-100 overflow-hidden">
                        <div class="flex items-center justify-between gap-4 px-4 py-3.5 bg-[#eff4ff]/40">
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-gray-900">Termos de uso</p>
                                <p class="text-xs text-gray-500 mt-0.5">Aceite dos termos da plataforma</p>
                            </div>
                            <span class="shrink-0 inline-flex px-2.5 py-1 text-xs font-semibold rounded-full {{ ($cliente['aceitou_termos'] ?? false) ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-600' }}">
                                {{ ($cliente['aceitou_termos'] ?? false) ? 'Aceitos' : 'Pendente' }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between gap-4 px-4 py-3.5 bg-white">
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-gray-900">Newsletter</p>
                                <p class="text-xs text-gray-500 mt-0.5">Comunicações por e-mail</p>
                            </div>
                            <span class="shrink-0 inline-flex px-2.5 py-1 text-xs font-semibold rounded-full {{ ($cliente['newsletter'] ?? false) ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600' }}">
                                {{ ($cliente['newsletter'] ?? false) ? 'Inscrito' : 'Não inscrito' }}
                            </span>
                        </div>
                    </div>
                </x-client-detail-section>

                @if(!empty($pagina))
                    <x-client-detail-section title="Página Pública" icon="globe" theme="teal" class="h-full">
                        <div class="flex flex-col h-full gap-4">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="text-lg font-bold text-gray-900 leading-tight">{{ $pagina['title'] ?? '-' }}</p>
                                    @if(!empty($pagina['slug']))
                                        <p class="text-xs font-mono text-gray-500 mt-1 truncate">/p/{{ $pagina['slug'] }}</p>
                                    @endif
                                </div>
                                <span class="shrink-0 inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded-full {{ ($pagina['active'] ?? false) ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-600' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ ($pagina['active'] ?? false) ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                                    {{ ($pagina['active'] ?? false) ? 'Ativa' : 'Inativa' }}
                                </span>
                            </div>

                            @if(!empty($pagina['url']))
                                <a href="{{ $pagina['url'] }}"
                                   target="_blank" rel="noopener noreferrer"
                                   class="block p-3 rounded-xl bg-[#eff4ff] border border-blue-100 hover:border-blue-200 transition-colors group">
                                    <p class="text-xs font-medium text-blue-600 mb-1">URL pública</p>
                                    <p class="text-sm font-mono text-gray-800 break-all group-hover:text-blue-700">{{ $pagina['url'] }}</p>
                                </a>
                                <a href="{{ $pagina['url'] }}"
                                   target="_blank" rel="noopener noreferrer"
                                   class="mt-auto inline-flex items-center justify-center gap-2 w-full px-4 py-2.5 text-sm font-semibold text-white bg-teal-600 rounded-xl hover:bg-teal-700 transition-colors">
                                    Abrir página pública
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                </a>
                            @endif
                        </div>
                    </x-client-detail-section>
                @endif

                @if(!empty($suporte))
                    <x-client-detail-section title="Suporte" icon="ticket" theme="amber" class="h-full {{ empty($pagina) ? 'lg:col-span-2' : '' }}">
                        <div class="flex flex-col h-full gap-4">
                            <div class="grid grid-cols-2 gap-3">
                                <div class="flex items-center gap-3 p-3.5 rounded-xl bg-[#eff4ff] border border-blue-100">
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-white text-amber-600 shadow-sm">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    </div>
                                    <div>
                                        <p class="text-2xl font-bold text-gray-900 leading-none">{{ $suporte['total_tickets'] ?? 0 }}</p>
                                        <p class="text-xs font-medium text-gray-500 mt-1">Total</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3 p-3.5 rounded-xl bg-[#eff4ff] border border-blue-100">
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-white text-blue-600 shadow-sm">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                    <div>
                                        <p class="text-2xl font-bold text-blue-600 leading-none">{{ $suporte['tickets_abertos'] ?? 0 }}</p>
                                        <p class="text-xs font-medium text-gray-500 mt-1">Abertos</p>
                                    </div>
                                </div>
                            </div>

                            @if(!empty($suporte['ultimo_ticket']))
                                @php
                                    $ticket = $suporte['ultimo_ticket'];
                                    $ticketStatus = strtolower($ticket['status'] ?? '');
                                @endphp
                                <div class="flex-1 rounded-xl border border-gray-100 bg-slate-50/80 p-4">
                                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">Último ticket</p>
                                    <p class="text-sm font-semibold text-gray-900 leading-snug line-clamp-2">{{ $ticket['assunto'] ?? '-' }}</p>
                                    <div class="flex flex-wrap items-center gap-2 mt-3">
                                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full {{ $ticketStatusStyles[$ticketStatus] ?? 'bg-gray-100 text-gray-700' }}">
                                            {{ $ticketStatusLabels[$ticketStatus] ?? ucfirst($ticket['status'] ?? '-') }}
                                        </span>
                                        @if(!empty($ticket['criado_em_formatada']))
                                            <span class="text-xs text-gray-400">{{ $ticket['criado_em_formatada'] }}</span>
                                        @endif
                                    </div>
                                    @if(!empty($ticket['id']))
                                        <a href="{{ route('suporte.visualizar', $ticket['id']) }}" class="inline-flex items-center gap-1 mt-3 text-sm font-semibold text-blue-600 hover:text-blue-800">
                                            Ver ticket
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                        </a>
                                    @endif
                                </div>
                            @else
                                <div class="flex-1 flex flex-col items-center justify-center rounded-xl border border-dashed border-gray-200 bg-slate-50/50 py-8 text-center">
                                    <p class="text-sm font-medium text-gray-500">Nenhum ticket registrado</p>
                                    <p class="text-xs text-gray-400 mt-1">O histórico aparecerá aqui</p>
                                </div>
                            @endif
                        </div>
                    </x-client-detail-section>
                @endif
            </div>

            {{-- Equipe --}}
            <x-client-detail-section title="Profissionais da Equipe" icon="users" theme="slate">
                @if(count($equipe) > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($equipe as $profissional)
                            @php
                                $profIniciais = collect(explode(' ', $profissional['nome'] ?? 'P'))
                                    ->filter()->take(2)
                                    ->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))
                                    ->implode('');
                            @endphp
                            <div class="flex items-center gap-4 p-4 rounded-xl border border-gray-100 bg-gradient-to-br from-white to-slate-50 hover:shadow-md hover:border-slate-200 transition-all">
                                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-indigo-100 text-indigo-700 font-bold text-sm">
                                    {{ $profIniciais ?: 'P' }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-semibold text-gray-900 truncate">{{ $profissional['nome'] ?? '-' }}</p>
                                    <p class="text-xs text-gray-500 truncate">{{ $profissional['email'] ?? '-' }}</p>
                                    <p class="text-xs text-gray-400 mt-0.5">{{ $profissional['telefone_formatado'] ?? $profissional['telefone'] ?? '-' }}</p>
                                </div>
                                <span class="shrink-0 px-2 py-1 text-xs font-semibold rounded-full {{ ($profissional['ativo'] ?? false) ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-600' }}">
                                    {{ ($profissional['ativo'] ?? false) ? 'Ativo' : 'Inativo' }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="flex flex-col items-center justify-center py-12 text-center">
                        <div class="w-16 h-16 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-400 mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <p class="text-sm font-medium text-gray-500">Nenhum profissional cadastrado</p>
                        <p class="text-xs text-gray-400 mt-1">A equipe aparecerá aqui quando houver membros</p>
                    </div>
                @endif
            </x-client-detail-section>

        </div>
    </main>
</div>
@endsection
