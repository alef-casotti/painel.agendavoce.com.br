@extends('layouts.app')

@section('content')
<div class="flex min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <x-sidebar />
    <x-header />

    <!-- Main Content -->
    <main class="flex-1 lg:ml-3 mt-16 overflow-y-auto">
        <div class="p-4 lg:p-8">
            @php
                $isFinanceUser = $user->isAdmin() || $user->isFinanceiro();
                $monthNames = [
                    '01' => 'Janeiro',
                    '02' => 'Fevereiro',
                    '03' => 'Março',
                    '04' => 'Abril',
                    '05' => 'Maio',
                    '06' => 'Junho',
                    '07' => 'Julho',
                    '08' => 'Agosto',
                    '09' => 'Setembro',
                    '10' => 'Outubro',
                    '11' => 'Novembro',
                    '12' => 'Dezembro',
                ];

                $healthHighlights = $financeMetricsHighlights['health'] ?? [];
                $growthHighlights = $financeMetricsHighlights['growth'] ?? [];
                $acquisitionHighlights = $financeMetricsHighlights['acquisition'] ?? [];
            @endphp

            <!-- Page Header -->
            <div class="mb-8">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 mb-2">Dashboard</h1>
                        <p class="text-gray-600">Bem-vindo ao seu painel de controle</p>
                    </div>

                    @if($isFinanceUser)
                        <div class="card p-4 md:min-w-[540px]">
                            <form method="GET" action="{{ route('dashboard') }}" class="flex flex-col gap-4 md:flex-row md:items-end">
                                <div class="flex-1">
                                    <label for="filtro_mes" class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-2">Mês</label>
                                    <select id="filtro_mes" name="filtro_mes"
                                            class="w-full rounded-lg border-gray-300 focus:border-yellow-400 focus:ring focus:ring-yellow-200 focus:ring-opacity-50">
                                        @foreach($monthNames as $value => $label)
                                            <option value="{{ $value }}" {{ (int) $selectedFilterMonth === (int) $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="flex-1">
                                    <label for="filtro_ano" class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-2">Ano</label>
                                    <select id="filtro_ano" name="filtro_ano"
                                            class="w-full rounded-lg border-gray-300 focus:border-yellow-400 focus:ring focus:ring-yellow-200 focus:ring-opacity-50">
                                        @for($year = now()->year; $year >= now()->year - 5; $year--)
                                            <option value="{{ $year }}" {{ (int) $selectedFilterYear === (int) $year ? 'selected' : '' }}>
                                                {{ $year }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>

                                <div class="flex items-center gap-3 md:gap-4">
                                    <button type="submit"
                                            class="inline-flex items-center px-4 py-2 bg-yellow-500 text-white text-sm font-semibold rounded-lg hover:bg-yellow-600 transition-colors whitespace-nowrap">
                                        Aplicar Filtro
                                    </button>
                                    <a href="{{ route('dashboard') }}"
                                       class="text-xs text-gray-500 hover:text-gray-700 font-medium underline whitespace-nowrap">
                                        Limpar
                                    </a>
                                </div>
                            </form>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Messages -->
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            @if($financeMetricsError)
                <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 text-yellow-800 rounded-lg">
                    {{ $financeMetricsError }}
                </div>
            @endif

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-{{ $isFinanceUser ? '4' : '3' }} gap-6 mb-8">
                <div class="card p-6 group hover:scale-105 transition-transform">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="p-3 bg-gradient-to-br from-blue-100 to-blue-200 rounded-xl group-hover:from-blue-200 group-hover:to-blue-300 transition-colors">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-gray-500 text-sm font-medium">Usuário</p>
                                <p class="text-gray-900 font-bold text-lg mt-1">{{ $user->name }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card p-6 group hover:scale-105 transition-transform">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="p-3 bg-gradient-to-br from-green-100 to-green-200 rounded-xl group-hover:from-green-200 group-hover:to-green-300 transition-colors">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-gray-500 text-sm font-medium">Nível de Acesso</p>
                                <p class="text-gray-900 font-bold text-lg mt-1">{{ ucfirst($user->role) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card p-6 group hover:scale-105 transition-transform">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="p-3 bg-gradient-to-br from-purple-100 to-purple-200 rounded-xl group-hover:from-purple-200 group-hover:to-purple-300 transition-colors">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-gray-500 text-sm font-medium">E-mail</p>
                                <p class="text-gray-900 font-bold text-sm mt-1 break-all">{{ $user->email }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                @if($isFinanceUser)
                    <div class="card p-6">
                        <div class="flex items-center">
                            <div class="p-3 bg-gradient-to-br from-yellow-100 to-yellow-200 rounded-xl">
                                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-gray-500 text-sm font-medium">Período de Referência</p>
                                <p class="text-gray-900 font-bold text-sm mt-1">{{ $dashboardPeriodLabel }}</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            @if(($user->isAdmin() || $user->isFinanceiro()) && $financeMetrics)
            <div class="card p-8 mb-8">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 mb-1">Resumo Financeiro</h2>
                        <p class="text-gray-600 text-sm">Principais indicadores financeiros do período</p>
                    </div>
                </div>

                @if(!empty($healthHighlights))
                    <section class="mb-10">
                        <div class="mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">🚨 1. Métricas de Saúde Financeira</h3>
                            <p class="text-sm text-gray-500 mt-1">Saúde do caixa, lucratividade e capacidade de sustentar a operação.</p>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                            @if(!empty($healthHighlights['mrr']))
                                @php
                                    $card = $healthHighlights['mrr'];
                                    $style = $card['style'] ?? 'neutral';
                                    $isNegative = $style === 'negative';
                                    $containerClasses = $isNegative
                                        ? 'bg-gradient-to-br from-red-50 to-red-100 border border-red-200'
                                        : 'bg-gradient-to-br from-green-50 to-green-100 border border-green-200';
                                    $labelClasses = $isNegative ? 'text-red-600' : 'text-green-600';
                                    $metaLabelClasses = $isNegative ? 'text-red-700' : 'text-green-700';
                                    $metaValueClasses = $isNegative ? 'text-red-800' : 'text-green-800';
                                @endphp
                                <div class="{{ $containerClasses }} p-6 rounded-xl">
                                    <span class="{{ $labelClasses }} text-sm font-medium">{{ $card['label'] }}</span>
                                    <p class="text-gray-600 text-xs mt-2 leading-relaxed">{{ $card['description'] }}</p>
                                    <p class="text-3xl font-bold text-gray-900 mt-4">{{ $card['value'] }}</p>
                                    @if(!empty($card['meta']))
                                        <div class="mt-4 p-3 bg-white bg-opacity-60 border {{ $isNegative ? 'border-red-200' : 'border-green-200' }} rounded-lg">
                                            <div class="flex items-center justify-between">
                                                <span class="text-xs font-semibold {{ $metaLabelClasses }}">{{ $card['meta']['label'] }}</span>
                                                <span class="text-sm font-bold {{ $metaValueClasses }}">{{ $card['meta']['value'] }}</span>
                                            </div>
                                            @if(!empty($card['meta']['hint']))
                                                <p class="text-xs text-gray-600 mt-2">{{ $card['meta']['hint'] }}</p>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @endif

                            @if(!empty($healthHighlights['churn']))
                                @php
                                    $card = $healthHighlights['churn'];
                                    $style = $card['style'] ?? 'neutral';
                                    $isNegative = $style === 'negative';
                                    $containerClasses = $isNegative
                                        ? 'bg-gradient-to-br from-red-50 to-red-100 border border-red-200'
                                        : 'bg-gradient-to-br from-green-50 to-green-100 border border-green-200';
                                    $labelClasses = $isNegative ? 'text-red-600' : 'text-green-600';
                                    $metaLabelClasses = $isNegative ? 'text-red-700' : 'text-green-700';
                                    $metaValueClasses = $isNegative ? 'text-red-800' : 'text-green-800';
                                @endphp
                                <div class="{{ $containerClasses }} p-6 rounded-xl">
                                    <span class="{{ $labelClasses }} text-sm font-medium">{{ $card['label'] }}</span>
                                    <p class="text-gray-600 text-xs mt-2 leading-relaxed">{{ $card['description'] }}</p>
                                    <p class="text-3xl font-bold text-gray-900 mt-4">{{ $card['value'] }}</p>
                                    @if(!empty($card['meta']))
                                        <div class="mt-4 p-3 bg-white bg-opacity-60 border {{ $isNegative ? 'border-red-200' : 'border-green-200' }} rounded-lg">
                                            <div class="flex items-center justify-between">
                                                <span class="text-xs font-semibold {{ $metaLabelClasses }}">{{ $card['meta']['label'] }}</span>
                                                <span class="text-sm font-bold {{ $metaValueClasses }}">{{ $card['meta']['value'] }}</span>
                                            </div>
                                            @if(!empty($card['meta']['hint']))
                                                <p class="text-xs text-gray-600 mt-2">{{ $card['meta']['hint'] }}</p>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </section>
                @endif

                @if(!empty($growthHighlights))
                    <section class="mb-10">
                        <div class="mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">🚀 2. Métricas de Crescimento e Retenção</h3>
                            <p class="text-sm text-gray-500 mt-1">Acompanhe expansão da base, retenção e poder recorrente.</p>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                            @if(!empty($growthHighlights['clientes_ativos']))
                                @php
                                    $card = $growthHighlights['clientes_ativos'];
                                    $style = $card['style'] ?? 'neutral';
                                    $isNegative = $style === 'negative';
                                    $containerClasses = $isNegative
                                        ? 'bg-gradient-to-br from-red-50 to-red-100 border border-red-200'
                                        : 'bg-gradient-to-br from-orange-50 to-orange-100 border border-orange-200';
                                    $labelClasses = $isNegative ? 'text-red-600' : 'text-orange-600';
                                    $metaLabelClasses = $isNegative ? 'text-red-700' : 'text-orange-700';
                                    $metaValueClasses = $isNegative ? 'text-red-800' : 'text-orange-800';
                                @endphp
                                <div class="{{ $containerClasses }} p-6 rounded-xl">
                                    <span class="{{ $labelClasses }} text-sm font-medium">{{ $card['label'] }}</span>
                                    <p class="text-gray-600 text-xs mt-2 leading-relaxed">{{ $card['description'] }}</p>
                                    <p class="text-3xl font-bold text-gray-900 mt-4">{{ $card['value'] }}</p>
                                    @if(!empty($card['meta']))
                                        <div class="mt-4 p-3 bg-white bg-opacity-60 border {{ $isNegative ? 'border-red-200' : 'border-green-200' }} rounded-lg">
                                            <div class="flex items-center justify-between">
                                                <span class="text-xs font-semibold {{ $metaLabelClasses }}">{{ $card['meta']['label'] }}</span>
                                                <span class="text-sm font-bold {{ $metaValueClasses }}">{{ $card['meta']['value'] }}</span>
                                            </div>
                                            @if(!empty($card['meta']['hint']))
                                                <p class="text-xs text-gray-600 mt-2">{{ $card['meta']['hint'] }}</p>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </section>
                @endif

                <section class="mb-10">
                    <div class="mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">💵 3. Métricas de Aquisição e Marketing</h3>
                        <p class="text-sm text-gray-500 mt-1">Indicadores que medem investimento em marketing e geração de novas oportunidades.</p>
                    </div>

                    @if(!empty($acquisitionHighlights))
                        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                            @if(!empty($acquisitionHighlights['novos_clientes']))
                                @php
                                    $card = $acquisitionHighlights['novos_clientes'];
                                    $style = $card['style'] ?? 'neutral';
                                    $isNegative = $style === 'negative';
                                    $containerClasses = $isNegative
                                        ? 'bg-gradient-to-br from-red-50 to-red-100 border border-red-200'
                                        : 'bg-gradient-to-br from-purple-50 to-purple-100 border border-purple-200';
                                    $labelClasses = $isNegative ? 'text-red-600' : 'text-purple-600';
                                @endphp
                                <div class="{{ $containerClasses }} p-6 rounded-xl">
                                    <span class="{{ $labelClasses }} text-sm font-medium">{{ $card['label'] }}</span>
                                    <p class="text-gray-600 text-xs mt-2 leading-relaxed">{{ $card['description'] ?? '' }}</p>
                                    <p class="text-3xl font-bold text-gray-900 mt-4">{{ $card['value'] }}</p>
                                </div>
                            @endif

                            @if(!empty($acquisitionHighlights['trial_conversion']))
                                @php
                                    $card = $acquisitionHighlights['trial_conversion'];
                                    $style = $card['style'] ?? 'neutral';
                                    $isNegative = $style === 'negative';
                                    $containerClasses = $isNegative
                                        ? 'bg-gradient-to-br from-red-50 to-red-100 border border-red-200'
                                        : 'bg-gradient-to-br from-purple-50 to-purple-100 border border-purple-200';
                                    $labelClasses = $isNegative ? 'text-red-600' : 'text-purple-600';
                                    $metaLabelClasses = $isNegative ? 'text-red-700' : 'text-purple-700';
                                    $metaValueClasses = $isNegative ? 'text-red-800' : 'text-purple-800';
                                @endphp
                                <div class="{{ $containerClasses }} p-6 rounded-xl">
                                    <span class="{{ $labelClasses }} text-sm font-medium">{{ $card['label'] }}</span>
                                    <p class="text-gray-600 text-xs mt-2 leading-relaxed">{{ $card['description'] ?? '' }}</p>
                                    <p class="text-3xl font-bold text-gray-900 mt-4">{{ $card['value'] }}</p>
                                    @if(!empty($card['meta']))
                                        <div class="mt-4 p-3 bg-white bg-opacity-60 border {{ $isNegative ? 'border-red-200' : 'border-purple-200' }} rounded-lg">
                                            <div class="flex items-center justify-between">
                                                <span class="text-xs font-semibold {{ $metaLabelClasses }}">{{ $card['meta']['label'] }}</span>
                                                <span class="text-sm font-bold {{ $metaValueClasses }}">{{ $card['meta']['value'] }}</span>
                                            </div>
                                            @if(!empty($card['meta']['hint']))
                                                <p class="text-xs text-gray-600 mt-2">{{ $card['meta']['hint'] }}</p>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="p-6 rounded-xl border border-blue-200 bg-gradient-to-br from-blue-50 to-blue-100 text-sm text-blue-700">
                            Ainda não há métricas cadastradas para aquisição e marketing neste período. Conecte suas fontes de marketing ou registre investimentos para acompanhar estes indicadores.
                        </div>
                    @endif
                </section>

            </div>
            @endif

            <!-- Estatísticas de Atendimento (Apenas Admin) -->
            @if($user->isAdmin() && $estatisticas)
            <div class="card p-8 mb-8">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 mb-1">Estatísticas de Atendimento</h2>
                        <p class="text-gray-600 text-sm">Métricas de desempenho do suporte</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-6 rounded-xl border border-blue-200">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-blue-600 text-sm font-medium">Tickets Atendidos</span>
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <p class="text-3xl font-bold text-blue-900">{{ $estatisticas['total_tickets_atendidos'] }}</p>
                    </div>

                    <div class="bg-gradient-to-br from-green-50 to-green-100 p-6 rounded-xl border border-green-200">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-green-600 text-sm font-medium">Mensagens Respondidas</span>
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                        </div>
                        <p class="text-3xl font-bold text-green-900">{{ $estatisticas['total_mensagens_respondidas'] }}</p>
                    </div>

                    <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-6 rounded-xl border border-purple-200">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-purple-600 text-sm font-medium">Tempo Médio de Resposta</span>
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <p class="text-3xl font-bold text-purple-900">
                            @if($estatisticas['tempo_medio_resposta'] > 0)
                                {{ number_format($estatisticas['tempo_medio_resposta'], 0) }} min
                            @else
                                N/A
                            @endif
                        </p>
                    </div>

                    <div class="bg-gradient-to-br from-orange-50 to-orange-100 p-6 rounded-xl border border-orange-200">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-orange-600 text-sm font-medium">Top Atendentes</span>
                            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <p class="text-3xl font-bold text-orange-900">{{ $estatisticas['atendentes']->count() }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Top Atendentes -->
                    <div class="bg-white p-6 rounded-xl border border-gray-200">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Top 5 - Quem Atendeu Mais Tickets</h3>
                        @if($estatisticas['atendentes']->count() > 0)
                            <div class="space-y-3">
                                @foreach($estatisticas['atendentes'] as $index => $atendente)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center font-bold mr-3">
                                            {{ $index + 1 }}
                                        </div>
                                        <span class="text-gray-900 font-medium">{{ $atendente['nome'] }}</span>
                                    </div>
                                    <span class="text-blue-600 font-bold">{{ $atendente['total'] }} tickets</span>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 text-sm">Nenhum dado disponível ainda.</p>
                        @endif
                    </div>

                    <!-- Top Respondentes -->
                    <div class="bg-white p-6 rounded-xl border border-gray-200">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Top 5 - Quem Respondeu Mais Mensagens</h3>
                        @if($estatisticas['respondentes']->count() > 0)
                            <div class="space-y-3">
                                @foreach($estatisticas['respondentes'] as $index => $respondente)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-green-100 text-green-600 rounded-full flex items-center justify-center font-bold mr-3">
                                            {{ $index + 1 }}
                                        </div>
                                        <span class="text-gray-900 font-medium">{{ $respondente['nome'] }}</span>
                                    </div>
                                    <span class="text-green-600 font-bold">{{ $respondente['total'] }} mensagens</span>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 text-sm">Nenhum dado disponível ainda.</p>
                        @endif
                    </div>
                </div>

                <!-- Tempo Médio por Atendente -->
                @if($estatisticas['tempo_medio_por_atendente']->count() > 0)
                <div class="mt-6 bg-white p-6 rounded-xl border border-gray-200">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Tempo Médio de Resposta por Atendente</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                        @foreach($estatisticas['tempo_medio_por_atendente'] as $atendente)
                        <div class="p-4 bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg border border-purple-200">
                            <p class="text-sm text-purple-600 font-medium mb-1">{{ $atendente['nome'] }}</p>
                            <p class="text-2xl font-bold text-purple-900">{{ number_format($atendente['tempo_medio'], 0) }} min</p>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
            @endif

            <!-- Areas Section -->
            <div class="card p-8">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 mb-1">Áreas Disponíveis</h2>
                        <p class="text-gray-600 text-sm">Selecione uma área para acessar</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @if($user->isAdmin() || $user->isFinanceiro())
                        <a href="{{ route('financeiro.index') }}" 
                           class="card p-6 border-2 border-transparent hover:border-green-200 hover:shadow-lg transition-all group">
                            <div class="flex items-start justify-between mb-4">
                                <div class="p-3 bg-gradient-to-br from-green-100 to-green-200 rounded-xl group-hover:from-green-200 group-hover:to-green-300 transition-colors">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <svg class="w-5 h-5 text-gray-400 group-hover:text-green-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 mb-2">Área Financeira</h3>
                            <p class="text-gray-600 text-sm mb-4 leading-relaxed">Acesso completo à área financeira do sistema</p>
                            <span class="text-green-600 text-sm font-semibold group-hover:underline inline-flex items-center">
                                Acessar
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </span>
                        </a>
                    @endif

                    @if($user->isAdmin() || $user->isSuporte())
                        <a href="{{ route('suporte.index') }}" 
                           class="card p-6 border-2 border-transparent hover:border-blue-200 hover:shadow-lg transition-all group">
                            <div class="flex items-start justify-between mb-4">
                                <div class="p-3 bg-gradient-to-br from-blue-100 to-blue-200 rounded-xl group-hover:from-blue-200 group-hover:to-blue-300 transition-colors">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                    </svg>
                                </div>
                                <svg class="w-5 h-5 text-gray-400 group-hover:text-blue-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 mb-2">Área de Suporte</h3>
                            <p class="text-gray-600 text-sm mb-4 leading-relaxed">Acesso completo à área de suporte do sistema</p>
                            <span class="text-blue-600 text-sm font-semibold group-hover:underline inline-flex items-center">
                                Acessar
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </span>
                        </a>
                    @endif

                    @if($user->isAdmin())
                        <a href="{{ route('admin.users.index') }}" 
                           class="card p-6 border-2 border-transparent hover:border-red-200 hover:shadow-lg transition-all group">
                            <div class="flex items-start justify-between mb-4">
                                <div class="p-3 bg-gradient-to-br from-red-100 to-red-200 rounded-xl group-hover:from-red-200 group-hover:to-red-300 transition-colors">
                                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                    </svg>
                                </div>
                                <svg class="w-5 h-5 text-gray-400 group-hover:text-red-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 mb-2">Usuários</h3>
                            <p class="text-gray-600 text-sm mb-4 leading-relaxed">Gerencie os usuários e seus acessos no sistema</p>
                            <span class="text-red-600 text-sm font-semibold group-hover:underline inline-flex items-center">
                                Acessar
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </span>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </main>
</div>
@endsection
