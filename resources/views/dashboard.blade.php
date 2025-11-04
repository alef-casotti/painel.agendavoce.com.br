@extends('layouts.app')

@section('content')
<div class="flex h-screen bg-gray-50">
    <x-sidebar />

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto">
        <div class="p-8">
            <!-- Header -->
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-800">Dashboard</h1>
                <p class="text-gray-500 mt-1">Bem-vindo ao seu painel de controle</p>
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

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <div class="flex items-center">
                        <div class="p-3 bg-blue-100 rounded-lg">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-gray-500 text-sm">Usuário</p>
                            <p class="text-gray-800 font-semibold text-lg">{{ $user->name }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <div class="flex items-center">
                        <div class="p-3 bg-green-100 rounded-lg">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-gray-500 text-sm">Nível de Acesso</p>
                            <p class="text-gray-800 font-semibold text-lg">{{ ucfirst($user->role) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <div class="flex items-center">
                        <div class="p-3 bg-purple-100 rounded-lg">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-gray-500 text-sm">E-mail</p>
                            <p class="text-gray-800 font-semibold text-sm">{{ $user->email }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Areas Section -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Áreas Disponíveis</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @if($user->isAdmin() || $user->isFinanceiro())
                        <a href="{{ route('financeiro.index') }}" 
                           class="p-5 border-2 border-gray-200 rounded-lg hover:border-green-500 hover:bg-green-50 transition-all group">
                            <div class="flex items-center mb-3">
                                <div class="p-2 bg-green-100 rounded-lg group-hover:bg-green-200">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <h3 class="ml-3 font-semibold text-gray-800">Área Financeira</h3>
                            </div>
                            <p class="text-gray-500 text-sm mb-3">Acesso completo à área financeira</p>
                            <span class="text-green-600 text-sm font-medium group-hover:underline">
                                Acessar →
                            </span>
                        </a>
                    @endif

                    @if($user->isAdmin() || $user->isSuporte())
                        <a href="{{ route('suporte.index') }}" 
                           class="p-5 border-2 border-gray-200 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition-all group">
                            <div class="flex items-center mb-3">
                                <div class="p-2 bg-blue-100 rounded-lg group-hover:bg-blue-200">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                    </svg>
                                </div>
                                <h3 class="ml-3 font-semibold text-gray-800">Área de Suporte</h3>
                            </div>
                            <p class="text-gray-500 text-sm mb-3">Acesso completo à área de suporte</p>
                            <span class="text-blue-600 text-sm font-medium group-hover:underline">
                                Acessar →
                            </span>
                        </a>
                    @endif

                    @if($user->isAdmin())
                        <a href="{{ route('admin.index') }}" 
                           class="p-5 border-2 border-gray-200 rounded-lg hover:border-red-500 hover:bg-red-50 transition-all group">
                            <div class="flex items-center mb-3">
                                <div class="p-2 bg-red-100 rounded-lg group-hover:bg-red-200">
                                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                    </svg>
                                </div>
                                <h3 class="ml-3 font-semibold text-gray-800">Administração</h3>
                            </div>
                            <p class="text-gray-500 text-sm mb-3">Acesso completo a todas as áreas</p>
                            <span class="text-red-600 text-sm font-medium group-hover:underline">
                                Acessar →
                            </span>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </main>
</div>
@endsection
