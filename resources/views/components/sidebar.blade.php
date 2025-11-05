@php
    $user = auth()->user();
    $currentRoute = request()->route()->getName();
@endphp

<aside class="w-64 bg-gradient-to-b from-blue-50 via-indigo-50 to-blue-50 border-r border-blue-100 shadow-sm flex flex-col fixed left-0 top-0 bottom-0 z-30" style="height: 100vh;">
    <!-- Logo/Header -->
    <div class="h-16 flex items-center px-6 border-b border-blue-100">
        <a href="{{ route('dashboard') }}" class="logo-agenda-voce text-blue-700 text-2xl hover:text-blue-800 transition-colors">
            Agenda Você
        </a>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
        <a href="{{ route('dashboard') }}" 
           class="sidebar-link flex items-center px-4 py-3 {{ $currentRoute === 'dashboard' ? 'active text-blue-700 bg-white border-r-2 border-blue-600 shadow-sm' : 'text-blue-900 hover:bg-white/60' }} rounded-lg transition-all group">
            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
            <span class="font-medium">Dashboard</span>
        </a>

        <a href="{{ route('profile.index') }}" 
           class="sidebar-link flex items-center px-4 py-3 {{ str_contains($currentRoute, 'profile') ? 'active text-blue-700 bg-white border-r-2 border-blue-600 shadow-sm' : 'text-blue-900 hover:bg-white/60' }} rounded-lg transition-all group">
            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
            <span class="font-medium">Meu Perfil</span>
        </a>

        @if($user->isAdmin() || $user->isFinanceiro())
            <a href="{{ route('financeiro.index') }}" 
               class="sidebar-link flex items-center px-4 py-3 {{ str_contains($currentRoute, 'financeiro') ? 'active text-blue-700 bg-white border-r-2 border-blue-600 shadow-sm' : 'text-blue-900 hover:bg-white/60' }} rounded-lg transition-all group">
                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="font-medium">Financeiro</span>
            </a>
        @endif

        @if($user->isAdmin() || $user->isSuporte())
            <a href="{{ route('suporte.index') }}" 
               class="sidebar-link flex items-center px-4 py-3 {{ str_contains($currentRoute, 'suporte') ? 'active text-blue-700 bg-white border-r-2 border-blue-600 shadow-sm' : 'text-blue-900 hover:bg-white/60' }} rounded-lg transition-all group">
                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
                <span class="font-medium">Suporte</span>
            </a>
        @endif

        @if($user->isAdmin())
            <a href="{{ route('admin.users.index') }}" 
               class="sidebar-link flex items-center px-4 py-3 {{ str_contains($currentRoute, 'admin.users') ? 'active text-blue-700 bg-white border-r-2 border-blue-600 shadow-sm' : 'text-blue-900 hover:bg-white/60' }} rounded-lg transition-all group">
                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
                <span class="font-medium">Usuários</span>
            </a>
        @endif
    </nav>
</aside>

