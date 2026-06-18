@extends('layouts.app')

@section('content')
<div class="flex min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <x-sidebar />
    <x-header />

    <!-- Main Content -->
    <main class="flex-1 lg:ml-3 mt-16 overflow-y-auto">
        <div class="p-4 lg:p-8">
            <!-- Page Header -->
            <div class="mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Dashboard</h1>
                    <p class="text-gray-600">Bem-vindo ao seu painel de controle</p>
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

            @if($usuarioStatsError)
                <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 text-yellow-800 rounded-lg">
                    {{ $usuarioStatsError }}
                </div>
            @endif

            @if($usuarioStats)
            @php
                $stats = $usuarioStatsHighlights;
            @endphp
            <div class="mb-8 space-y-5">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
                    @if(!empty($stats['total_clientes']))
                        <x-client-stat-card
                            :title="$stats['total_clientes']['label']"
                            :value="$stats['total_clientes']['value']"
                            theme="slate"
                            icon="users"
                            class="min-h-[140px]"
                        />
                    @endif

                    @if(!empty($stats['em_operacao']))
                        <x-client-stat-card
                            :title="$stats['em_operacao']['label']"
                            :value="$stats['em_operacao']['valor']"
                            :subtitle="$stats['em_operacao']['quantidade'] . ' clientes'"
                            theme="green"
                            icon="trending"
                            class="min-h-[140px]"
                        />
                    @endif

                    @if(!empty($stats['total_geral']))
                        <x-client-stat-card
                            :title="$stats['total_geral']['label']"
                            :value="$stats['total_geral']['valor']"
                            :subtitle="$stats['total_geral']['quantidade'] . ' clientes'"
                            theme="blue"
                            icon="wallet"
                            class="min-h-[140px]"
                        />
                    @endif
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    @if(!empty($stats['em_trial']))
                        <x-client-stat-card
                            :title="$stats['em_trial']['label']"
                            :value="$stats['em_trial']['quantidade']"
                            theme="amber"
                            icon="clock"
                            compact
                        />
                    @endif

                    @if(!empty($stats['canceladas']))
                        <x-client-stat-card
                            :title="$stats['canceladas']['label']"
                            :value="$stats['canceladas']['quantidade']"
                            theme="red"
                            icon="x-circle"
                            compact
                        />
                    @endif
                </div>
            </div>
            @endif

            <!-- Estatísticas de Atendimento (Apenas Admin) -->
            @if($user->isAdmin() && $estatisticas)
            <div class="mb-8">
                <div class="mb-5">
                    <h2 class="text-2xl font-bold text-gray-900 mb-1">Estatísticas de Atendimento</h2>
                    <p class="text-gray-600 text-sm">Métricas de desempenho do suporte</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5 mb-8">
                    <x-client-stat-card
                        title="Tickets Atendidos"
                        :value="(string) $estatisticas['total_tickets_atendidos']"
                        theme="blue"
                        icon="ticket"
                        compact
                    />

                    <x-client-stat-card
                        title="Mensagens Respondidas"
                        :value="(string) $estatisticas['total_mensagens_respondidas']"
                        theme="green"
                        icon="message"
                        compact
                    />

                    <x-client-stat-card
                        title="Tempo Médio de Resposta"
                        :value="number_format($estatisticas['tempo_medio_resposta'], 0) . ' min'"
                        theme="purple"
                        icon="clock"
                        compact
                    />
                </div>

                <div class="card p-8">
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
                            <p class="text-gray-500 text-sm">0</p>
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
                            <p class="text-gray-500 text-sm">0</p>
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
