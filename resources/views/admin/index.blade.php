@extends('layouts.app')

@section('content')
<div class="flex min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <x-sidebar />
    <x-header />

    <!-- Main Content -->
    <main class="flex-1 ml-64 mt-16 overflow-y-auto">
        <div class="p-8">
            <!-- Page Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Gestão de Usuários</h1>
                <p class="text-gray-600">Gerencie contas, permissões e acessos do sistema</p>
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
                    <strong>Bem-vindo à Gestão de Usuários!</strong> Mantenha os cadastros e permissões sempre atualizados.
                </div>
            </div>

            <!-- Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="card p-6 border-2 border-red-200">
                    <div class="flex items-center mb-4">
                        <div class="p-3 bg-red-100 rounded-lg">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                        </div>
                        <h3 class="ml-3 font-semibold text-gray-800">Usuários</h3>
                    </div>
                    <p class="text-gray-600 text-sm mb-4">Gerencie os usuários, suas permissões e dados cadastrais.</p>
                    <a href="{{ route('admin.users.index') }}" class="text-red-600 hover:text-red-700 text-sm font-medium">
                        Ir para lista de usuários →
                    </a>
                </div>

                <div class="card p-6 border border-gray-200 hover:border-green-500 transition-colors">
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

                <div class="card p-6 border border-gray-200 hover:border-blue-500 transition-colors">
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
