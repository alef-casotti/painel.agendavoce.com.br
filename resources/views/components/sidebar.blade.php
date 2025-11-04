@php
    $user = auth()->user();
    $currentRoute = request()->route()->getName();
@endphp

<aside class="w-64 bg-gradient-to-b from-blue-700 to-blue-800 shadow-lg">
    <div class="flex flex-col h-full">
        <!-- Logo/Header -->
        <div class="p-6 border-b border-blue-600">
            <h2 class="text-white text-xl font-bold">Agenda Você</h2>
            <p class="text-blue-200 text-xs mt-1">{{ $user->name }}</p>
            <span class="inline-block mt-2 px-2 py-1 bg-blue-600 text-white text-xs rounded-md">
                {{ ucfirst($user->role) }}
            </span>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 p-4 space-y-2">
            <a href="{{ route('dashboard') }}" 
               class="flex items-center px-4 py-3 {{ $currentRoute === 'dashboard' ? 'text-white bg-blue-600' : 'text-blue-100 hover:bg-blue-700' }} rounded-lg transition-colors">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                Dashboard
            </a>

            @if($user->isAdmin() || $user->isFinanceiro())
                <a href="{{ route('financeiro.index') }}" 
                   class="flex items-center px-4 py-3 {{ str_contains($currentRoute, 'financeiro') ? 'text-white bg-blue-600' : 'text-blue-100 hover:bg-blue-700' }} rounded-lg transition-colors">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Financeiro
                </a>
            @endif

            @if($user->isAdmin() || $user->isSuporte())
                <a href="{{ route('suporte.index') }}" 
                   class="flex items-center px-4 py-3 {{ str_contains($currentRoute, 'suporte') ? 'text-white bg-blue-600' : 'text-blue-100 hover:bg-blue-700' }} rounded-lg transition-colors">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    Suporte
                </a>
            @endif

            @if($user->isAdmin())
                <a href="{{ route('admin.index') }}" 
                   class="flex items-center px-4 py-3 {{ str_contains($currentRoute, 'admin') ? 'text-white bg-blue-600' : 'text-blue-100 hover:bg-blue-700' }} rounded-lg transition-colors">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                    Administração
                </a>
            @endif
        </nav>

        <!-- Logout -->
        <div class="p-4 border-t border-blue-600">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" 
                        class="w-full flex items-center justify-center px-4 py-3 text-blue-100 hover:bg-blue-700 rounded-lg transition-colors">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    Sair
                </button>
            </form>
        </div>
    </div>
</aside>

