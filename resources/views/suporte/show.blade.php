@extends('layouts.app')

@section('content')
<div class="flex min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">      
    <x-sidebar />
    <x-header />

    <!-- Main Content -->
    <main class="flex-1 lg:ml-3 mt-16 overflow-y-auto">
        <div class="p-4 lg:p-8">
            
            <!-- Breadcrumb -->
            <nav class="mb-6">
                <ol class="flex items-center space-x-2 text-sm text-gray-500">
                    <li><a href="{{ route('suporte.index') }}" class="hover:text-blue-600 transition-colors">Tickets</a></li>
                    <li><span class="mx-2">/</span></li>
                    <li class="text-gray-900 font-medium">Ticket #{{ $ticket->id }}</li>
                </ol>
            </nav>

            <!-- Alert Messages -->
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-400 rounded-r-lg shadow-sm">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-green-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <p class="text-green-700 font-medium">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-400 rounded-r-lg shadow-sm">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-red-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <p class="text-red-700 font-medium">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            <!-- Ticket Header Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-4 sm:px-6 py-4 sm:py-5">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                        <div class="flex items-center space-x-3 sm:space-x-4 flex-1 min-w-0">
                            <div class="bg-white/20 backdrop-blur-sm rounded-lg p-2 sm:p-3 flex-shrink-0">
                                <svg class="w-6 h-6 sm:w-8 sm:h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center space-x-2 sm:space-x-3 flex-wrap">
                                    <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-white">
                                        <span class="text-blue-200 font-normal">Ticket</span> 
                                        <span class="text-white">#{{ str_pad($ticket->id, 6, '0', STR_PAD_LEFT) }}</span>
                                    </h1>
                                </div>
                                <p class="text-blue-100 text-xs sm:text-sm mt-1 truncate">{{ $ticket->assunto }}</p>
                            </div>
                        </div>
                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 sm:gap-3 flex-shrink-0">
                            @php
                                $statusColors = [
                                    'aberto' => 'bg-green-500',
                                    'em_andamento' => 'bg-blue-500',
                                    'aguardando_cliente' => 'bg-yellow-500',
                                    'fechado' => 'bg-gray-500',
                                ];
                                $statusLabels = [
                                    'aberto' => 'Aberto',
                                    'em_andamento' => 'Em Andamento',
                                    'aguardando_cliente' => 'Aguardando Cliente',
                                    'fechado' => 'Fechado',
                                ];
                                $color = $statusColors[$ticket->status] ?? 'bg-gray-500';
                                $label = $statusLabels[$ticket->status] ?? $ticket->status;
                            @endphp
                            <span class="px-3 sm:px-4 py-2 bg-white/20 backdrop-blur-sm text-white text-xs sm:text-sm font-semibold rounded-lg border border-white/30 text-center whitespace-nowrap">
                                {{ $label }}
                            </span>
                            @if($ticket->status !== 'fechado')
                                <form method="POST" action="{{ route('suporte.fechar', $ticket->id) }}" class="inline w-full sm:w-auto" id="fecharTicketForm" onsubmit="return confirmarFechamento(event);">
                                    @csrf
                                    <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-white text-blue-700 rounded-lg hover:bg-blue-50 text-sm font-semibold transition-colors shadow-sm whitespace-nowrap">
                                        Fechar Ticket
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div class="flex items-start space-x-3">
                            <div class="bg-blue-100 rounded-lg p-2">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium mb-1">Cliente</p>
                                <p class="text-sm font-semibold text-gray-900">{{ $ticket->email }}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-3">
                            <div class="bg-purple-100 rounded-lg p-2">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium mb-1">Criado em</p>
                                <p class="text-sm font-semibold text-gray-900">{{ $ticket->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-3">
                            <div class="bg-green-100 rounded-lg p-2">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium mb-1">Atualizado em</p>
                                <p class="text-sm font-semibold text-gray-900">{{ $ticket->updated_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                        
                        @if($ticket->prioridade === 'alta')
                        <div class="flex items-start space-x-3">
                            <div class="bg-red-100 rounded-lg p-2">
                                <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium mb-1">Prioridade</p>
                                <p class="text-sm font-semibold text-red-600">Alta Prioridade</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Messages Container -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900">Conversa</h2>
                        <span class="text-sm text-gray-500">{{ $ticket->messages->count() }} mensagem(ns)</span>
                    </div>
                </div>
                
                <div class="p-6 space-y-6 max-h-[600px] overflow-y-auto" id="messagesContainer">
                    @forelse($ticket->messages as $message)
                        @php
                            $naoRespondida = $message->sender_type === 'cliente' && is_null($message->answered_at);
                        @endphp
                        
                        <div class="flex {{ $message->sender_type === 'cliente' ? 'justify-start' : 'justify-end' }}">
                            <div class="flex items-start space-x-3 max-w-2xl {{ $message->sender_type === 'cliente' ? '' : 'flex-row-reverse space-x-reverse' }}">
                                <!-- Avatar -->
                                <div class="flex-shrink-0">
                                    @if($message->sender_type === 'cliente')
                                        <div class="w-10 h-10 rounded-full {{ $naoRespondida ? 'bg-red-100 ring-2 ring-red-400' : 'bg-gray-200' }} flex items-center justify-center">
                                            @if($naoRespondida)
                                                <svg class="w-6 h-6 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                                </svg>
                                            @else
                                                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                </svg>
                                            @endif
                                        </div>
                                    @else
                                        <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center">
                                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                
                                <!-- Message Content -->
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2 mb-1 {{ $message->sender_type === 'cliente' ? '' : 'justify-end' }}">
                                        <span class="text-sm font-semibold {{ $message->sender_type === 'cliente' ? 'text-gray-900' : 'text-blue-600' }}">
                                            {{ $message->sender_type === 'cliente' ? 'Cliente' : 'Suporte' }}
                                        </span>
                                        @if($naoRespondida)
                                            <span class="px-2 py-0.5 bg-red-500 text-white text-xs font-bold rounded-full animate-pulse">
                                                ⚠️ PRECISA RESPOSTA
                                            </span>
                                        @endif
                                        <span class="text-xs text-gray-500">{{ $message->sent_at->format('d/m H:i') }}</span>
                                    </div>
                                    
                                    <div class="rounded-2xl px-4 py-3 {{ $naoRespondida ? 'bg-red-50 border-2 border-red-300 shadow-md' : ($message->sender_type === 'cliente' ? 'bg-gray-100' : 'bg-blue-50') }}">
                                        <p class="text-sm {{ $naoRespondida ? 'text-red-900 font-medium' : 'text-gray-800' }} whitespace-pre-wrap leading-relaxed">{{ $message->message }}</p>
                                    </div>
                                    
                                    <div class="mt-2 flex items-center space-x-4 text-xs {{ $naoRespondida ? 'text-red-600' : 'text-gray-500' }}">
                                        @if($message->answered_at)
                                            <span class="flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                                Respondido em {{ $message->answered_at->format('d/m H:i') }}
                                            </span>
                                        @elseif($message->sender_type === 'cliente')
                                            <span class="flex items-center font-semibold">
                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                                </svg>
                                                Aguardando resposta
                                            </span>
                                        @endif
                                        
                                        @if($message->sender_type === 'suporte')
                                            @if($message->viewed_at)
                                                <span class="flex items-center text-green-600">
                                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 2.5 10 2.5c4.478 0 8.268 3.443 9.542 7.5-1.274 4.057-5.064 7.5-9.542 7.5-4.478 0-8.268-3.443-9.542-7.5zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                                    </svg>
                                                    Visualizado
                                                </span>
                                            @else
                                                <span class="flex items-center text-gray-400">
                                                    Não visualizado
                                                </span>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                            <p class="text-gray-500">Nenhuma mensagem ainda.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Reply Form -->
            @if($ticket->status !== 'fechado')
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h2 class="text-lg font-semibold text-gray-900">Responder Ticket</h2>
                    </div>
                    <form method="POST" action="{{ route('suporte.responder', $ticket->id) }}" class="p-6">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Sua resposta</label>
                            <textarea 
                                name="mensagem" 
                                rows="6" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors resize-none" 
                                placeholder="Digite sua resposta aqui..."
                                required
                                minlength="10"
                            ></textarea>
                            @error('mensagem')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="flex items-center justify-end space-x-3">
                            <a href="{{ route('suporte.index') }}" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors font-medium">
                                Cancelar
                            </a>
                            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-semibold shadow-sm flex items-center space-x-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                </svg>
                                <span>Enviar Resposta</span>
                            </button>
                        </div>
                    </form>
                </div>
            @else
                <div class="bg-gray-50 rounded-xl border border-gray-200 p-6">
                    <div class="flex items-center space-x-3">
                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        <p class="text-gray-600">Este ticket está fechado. Tickets fechados não podem ser reabertos. Se necessário, o cliente pode abrir um novo ticket.</p>
                    </div>
                </div>
            @endif
        </div>
    </main>
</div>

@push('scripts')
<script>
    function confirmarFechamento(event) {
        event.preventDefault();
        
        const mensagem = '⚠️ ATENÇÃO: Você está prestes a fechar este ticket.\n\n' +
                        'Ao fechar:\n' +
                        '• Todas as mensagens serão marcadas como visualizadas\n' +
                        '• Uma mensagem será enviada automaticamente ao cliente\n' +
                        '• O ticket NÃO poderá ser reaberto\n\n' +
                        'Tem certeza que deseja continuar?';
        
        if (confirm(mensagem)) {
            document.getElementById('fecharTicketForm').submit();
            return true;
        }
        
        return false;
    }
    
    // Função para scroll até o final
    function scrollToBottom() {
        const messagesContainer = document.getElementById('messagesContainer');
        if (messagesContainer) {
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }
    }
    
    // Scroll automático ao carregar a página
    document.addEventListener('DOMContentLoaded', function() {
        scrollToBottom();
        // Pequeno delay para garantir que o conteúdo foi renderizado
        setTimeout(scrollToBottom, 100);
    });
    
    // Scroll automático quando a página é exibida (útil após redirect)
    window.addEventListener('load', function() {
        scrollToBottom();
        setTimeout(scrollToBottom, 100);
    });
    
    // Observa mudanças no container de mensagens
    const messagesContainer = document.getElementById('messagesContainer');
    if (messagesContainer) {
        const observer = new MutationObserver(function() {
            scrollToBottom();
        });
        
        observer.observe(messagesContainer, {
            childList: true,
            subtree: true
        });
    }
</script>
@endpush
@endsection
