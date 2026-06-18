@extends('layouts.app')

@section('title', 'Recebimentos - Agenda Você')

@section('content')
<div class="flex min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <x-sidebar />
    <x-header />

    <main class="flex-1 lg:ml-3 mt-16 overflow-y-auto">
        <div class="p-4 lg:p-8 space-y-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 text-sm text-gray-500 mb-2">
                        <a href="{{ route('financeiro.index') }}" class="hover:text-blue-600 transition">Financeiro</a>
                        <span>/</span>
                        <span class="text-gray-700">Recebimentos</span>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-900">Recebimentos</h1>
                    <p class="text-gray-600">Assinaturas ativas dos clientes — dados sincronizados via API.</p>
                </div>
                <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-3 w-full md:w-auto">
                    <a href="{{ route('financeiro.index') }}" class="btn-outline inline-flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Voltar ao financeiro
                    </a>
                    <a href="{{ route('financeiro.pagamentos.index') }}" class="btn-secondary inline-flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        Ver pagamentos
                    </a>
                </div>
            </div>

            @if($error)
                <div class="p-4 bg-yellow-50 border border-yellow-200 text-yellow-800 rounded-lg">
                    {{ $error }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="card p-5 bg-white border-l-4 border-emerald-500">
                    <p class="text-sm text-gray-500 mb-1">Receita recorrente (MRR)</p>
                    <span class="text-2xl font-semibold text-emerald-600">R$ {{ number_format($resumo['total_mrr'], 2, ',', '.') }}</span>
                </div>
                <div class="card p-5 bg-white">
                    <p class="text-sm text-gray-500 mb-1">Assinaturas ativas</p>
                    <span class="text-2xl font-semibold text-gray-900">{{ number_format($resumo['quantidade'], 0, '', '.') }}</span>
                </div>
            </div>

            <p class="text-xs text-gray-500">
                Valores baseados nas assinaturas ativas no Stripe. O MRR é a soma mensal dos planos, não necessariamente o caixa recebido no mês.
            </p>

            @if($recebimentos->isEmpty())
                <div class="card p-10 text-center">
                    <svg class="mx-auto w-12 h-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-900 mb-1">Nenhuma assinatura ativa</h3>
                    <p class="text-gray-500">Quando clientes tiverem assinaturas ativas, elas aparecerão aqui.</p>
                </div>
            @else
                <div class="card p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Cliente</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Plano</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Valor/mês</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Cliente desde</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($recebimentos as $recebimento)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-4 py-3">
                                            <div class="font-medium text-gray-900">{{ $recebimento['nome'] }}</div>
                                            <div class="text-xs text-gray-500">{{ $recebimento['email'] }}</div>
                                        </td>
                                        <td class="px-4 py-3 text-sm font-medium text-gray-700">{{ $recebimento['plano'] }}</td>
                                        <td class="px-4 py-3 text-sm font-semibold text-emerald-600">R$ {{ number_format($recebimento['valor'], 2, ',', '.') }}</td>
                                        <td class="px-4 py-3 text-sm">
                                            <span class="px-3 py-1.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                Ativa
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-600">{{ $recebimento['started_at_formatada'] ?? '-' }}</td>
                                        <td class="px-4 py-3 text-right">
                                            <a href="{{ route('clientes.show', $recebimento['id']) }}" class="text-sm font-medium text-blue-600 hover:text-blue-700 transition">
                                                Ver cliente
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50">
                                <tr>
                                    <td colspan="2" class="px-4 py-3 text-sm font-semibold text-gray-700">Total</td>
                                    <td class="px-4 py-3 text-sm font-bold text-emerald-600">R$ {{ number_format($resumo['total_mrr'], 2, ',', '.') }}</td>
                                    <td colspan="3"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </main>
</div>
@endsection
