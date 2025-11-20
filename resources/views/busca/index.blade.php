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
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Resultados da Busca</h1>
                <p class="text-gray-600">Busca por: <strong>"{{ $query }}"</strong></p>
                @if($totalResults > 0)
                    <p class="text-sm text-gray-500 mt-1">{{ $totalResults }} resultado(s) encontrado(s)</p>
                @endif
            </div>

            <!-- Messages -->
            @if(session('info'))
                <div class="mb-6 p-4 bg-blue-50 border border-blue-200 text-blue-700 rounded-lg">
                    {{ session('info') }}
                </div>
            @endif

            @if($totalResults == 0)
                <div class="card p-8 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <p class="text-gray-500 text-lg mb-2">Nenhum resultado encontrado</p>
                    <p class="text-gray-400 text-sm">Tente buscar por termos diferentes ou verifique a ortografia</p>
                </div>
            @else
                <!-- Tickets Results -->
                @if($results['tickets']->count() > 0 && in_array(auth()->user()->role, ['admin', 'suporte', 'customer_success_manager']))
                    <div class="card mb-6 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                            <h2 class="text-xl font-semibold text-gray-900">
                                Tickets 
                                <span class="text-sm font-normal text-gray-500">({{ $results['tickets']->count() }})</span>
                            </h2>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prioridade</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email Cliente</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assunto</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Última Atualização</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($results['tickets'] as $ticket)
                                        <tr class="hover:bg-gray-50 {{ $ticket->prioridade === 'alta' ? 'bg-yellow-50' : '' }}">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#{{ $ticket->id }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($ticket->prioridade === 'alta')
                                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                        🔴 Alta Prioridade
                                                    </span>
                                                @else
                                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-600">
                                                        Normal
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $ticket->email }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-900 max-w-xs truncate">{{ $ticket->assunto }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @php
                                                    $statusColors = [
                                                        'aberto' => 'bg-green-100 text-green-800',
                                                        'em_andamento' => 'bg-blue-100 text-blue-800',
                                                        'aguardando_cliente' => 'bg-yellow-100 text-yellow-800',
                                                        'fechado' => 'bg-gray-100 text-gray-800',
                                                    ];
                                                    $statusLabels = [
                                                        'aberto' => 'Aberto',
                                                        'em_andamento' => 'Em Andamento',
                                                        'aguardando_cliente' => 'Aguardando Cliente',
                                                        'fechado' => 'Fechado',
                                                    ];
                                                    $color = $statusColors[$ticket->status] ?? 'bg-gray-100 text-gray-800';
                                                    $label = $statusLabels[$ticket->status] ?? $ticket->status;
                                                @endphp
                                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $color }}">
                                                    {{ $label }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $ticket->updated_at->format('d/m/Y H:i') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <a href="{{ route('suporte.visualizar', $ticket->id) }}" class="text-blue-600 hover:text-blue-900">
                                                    Ver Detalhes
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                <!-- Users Results (apenas para admin) -->
                @if($results['users']->count() > 0 && auth()->user()->role === 'admin')
                    <div class="card overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                            <h2 class="text-xl font-semibold text-gray-900">
                                Usuários 
                                <span class="text-sm font-normal text-gray-500">({{ $results['users']->count() }})</span>
                            </h2>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Perfil</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Criado em</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($results['users'] as $user)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $user->name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $user->email }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                    {{ ucfirst($user->role) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $user->created_at->format('d/m/Y') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </main>
</div>
@endsection

