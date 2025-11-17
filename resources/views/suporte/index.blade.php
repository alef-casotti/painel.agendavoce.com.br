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
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Área de Suporte</h1>
                <p class="text-gray-600">Gerencie tickets e atendimentos aos clientes</p>
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

            <!-- Filtros -->
            <div class="card p-6 mb-6">
                <form method="GET" action="{{ route('suporte.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select name="status" class="input-field">
                            <option value="">Todos</option>
                            <option value="aberto" {{ request('status') == 'aberto' ? 'selected' : '' }}>Aberto</option>
                            <option value="em_andamento" {{ request('status') == 'em_andamento' ? 'selected' : '' }}>Em Andamento</option>
                            <option value="aguardando_cliente" {{ request('status') == 'aguardando_cliente' ? 'selected' : '' }}>Aguardando Cliente</option>
                            <option value="fechado" {{ request('status') == 'fechado' ? 'selected' : '' }}>Fechado</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" name="email" value="{{ request('email') }}" placeholder="Buscar por email" class="input-field">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Assunto</label>
                        <input type="text" name="assunto" value="{{ request('assunto') }}" placeholder="Buscar por assunto" class="input-field">
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="btn-primary w-full">Filtrar</button>
                    </div>
                </form>
            </div>

            <!-- Tickets Table -->
            <div class="card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prioridade</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email Cliente</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assunto</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mensagens</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status Mensagens</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Última Atualização</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                            </tr>
                        </thead>
                        <tbody id="tickets-table-body" class="bg-white divide-y divide-gray-200">
                            @forelse($tickets as $ticket)
                                @php
                                    $temMensagensNaoRespondidas = $ticket->mensagens_nao_respondidas > 0;
                                @endphp
                                <tr class="hover:bg-gray-50 {{ $temMensagensNaoRespondidas ? 'bg-red-50 border-l-4 border-red-500' : ($ticket->prioridade === 'alta' ? 'bg-yellow-50' : '') }}">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm {{ $temMensagensNaoRespondidas ? 'font-bold text-red-900' : 'text-gray-900' }}">#{{ $ticket->id }}</td>
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
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $ticket->total_mensagens }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($temMensagensNaoRespondidas)
                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-500 text-white animate-pulse">
                                                ⚠️ {{ $ticket->mensagens_nao_respondidas }} não respondida(s)
                                            </span>
                                        @elseif($ticket->nao_visualizadas > 0)
                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800">
                                                {{ $ticket->nao_visualizadas }} não visualizada(s)
                                            </span>
                                        @else
                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-600">
                                                0
                                            </span>
                                        @endif
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
                            @empty
                                <tr>
                                    <td colspan="9" class="px-6 py-4 text-center text-sm text-gray-500">
                                        Nenhum ticket encontrado.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div id="tickets-pagination">
                    @if($tickets->hasPages())
                        <div class="px-6 py-4 border-t border-gray-200">
                            {{ $tickets->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </main>

    <!-- Audio para notificação -->
    <audio id="notificationSound" preload="auto" loop>
        <source src="{{ asset('sounds/notification.mp3') }}" type="audio/mpeg">
    </audio>
</div>

@push('scripts')
<script>
    let ultimasMensagensNaoRespondidas = parseInt('{{ $novasMensagens }}') || 0;
    let ultimoTimestamp = {{ $ultimoTimestamp ?? 'null' }};
    let audioTocando = false;
    let intervaloNotificacao;
    let intervaloAtualizacaoTabela;
    let usuarioParouManualmente = false;

    // Função para tocar notificação
    function tocarNotificacao() {
        const audio = document.getElementById('notificationSound');
        if (audio && !audioTocando && !usuarioParouManualmente) {
            audioTocando = true;
            audio.loop = true;
            audio.play().catch(e => {
                console.log('Erro ao reproduzir áudio:', e);
                audioTocando = false;
            });
        }
    }

    // Função para parar notificação
    function pararNotificacao() {
        const audio = document.getElementById('notificationSound');
        if (audio && audioTocando) {
            audio.pause();
            audio.currentTime = 0;
            audioTocando = false;
        }
    }

    // Função para verificar novas mensagens não respondidas (para notificação)
    function verificarNovidades() {
        fetch('{{ route("suporte.index") }}?ajax=1', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Erro na resposta');
            }
            return response.json();
        })
        .then(data => {
            const novasMensagens = parseInt(data.novasMensagens) || 0;
            const novoTimestamp = data.ultimaMensagemTimestamp || null;
            
            // Se houver mensagens não respondidas
            if (novasMensagens > 0) {
                let deveTocar = false;
                
                // Caso 1: Primeira vez que detecta mensagens (não havia antes)
                if (ultimasMensagensNaoRespondidas === 0) {
                    deveTocar = true;
                }
                // Caso 2: Aumentou o número de mensagens não respondidas
                else if (novasMensagens > ultimasMensagensNaoRespondidas) {
                    deveTocar = true;
                }
                // Caso 3: Mesmo número de mensagens, mas timestamp mudou (nova mensagem)
                else if (novasMensagens === ultimasMensagensNaoRespondidas && novoTimestamp && novoTimestamp !== ultimoTimestamp) {
                    deveTocar = true;
                }
                // Caso 4: Havia mensagens, mas não estava tocando (ex: página recém carregou)
                else if (novasMensagens > 0 && !audioTocando && !usuarioParouManualmente) {
                    deveTocar = true;
                }
                
                if (deveTocar) {
                    tocarNotificacao();
                    usuarioParouManualmente = false; // Reset flag quando nova mensagem chega
                }
            } else {
                // Para quando todas as mensagens foram respondidas/visualizadas
                if (audioTocando) {
                    pararNotificacao();
                }
                usuarioParouManualmente = false;
            }
            
            ultimasMensagensNaoRespondidas = novasMensagens;
            ultimoTimestamp = novoTimestamp;
        })
        .catch(error => {
            console.log('Erro ao verificar novidades:', error);
        });
    }

    // Função para atualizar a tabela via AJAX
    function atualizarTabela() {
        // Pega os filtros atuais da URL
        const urlParams = new URLSearchParams(window.location.search);
        const params = new URLSearchParams();
        
        // Mantém os filtros
        if (urlParams.get('status')) params.append('status', urlParams.get('status'));
        if (urlParams.get('email')) params.append('email', urlParams.get('email'));
        if (urlParams.get('assunto')) params.append('assunto', urlParams.get('assunto'));
        
        // Adiciona parâmetros para AJAX
        params.append('ajax', '1');
        params.append('updateTable', '1');
        
        // Se estiver em uma página de paginação, mantém
        if (urlParams.get('page')) params.append('page', urlParams.get('page'));

        fetch('{{ route("suporte.index") }}?' + params.toString(), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Erro na resposta');
            }
            return response.json();
        })
        .then(data => {
            // Atualiza o corpo da tabela
            const tbody = document.getElementById('tickets-table-body');
            if (tbody && data.ticketsHtml) {
                tbody.innerHTML = data.ticketsHtml;
            }

            // Atualiza a paginação
            const pagination = document.getElementById('tickets-pagination');
            if (pagination) {
                if (data.paginationHtml) {
                    pagination.innerHTML = '<div class="px-6 py-4 border-t border-gray-200">' + data.paginationHtml + '</div>';
                } else {
                    pagination.innerHTML = '';
                }
            }

            // Atualiza dados de notificação
            const novasMensagens = parseInt(data.novasMensagens) || 0;
            const novoTimestamp = data.ultimaMensagemTimestamp || null;
            
            ultimasMensagensNaoRespondidas = novasMensagens;
            ultimoTimestamp = novoTimestamp;
        })
        .catch(error => {
            console.log('Erro ao atualizar tabela:', error);
        });
    }

    // Atualiza a tabela a cada 30 segundos (sem recarregar a página)
    intervaloAtualizacaoTabela = setInterval(atualizarTabela, 30000);

    // Verifica notificações a cada 10 segundos (sem recarregar a página)
    intervaloNotificacao = setInterval(verificarNovidades, 10000);

    // Verifica imediatamente ao carregar a página
    document.addEventListener('DOMContentLoaded', function() {
        verificarNovidades();
    });

    // Para quando a página perde foco
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            // Continua verificando mesmo quando não está visível
        } else {
            // Quando volta, verifica imediatamente
            verificarNovidades();
        }
    });

    // Para quando a página é fechada
    window.addEventListener('beforeunload', function() {
        clearInterval(intervaloAtualizacaoTabela);
        clearInterval(intervaloNotificacao);
        pararNotificacao();
    });

    // Permite parar o som ao clicar na página (mas não reinicia automaticamente)
    document.addEventListener('click', function() {
        if (audioTocando) {
            pararNotificacao();
            usuarioParouManualmente = true; // Marca que usuário parou manualmente
        }
    });
    
    // Se o usuário interagir com a página de qualquer forma, para a notificação
    // mas permite que toque novamente se chegar nova mensagem
    document.addEventListener('keydown', function() {
        if (audioTocando) {
            pararNotificacao();
            usuarioParouManualmente = true;
        }
    });
</script>
@endpush
@endsection
