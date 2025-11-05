<tbody class="bg-white divide-y divide-gray-200">
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

