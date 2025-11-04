@extends('layouts.app')

@section('content')
<div class="flex h-screen bg-gray-50">
    <x-sidebar />

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto">
        <div class="p-8">
            <!-- Header -->
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-800">Área Administrativa</h1>
                <p class="text-gray-500 mt-1">Acesso completo a todas as funcionalidades</p>
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

            <!-- Alert -->
            <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <strong>Bem-vindo à Área Administrativa!</strong> Você tem acesso completo a todas as funcionalidades do sistema.
                </div>
            </div>

            <!-- Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white rounded-xl shadow-sm p-6 border-2 border-red-200">
                    <div class="flex items-center mb-4">
                        <div class="p-3 bg-red-100 rounded-lg">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                        </div>
                        <h3 class="ml-3 font-semibold text-gray-800">Administração</h3>
                    </div>
                    <p class="text-gray-600 text-sm">Você está na área administrativa com acesso total ao sistema.</p>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200 hover:border-green-500 transition-colors">
                    <div class="flex items-center mb-4">
                        <div class="p-3 bg-green-100 rounded-lg">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="ml-3 font-semibold text-gray-800">Financeiro</h3>
                    </div>
                    <p class="text-gray-600 text-sm mb-4">Acesso completo à área financeira.</p>
                    <a href="{{ route('financeiro.index') }}" class="text-green-600 hover:text-green-700 text-sm font-medium">
                        Acessar →
                    </a>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200 hover:border-blue-500 transition-colors">
                    <div class="flex items-center mb-4">
                        <div class="p-3 bg-blue-100 rounded-lg">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                        </div>
                        <h3 class="ml-3 font-semibold text-gray-800">Suporte</h3>
                    </div>
                    <p class="text-gray-600 text-sm mb-4">Acesso completo à área de suporte.</p>
                    <a href="{{ route('suporte.index') }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                        Acessar →
                    </a>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection
