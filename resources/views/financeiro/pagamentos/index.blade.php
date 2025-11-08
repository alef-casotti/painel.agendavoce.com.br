@extends('layouts.app')

@section('title', 'Pagamentos - Agenda Você')

@section('content')
<div class="flex min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <x-sidebar />
    <x-header />

    <main class="flex-1 ml-64 mt-16 overflow-y-auto">
        <div class="p-8 space-y-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Pagamentos</h1>
                    <p class="text-gray-600">Controle detalhado de todas as saídas financeiras</p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('financeiro.pagamentos.create') }}" class="btn-primary inline-flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Novo pagamento
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="p-4 bg-green-50 border border-green-100 text-green-700 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if($pagamentos->isEmpty())
                <div class="card p-10 text-center">
                    <svg class="mx-auto w-12 h-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-6m3 6v-4M4 4h16M4 8h16M4 12h7"></path>
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-900 mb-1">Nenhum pagamento registrado ainda</h3>
                    <p class="text-gray-500 mb-4">Cadastre seus primeiros pagamentos para acompanhar seus custos e fluxos de caixa.</p>
                    <a href="{{ route('financeiro.pagamentos.create') }}" class="btn-primary inline-flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Criar pagamento
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="card p-5 bg-white">
                        <p class="text-sm text-gray-500 mb-1">Total previsto</p>
                        <span class="text-2xl font-semibold text-gray-900">R$ {{ number_format($resumo['total_previsto'], 2, ',', '.') }}</span>
                    </div>
                    <div class="card p-5 bg-white">
                        <p class="text-sm text-gray-500 mb-1">Total pago</p>
                        <span class="text-2xl font-semibold text-emerald-600">R$ {{ number_format($resumo['total_pago'], 2, ',', '.') }}</span>
                    </div>
                    <div class="card p-5 bg-white">
                        <p class="text-sm text-gray-500 mb-1">Pagamentos pendentes</p>
                        <span class="text-2xl font-semibold text-amber-600">{{ $resumo['pendentes'] }}</span>
                    </div>
                    <div class="card p-5 bg-white">
                        <p class="text-sm text-gray-500 mb-1">Pagamentos atrasados</p>
                        <span class="text-2xl font-semibold text-red-600">{{ $resumo['atrasados'] }}</span>
                    </div>
                </div>

                <div class="card p-6 space-y-6">
                    <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Busca</label>
                            <input type="text" name="busca" value="{{ $filtros['busca'] ?? '' }}" class="input-field" placeholder="Título, fornecedor, referência">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Status</label>
                            <select name="status" class="input-field">
                                <option value="">Todos</option>
                                @foreach($statuses as $value => $label)
                                    <option value="{{ $value }}" @selected(($filtros['status'] ?? null) === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Categoria</label>
                            <select name="categoria_id" class="input-field">
                                <option value="">Todas</option>
                                @foreach($categorias as $categoria)
                                    <option value="{{ $categoria->id }}" @selected(($filtros['categoria_id'] ?? null) == $categoria->id)>{{ $categoria->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Centro de custo</label>
                            <select name="centro_custo_id" class="input-field">
                                <option value="">Todos</option>
                                @foreach($centrosCusto as $centro)
                                    <option value="{{ $centro->id }}" @selected(($filtros['centro_custo_id'] ?? null) == $centro->id)>{{ $centro->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Competência de</label>
                            <input type="date" name="periodo_de" value="{{ $filtros['periodo_de'] ?? '' }}" class="input-field">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Competência até</label>
                            <input type="date" name="periodo_ate" value="{{ $filtros['periodo_ate'] ?? '' }}" class="input-field">
                        </div>
                        <div class="flex items-end gap-3">
                            <button type="submit" class="btn-primary w-full text-center">Filtrar</button>
                        </div>
                        <div class="flex items-end">
                            <a href="{{ route('financeiro.pagamentos.index') }}" class="w-full text-center px-4 py-2.5 border border-gray-200 rounded-lg text-gray-600 hover:bg-gray-50 transition">
                                Limpar filtros
                            </a>
                        </div>
                    </form>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Título</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoria</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Centro de custo</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fornecedor</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Método</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Valor previsto</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Valor pago</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vencimento</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($pagamentos as $pagamento)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-4 py-3">
                                            <div class="font-medium text-gray-900">{{ $pagamento->titulo }}</div>
                                            @if($pagamento->documento_referencia)
                                                <div class="text-xs text-gray-500">Ref: {{ $pagamento->documento_referencia }}</div>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-600">{{ $pagamento->categoria?->nome ?? '-' }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600">{{ $pagamento->centroCusto?->nome ?? '-' }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600">{{ $pagamento->fornecedor ?? '-' }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600">{{ $pagamento->metodo_pagamento_label ?? '-' }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-900 font-semibold text-right">R$ {{ number_format($pagamento->valor_previsto, 2, ',', '.') }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600 text-right">
                                            @if($pagamento->valor_pago)
                                                <span class="text-emerald-600 font-semibold">R$ {{ number_format($pagamento->valor_pago, 2, ',', '.') }}</span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-600">
                                            {{ $pagamento->data_vencimento?->format('d/m/Y') ?? '-' }}
                                        </td>
                                        <td class="px-4 py-3 text-sm">
                                            @php
                                                $statusClasses = [
                                                    'pendente' => 'bg-amber-50 text-amber-700 border border-amber-200',
                                                    'pago' => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
                                                    'atrasado' => 'bg-red-50 text-red-700 border border-red-200',
                                                    'cancelado' => 'bg-gray-100 text-gray-600 border border-gray-200',
                                                ];
                                            @endphp
                                            <span class="px-3 py-1.5 rounded-full text-xs font-semibold {{ $statusClasses[$pagamento->status] ?? 'bg-gray-100 text-gray-600 border border-gray-200' }}">
                                                {{ $statuses[$pagamento->status] ?? ucfirst($pagamento->status) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-right space-x-2">
                                            <a href="{{ route('financeiro.pagamentos.show', $pagamento) }}" class="inline-flex items-center px-3 py-1 border border-gray-200 rounded-lg text-gray-600 hover:bg-gray-50 transition">
                                                Ver
                                            </a>
                                            <a href="{{ route('financeiro.pagamentos.edit', $pagamento) }}" class="inline-flex items-center px-3 py-1 border border-blue-200 text-blue-600 rounded-lg hover:bg-blue-50 transition">
                                                Editar
                                            </a>
                                            <form action="{{ route('financeiro.pagamentos.destroy', $pagamento) }}" method="POST" class="inline-flex" onsubmit="return confirm('Tem certeza que deseja remover este pagamento?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center px-3 py-1 border border-red-200 text-red-600 rounded-lg hover:bg-red-50 transition">
                                                    Excluir
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div>
                        {{ $pagamentos->links() }}
                    </div>
                </div>
            @endif
        </div>
    </main>
</div>
@endsection

