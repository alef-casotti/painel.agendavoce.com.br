@extends('layouts.app')

@section('content')
@php
    $filtroAtivo = $filtroAtivo ?? 'todos';
    $busca = $busca ?? '';
    $statusFiltro = $statusFiltro ?? '';
@endphp
<div class="flex min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <x-sidebar />
    <x-header />

    <main class="flex-1 lg:ml-3 mt-16 overflow-y-auto">
        <div class="p-4 lg:p-8 space-y-6">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Clientes</h1>
                    <p class="text-gray-600">Acompanhe saúde da base e principais interações.</p>
                </div>
                <div class="flex flex-col gap-3 sm:flex-row">
                </div>
            </div>

            <section class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @php
                    $filtros = [
                        'Total Clientes' => 'todos',
                        'Onboardings em andamento' => 'onboarding',
                        'Risco de churn' => 'risco'
                    ];
                @endphp
                
                @foreach($metrics as $metric)
                    @php
                        $filtro = $filtros[$metric['label']] ?? 'todos';
                        $filtroSelecionado = ($filtroAtivo ?? 'todos') === $filtro;
                    @endphp
                    <a href="{{ route('clientes.index', ['filtro' => $filtro]) }}" 
                       class="card p-5 cursor-pointer transition-all duration-200 hover:shadow-lg {{ $filtroSelecionado ? 'ring-2 ring-blue-500 shadow-md' : '' }}">
                        <p class="text-sm font-medium text-gray-500">{{ $metric['label'] }}</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ $metric['value'] }}</p>
                        <p class="text-sm mt-2 {{ $metric['trend_positive'] ? 'text-emerald-600' : 'text-rose-600' }}">
                            {{ $metric['trend'] }}
                        </p>
                    </a>
                @endforeach
            </section>

            <section class="card p-6 space-y-4">
                @php
                    $temFiltroAtivo = (isset($filtroAtivo) && $filtroAtivo !== 'todos') || 
                                     (!empty($busca)) || 
                                     (!empty($statusFiltro));
                @endphp
                
                @if($temFiltroAtivo)
                    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-r-lg mb-4">
                        <div class="flex items-center justify-between flex-wrap gap-2">
                            <div class="flex items-center flex-wrap gap-2">
                                <svg class="w-5 h-5 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                                </svg>
                                <p class="text-sm font-medium text-blue-800">
                                    Filtros ativos:
                                    @if(isset($filtroAtivo) && $filtroAtivo !== 'todos')
                                        <span class="font-bold">
                                            @if($filtroAtivo === 'onboarding') Onboardings em andamento
                                            @elseif($filtroAtivo === 'risco') Risco de churn
                                            @else {{ ucfirst($filtroAtivo) }}
                                            @endif
                                        </span>
                                    @endif
                                    @if(!empty($busca))
                                        @if(isset($filtroAtivo) && $filtroAtivo !== 'todos'), @endif
                                        <span class="font-bold">Busca: "{{ $busca }}"</span>
                                    @endif
                                    @if(!empty($statusFiltro))
                                        @if((isset($filtroAtivo) && $filtroAtivo !== 'todos') || !empty($busca)), @endif
                                        <span class="font-bold">Status: {{ ucfirst($statusFiltro) }}</span>
                                    @endif
                                </p>
                            </div>
                            <a href="{{ route('clientes.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium underline">
                                Limpar filtros
                            </a>
                        </div>
                    </div>
                @endif
                
                <form method="GET" action="{{ route('clientes.index') }}" class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                    @if(isset($filtroAtivo) && $filtroAtivo !== 'todos')
                        <input type="hidden" name="filtro" value="{{ $filtroAtivo }}">
                    @endif
                    
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                        <div>
                            <label class="text-sm text-gray-600">Buscar</label>
                            <div class="relative">
                                <input type="text" 
                                       name="busca" 
                                       value="{{ $busca ?? '' }}"
                                       class="input-field pl-10" 
                                       placeholder="Nome, email ou telefone">
                                <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <label class="text-sm text-gray-600">Status</label>
                            <select name="status" class="input-field">
                                <option value="">Todos</option>
                                <option value="ativo" {{ ($statusFiltro ?? '') === 'ativo' ? 'selected' : '' }}>Ativo</option>
                                <option value="onboarding" {{ ($statusFiltro ?? '') === 'onboarding' ? 'selected' : '' }}>Onboarding</option>
                                <option value="risco" {{ ($statusFiltro ?? '') === 'risco' ? 'selected' : '' }}>Risco</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('clientes.index') }}" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900">Limpar</a>
                        <button type="submit" class="btn-primary">Filtrar</button>
                    </div>
                </form>

                <!-- Desktop table -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Segmento</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Telefone</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Desde</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">MRR</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @foreach($clientes as $cliente)
                                @php
                                    $statusStyles = [
                                        'ativo' => 'bg-emerald-100 text-emerald-700',
                                        'onboarding' => 'bg-amber-100 text-amber-700',
                                        'risco' => 'bg-rose-100 text-rose-700',
                                    ];
                                @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-4">
                                        <p class="font-semibold text-gray-900">{{ $cliente['name'] }}</p>
                                        <p class="text-sm text-gray-500">{{ $cliente['owner'] }}</p>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-600">{{ $cliente['segment'] }}</td>
                                    <td class="px-4 py-4 text-sm text-gray-600">{{ $cliente['telefone'] ?? '-' }}</td>
                                    <td class="px-4 py-4">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $statusStyles[$cliente['status']] ?? 'bg-gray-100 text-gray-600' }}">
                                            {{ ucfirst($cliente['status']) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-600">{{ $cliente['since'] }}</td>
                                    <td class="px-4 py-4 text-sm font-semibold text-gray-900">{{ $cliente['mrr'] }}</td>
                                    <td class="px-4 py-4 text-right">
                                        <a href="{{ route('clientes.show', $cliente['id']) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Detalhes</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($clientes->isEmpty() && isset($filtroAtivo) && $filtroAtivo !== 'todos')
                    <div class="text-center py-12">
                        <p class="text-gray-500 text-lg">Nenhum cliente encontrado com o filtro selecionado.</p>
                        <a href="{{ route('clientes.index') }}" class="text-blue-600 hover:text-blue-800 mt-2 inline-block">
                            Limpar filtro
                        </a>
                    </div>
                @endif

                <!-- Mobile cards -->
                <div class="grid grid-cols-1 gap-4 md:hidden">
                    @foreach($clientes as $cliente)
                        @php
                            $statusStyles = [
                                'ativo' => 'bg-emerald-100 text-emerald-700',
                                'onboarding' => 'bg-amber-100 text-amber-700',
                                'risco' => 'bg-rose-100 text-rose-700',
                            ];
                        @endphp
                        <div class="border border-gray-200 rounded-2xl p-4 bg-white shadow-sm">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-base font-semibold text-gray-900">{{ $cliente['name'] }}</p>
                                    <p class="text-sm text-gray-500">{{ $cliente['owner'] }}</p>
                                </div>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $statusStyles[$cliente['status']] ?? 'bg-gray-100 text-gray-600' }}">
                                    {{ ucfirst($cliente['status']) }}
                                </span>
                            </div>
                            <div class="mt-3 text-sm text-gray-600 space-y-1">
                                <p><strong class="text-gray-700">Segmento:</strong> {{ $cliente['segment'] }}</p>
                                <p><strong class="text-gray-700">Telefone:</strong> {{ $cliente['telefone'] ?? '-' }}</p>
                                <p><strong class="text-gray-700">Desde:</strong> {{ $cliente['since'] }}</p>
                                <p><strong class="text-gray-700">MRR:</strong> {{ $cliente['mrr'] }}</p>
                            </div>
                            <div class="mt-4 flex justify-end">
                                <a href="{{ route('clientes.show', $cliente['id']) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Ver detalhes</a>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($clientes->isEmpty() && isset($filtroAtivo) && $filtroAtivo !== 'todos')
                    <div class="text-center py-12 md:hidden">
                        <p class="text-gray-500 text-lg">Nenhum cliente encontrado com o filtro selecionado.</p>
                        <a href="{{ route('clientes.index') }}" class="text-blue-600 hover:text-blue-800 mt-2 inline-block">
                            Limpar filtro
                        </a>
                    </div>
                @endif
            </section>
        </div>
    </main>
</div>
@endsection

