@extends('layouts.app')

@section('title', 'Detalhes do Pagamento - Agenda Você')

@section('content')
<div class="flex min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <x-sidebar />
    <x-header />

    <main class="flex-1 ml-64 mt-16 overflow-y-auto">
        <div class="p-8 space-y-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        @php
                            $statusClasses = [
                                'pendente' => 'bg-amber-50 text-amber-700 border border-amber-200',
                                'pago' => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
                                'atrasado' => 'bg-red-50 text-red-700 border border-red-200',
                                'cancelado' => 'bg-gray-100 text-gray-600 border border-gray-200',
                            ];
                        @endphp
                        <span class="px-3 py-1.5 rounded-full text-xs font-semibold {{ $statusClasses[$pagamento->status] ?? 'bg-gray-100 text-gray-600 border border-gray-200' }}">
                            {{ \App\Models\Pagamento::statuses()[$pagamento->status] ?? ucfirst($pagamento->status) }}
                        </span>
                        @if($pagamento->recorrente)
                            <span class="px-3 py-1.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                Recorrente
                            </span>
                        @endif
                    </div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $pagamento->titulo }}</h1>
                    @if($pagamento->documento_referencia)
                        <p class="text-gray-600">Referência: {{ $pagamento->documento_referencia }}</p>
                    @endif
                </div>

                <div class="flex flex-col md:flex-row md:items-center md:justify-end gap-3">
                    <a href="{{ route('financeiro.pagamentos.edit', $pagamento) }}" class="inline-flex items-center px-4 py-2 border border-blue-200 text-blue-600 rounded-lg hover:bg-blue-50 transition">
                        Editar
                    </a>
                    <a href="{{ route('financeiro.pagamentos.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-200 rounded-lg text-gray-600 hover:bg-gray-50 transition">
                        Voltar para lista
                    </a>
                    <form action="{{ route('financeiro.pagamentos.destroy', $pagamento) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja remover este pagamento?')" class="inline-flex">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-red-200 text-red-600 rounded-lg hover:bg-red-50 transition">
                            Excluir
                        </button>
                    </form>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
                <div class="card p-5 bg-white">
                    <p class="text-sm text-gray-500 mb-1">Valor previsto</p>
                    <span class="text-2xl font-semibold text-gray-900">R$ {{ number_format($pagamento->valor_previsto, 2, ',', '.') }}</span>
                </div>
                <div class="card p-5 bg-white">
                    <p class="text-sm text-gray-500 mb-1">Valor pago</p>
                    <span class="text-2xl font-semibold {{ $pagamento->valor_pago ? 'text-emerald-600' : 'text-gray-400' }}">
                        {{ $pagamento->valor_pago ? 'R$ ' . number_format($pagamento->valor_pago, 2, ',', '.') : 'Não informado' }}
                    </span>
                </div>
                <div class="card p-5 bg-white">
                    <p class="text-sm text-gray-500 mb-1">Data de vencimento</p>
                    <span class="text-lg font-medium text-gray-800">{{ $pagamento->data_vencimento?->format('d/m/Y') ?? 'Não definida' }}</span>
                </div>
                <div class="card p-5 bg-white">
                    <p class="text-sm text-gray-500 mb-1">Data de pagamento</p>
                    <span class="text-lg font-medium text-gray-800">{{ $pagamento->data_pagamento?->format('d/m/Y') ?? 'Não informada' }}</span>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-6">
                    <div class="card p-6 space-y-4">
                        <h2 class="text-lg font-semibold text-gray-900">Informações gerais</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-500">Categoria</p>
                                <p class="text-base font-medium text-gray-800">{{ $pagamento->categoria?->nome ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Centro de custo</p>
                                <p class="text-base font-medium text-gray-800">{{ $pagamento->centroCusto?->nome ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Fornecedor</p>
                                <p class="text-base font-medium text-gray-800">{{ $pagamento->fornecedor ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Método de pagamento</p>
                                <p class="text-base font-medium text-gray-800">{{ $pagamento->metodo_pagamento_label ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Data competência</p>
                                <p class="text-base font-medium text-gray-800">{{ $pagamento->data_competencia?->format('d/m/Y') ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Parcelamento</p>
                                <p class="text-base font-medium text-gray-800">
                                    @if($pagamento->parcelas_total)
                                        {{ $pagamento->parcela_atual ?? 1 }} de {{ $pagamento->parcelas_total }}
                                    @else
                                        Sem parcelamento
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="card p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-2">Descrição</h2>
                        <p class="text-gray-700 leading-relaxed">
                            {{ $pagamento->descricao ?? 'Nenhuma descrição adicional informada.' }}
                        </p>
                    </div>

                    <div class="card p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-2">Observações</h2>
                        <p class="text-gray-700 leading-relaxed whitespace-pre-line">
                            {{ $pagamento->observacoes ?? 'Sem observações.' }}
                        </p>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="card p-6 space-y-3">
                        <h2 class="text-lg font-semibold text-gray-900">Auditoria</h2>
                        <div>
                            <p class="text-sm text-gray-500">Criado em</p>
                            <p class="text-base font-medium text-gray-800">{{ $pagamento->created_at?->format('d/m/Y H:i') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Última atualização</p>
                            <p class="text-base font-medium text-gray-800">{{ $pagamento->updated_at?->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>

                    <div class="card p-6 space-y-4">
                        <h2 class="text-lg font-semibold text-gray-900">Ações rápidas</h2>
                        <a href="{{ route('financeiro.pagamentos.edit', $pagamento) }}" class="btn-primary inline-flex items-center justify-center w-full">
                            Editar pagamento
                        </a>
                        <form action="{{ route('financeiro.pagamentos.destroy', $pagamento) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja remover este pagamento?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full inline-flex justify-center px-4 py-2 border border-red-200 text-red-600 rounded-lg hover:bg-red-50 transition">
                                Excluir pagamento
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection

