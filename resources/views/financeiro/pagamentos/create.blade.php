@extends('layouts.app')

@section('title', 'Novo Pagamento - Agenda Você')

@section('content')
<div class="flex min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <x-sidebar />
    <x-header />

    <main class="flex-1 lg:ml-3 mt-16 overflow-y-auto">
        <div class="p-4 lg:p-8 space-y-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Novo pagamento</h1>
                    <p class="text-gray-600">Cadastre uma nova despesa ou saída financeira.</p>
                </div>

                <a href="{{ route('financeiro.pagamentos.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-200 rounded-lg text-gray-600 hover:bg-gray-50 transition w-full md:w-auto justify-center md:justify-start">
                    Voltar para lista
                </a>
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

            <form action="{{ route('financeiro.pagamentos.store') }}" method="POST" class="space-y-6">
                @csrf
                @include('financeiro.pagamentos._form', [
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
                        Salvar pagamento
                    </button>
                </div>
            </form>
        </div>
    </main>
</div>
@endsection

