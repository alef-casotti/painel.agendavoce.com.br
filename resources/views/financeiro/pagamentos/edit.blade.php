@extends('layouts.app')

@section('title', 'Editar Pagamento - Agenda Você')

@section('content')
<div class="flex min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <x-sidebar />
    <x-header />

    <main class="flex-1 lg:ml-3 mt-16 overflow-y-auto">
        <div class="p-4 lg:p-8 space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Editar pagamento</h1>
                    <p class="text-gray-600">Atualize as informações desta despesa.</p>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('financeiro.pagamentos.show', $pagamento) }}" class="inline-flex items-center px-4 py-2 border border-blue-200 text-blue-600 rounded-lg hover:bg-blue-50 transition">
                        Ver detalhes
                    </a>
                    <a href="{{ route('financeiro.pagamentos.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-200 rounded-lg text-gray-600 hover:bg-gray-50 transition">
                        Voltar para lista
                    </a>
                </div>
            </div>

            @if($errors->any())
                <div class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg">
                    <p class="font-semibold mb-2">Ops! Encontramos alguns problemas:</p>
                    <ul class="list-disc pl-5 space-y-1 text-sm">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('financeiro.pagamentos.update', $pagamento) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                @include('financeiro.pagamentos._form', [
                    'pagamento' => $pagamento,
                    'categorias' => $categorias,
                    'centrosCusto' => $centrosCusto,
                    'statuses' => $statuses,
                ])

                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('financeiro.pagamentos.index') }}" class="px-4 py-2 border border-gray-200 rounded-lg text-gray-600 hover:bg-gray-50 transition">
                        Cancelar
                    </a>
                    <button type="submit" class="btn-primary inline-flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Salvar alterações
                    </button>
                </div>
            </form>
        </div>
    </main>
</div>
@endsection

