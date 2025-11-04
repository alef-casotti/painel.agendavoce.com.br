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
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Dashboard</h1>
                    <p class="text-gray-600">Bem-vindo ao seu painel de controle</p>
                </div>
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
                <div class="card p-6 group hover:scale-105 transition-transform">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="p-3 bg-gradient-to-br from-blue-100 to-blue-200 rounded-xl group-hover:from-blue-200 group-hover:to-blue-300 transition-colors">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-gray-500 text-sm font-medium">Usuário</p>
                                <p class="text-gray-900 font-bold text-lg mt-1">{{ $user->name }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card p-6 group hover:scale-105 transition-transform">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="p-3 bg-gradient-to-br from-green-100 to-green-200 rounded-xl group-hover:from-green-200 group-hover:to-green-300 transition-colors">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-gray-500 text-sm font-medium">Nível de Acesso</p>
                                <p class="text-gray-900 font-bold text-lg mt-1">{{ ucfirst($user->role) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card p-6 group hover:scale-105 transition-transform">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="p-3 bg-gradient-to-br from-purple-100 to-purple-200 rounded-xl group-hover:from-purple-200 group-hover:to-purple-300 transition-colors">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-gray-500 text-sm font-medium">E-mail</p>
                                <p class="text-gray-900 font-bold text-sm mt-1 break-all">{{ $user->email }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Areas Section -->
            <div class="card p-8">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 mb-1">Áreas Disponíveis</h2>
                        <p class="text-gray-600 text-sm">Selecione uma área para acessar</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @if($user->isAdmin() || $user->isFinanceiro())
                        <a href="{{ route('financeiro.index') }}" 
                           class="card p-6 border-2 border-transparent hover:border-green-200 hover:shadow-lg transition-all group">
                            <div class="flex items-start justify-between mb-4">
                                <div class="p-3 bg-gradient-to-br from-green-100 to-green-200 rounded-xl group-hover:from-green-200 group-hover:to-green-300 transition-colors">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <svg class="w-5 h-5 text-gray-400 group-hover:text-green-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 mb-2">Área Financeira</h3>
                            <p class="text-gray-600 text-sm mb-4 leading-relaxed">Acesso completo à área financeira do sistema</p>
                            <span class="text-green-600 text-sm font-semibold group-hover:underline inline-flex items-center">
                                Acessar
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </span>
                        </a>
                    @endif

                    @if($user->isAdmin() || $user->isSuporte())
                        <a href="{{ route('suporte.index') }}" 
                           class="card p-6 border-2 border-transparent hover:border-blue-200 hover:shadow-lg transition-all group">
                            <div class="flex items-start justify-between mb-4">
                                <div class="p-3 bg-gradient-to-br from-blue-100 to-blue-200 rounded-xl group-hover:from-blue-200 group-hover:to-blue-300 transition-colors">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                    </svg>
                                </div>
                                <svg class="w-5 h-5 text-gray-400 group-hover:text-blue-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 mb-2">Área de Suporte</h3>
                            <p class="text-gray-600 text-sm mb-4 leading-relaxed">Acesso completo à área de suporte do sistema</p>
                            <span class="text-blue-600 text-sm font-semibold group-hover:underline inline-flex items-center">
                                Acessar
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </span>
                        </a>
                    @endif

                    @if($user->isAdmin())
                        <a href="{{ route('admin.index') }}" 
                           class="card p-6 border-2 border-transparent hover:border-red-200 hover:shadow-lg transition-all group">
                            <div class="flex items-start justify-between mb-4">
                                <div class="p-3 bg-gradient-to-br from-red-100 to-red-200 rounded-xl group-hover:from-red-200 group-hover:to-red-300 transition-colors">
                                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                    </svg>
                                </div>
                                <svg class="w-5 h-5 text-gray-400 group-hover:text-red-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 mb-2">Administração</h3>
                            <p class="text-gray-600 text-sm mb-4 leading-relaxed">Acesso completo a todas as áreas do sistema</p>
                            <span class="text-red-600 text-sm font-semibold group-hover:underline inline-flex items-center">
                                Acessar
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </span>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </main>
</div>
@endsection
