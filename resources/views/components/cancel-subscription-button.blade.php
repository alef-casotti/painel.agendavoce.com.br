@props([
    'clienteId',
    'variant' => 'hero',
])

@php
    $classes = $variant === 'section'
        ? 'inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-red-700 bg-red-50 rounded-xl hover:bg-red-100 ring-1 ring-red-200 transition-all w-full justify-center'
        : 'inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-white bg-red-500 rounded-xl hover:bg-red-600 ring-1 ring-red-400/50 transition-all';
@endphp

<form method="POST" action="{{ route('clientes.cancel-subscription', $clienteId) }}" class="{{ $variant === 'section' ? 'w-full' : 'inline' }}"
      onsubmit="return confirm('Agendar cancelamento da assinatura deste cliente?');">
    @csrf
    @method('PATCH')
    <button type="submit" {{ $attributes->merge(['class' => $classes]) }}>
        Cancelar assinatura
    </button>
</form>
