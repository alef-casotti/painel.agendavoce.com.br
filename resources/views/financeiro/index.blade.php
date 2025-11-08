@extends('layouts.app')

@section('content')
<div class="flex min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <x-sidebar />
    <x-header />

    <main class="flex-1 ml-64 mt-16 overflow-y-auto">
        <div class="p-8 space-y-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Área Financeira</h1>
                    <p class="text-gray-600">Visão centralizada das finanças do painel.</p>
                </div>
                <div class="flex flex-col md:flex-row md:items-center gap-3">
                    <a href="{{ route('financeiro.pagamentos.create') }}" class="btn-primary inline-flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Novo pagamento
                    </a>
                    <a href="{{ route('financeiro.pagamentos.index') }}" class="inline-flex items-center justify-center px-4 py-2 border border-gray-200 rounded-lg text-gray-600 hover:bg-gray-50 transition">
                        Gerenciar pagamentos
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="card p-5 bg-white">
                    <p class="text-sm text-gray-500 mb-1">Total previsto (este mês)</p>
                    <span class="text-2xl font-semibold text-gray-900">R$ {{ number_format($resumo['total_previsto_mes'], 2, ',', '.') }}</span>
                </div>
                <div class="card p-5 bg-white">
                    <p class="text-sm text-gray-500 mb-1">Total pago (este mês)</p>
                    <span class="text-2xl font-semibold text-emerald-600">R$ {{ number_format($resumo['total_pago_mes'], 2, ',', '.') }}</span>
                </div>
                <div class="card p-5 bg-white">
                    <p class="text-sm text-gray-500 mb-1">Pagamentos pendentes/atrasados</p>
                    <span class="text-2xl font-semibold text-amber-600">{{ $resumo['pendentes'] }}</span>
                </div>
                <div class="card p-5 bg-white">
                    <p class="text-sm text-gray-500 mb-1">Lançamentos no mês</p>
                    <span class="text-2xl font-semibold text-gray-900">{{ $resumo['pagamentos_mes'] }}</span>
                </div>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                <div class="card p-6 xl:col-span-2">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900">Últimos pagamentos lançados</h2>
                            <p class="text-sm text-gray-500">Acompanhe as movimentações recém-registradas.</p>
                        </div>
                        <a href="{{ route('financeiro.pagamentos.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-700 transition">
                            Ver todos
                        </a>
                    </div>

                    @if($ultimosPagamentos->isEmpty())
                        <div class="p-6 bg-gray-50 border border-dashed border-gray-200 rounded-lg text-center">
                            <p class="text-gray-500">Ainda não há pagamentos cadastrados. Comece registrando suas despesas.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Título</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Categoria</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Valor</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Vencimento</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($ultimosPagamentos as $pagamento)
                                        <tr class="hover:bg-gray-50 transition">
                                            <td class="px-4 py-3">
                                                <div class="font-medium text-gray-900">{{ $pagamento->titulo }}</div>
                                                <div class="text-xs text-gray-500">{{ $pagamento->created_at?->format('d/m/Y H:i') }}</div>
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-600">{{ $pagamento->categoria?->nome ?? '-' }}</td>
                                            <td class="px-4 py-3 text-sm font-semibold text-gray-900">R$ {{ number_format($pagamento->valor_previsto, 2, ',', '.') }}</td>
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
                                                    {{ \App\Models\Pagamento::statuses()[$pagamento->status] ?? ucfirst($pagamento->status) }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-600">{{ $pagamento->data_vencimento?->format('d/m/Y') ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                <div class="card p-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-semibold text-gray-900">Próximos vencimentos</h2>
                        <a href="{{ route('financeiro.pagamentos.index', ['status' => 'pendente']) }}" class="text-sm font-medium text-blue-600 hover:text-blue-700 transition">
                            Ver pendentes
                        </a>
                    </div>

                    @if($proximosPagamentos->isEmpty())
                        <div class="p-5 bg-gray-50 border border-dashed border-gray-200 rounded-lg text-center">
                            <p class="text-sm text-gray-500">Nenhum pagamento pendente ou atrasado cadastrado.</p>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($proximosPagamentos as $pagamento)
                                <div class="border border-gray-100 rounded-lg p-4 hover:border-blue-100 bg-white transition">
                                    <div class="flex items-start justify-between">
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900">{{ $pagamento->titulo }}</p>
                                            <p class="text-xs text-gray-500">{{ $pagamento->categoria?->nome ?? 'Sem categoria' }}</p>
                                        </div>
                                        <span class="text-sm font-semibold text-gray-900">R$ {{ number_format($pagamento->valor_previsto, 2, ',', '.') }}</span>
                                    </div>
                                    <div class="flex items-center justify-between mt-3 text-xs text-gray-500">
                                        <span>Vencimento: <strong class="text-gray-700">{{ $pagamento->data_vencimento?->format('d/m/Y') ?? '-' }}</strong></span>
                                        <a href="{{ route('financeiro.pagamentos.edit', $pagamento) }}" class="text-blue-600 hover:text-blue-700 font-medium">Atualizar</a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </main>
</div>
@endsection
