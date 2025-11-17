@php
    $user = auth()->user();
    $currentRoute = request()->route()->getName();
@endphp

<!-- Container com Alpine.js para controlar o sidebar -->
<div x-data="{ sidebarOpen: false }" 
     @keydown.escape.window="sidebarOpen = false"
     @sidebar-open.window="sidebarOpen = true"
     @sidebar-close.window="sidebarOpen = false"
     id="sidebar-container"
     class="flex-shrink-0 w-0 lg:w-64">
    <!-- Overlay escuro quando sidebar está aberto no mobile -->
    <div 
        x-show="sidebarOpen"
        @click="sidebarOpen = false"
        x-transition:enter="transition-opacity ease-linear duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-linear duration-300"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-gray-600 bg-opacity-75 z-40 lg:hidden"
        style="display: none;"
        x-cloak
    ></div>

    <!-- Sidebar -->
    <aside 
        :class="{ '-translate-x-full': !sidebarOpen }"
        class="fixed inset-y-0 left-0 w-64 bg-gradient-to-b from-blue-50 via-indigo-50 to-blue-50 border-r border-blue-100 shadow-sm flex flex-col z-50 lg:z-30 transform lg:translate-x-0 transition-transform duration-300 ease-in-out"
        style="height: 100vh;"
    >
        <!-- Logo/Header -->
        <div class="h-16 flex items-center justify-between px-6 border-b border-blue-100">
            <a href="{{ route('dashboard') }}" class="logo-agenda-voce text-blue-700 text-2xl hover:text-blue-800 transition-colors">
                Agenda Você
            </a>
            <!-- Botão fechar no mobile -->
            <button 
                @click="sidebarOpen = false"
                class="lg:hidden p-2 text-gray-500 hover:text-gray-700 rounded-lg hover:bg-gray-100 transition-colors"
                aria-label="Fechar menu"
            >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 p-4 space-y-2 overflow-y-auto">
            <a href="{{ route('dashboard') }}" 
               class="sidebar-link flex items-center px-4 py-3.5 {{ $currentRoute === 'dashboard' ? 'active text-blue-800 bg-gradient-to-r from-blue-50 to-white border-l-4 border-blue-600 shadow-md' : 'text-indigo-900 hover:bg-blue-50/80 hover:text-blue-700' }} rounded-r-lg transition-all duration-200 group"
               @click="sidebarOpen = false">
                <svg class="w-5 h-5 mr-3 flex-shrink-0 {{ $currentRoute === 'dashboard' ? 'text-blue-600' : 'text-indigo-600 group-hover:text-blue-600' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                <span>Dashboard</span>
            </a>

            @if($user->isAdmin() || $user->isFinanceiro())
                <a href="{{ route('financeiro.index') }}" 
                   class="sidebar-link flex items-center px-4 py-3.5 {{ str_contains($currentRoute, 'financeiro') ? 'active text-blue-800 bg-gradient-to-r from-blue-50 to-white border-l-4 border-blue-600 shadow-md' : 'text-indigo-900 hover:bg-blue-50/80 hover:text-blue-700' }} rounded-r-lg transition-all duration-200 group"
                   @click="sidebarOpen = false">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0 {{ str_contains($currentRoute, 'financeiro') ? 'text-blue-600' : 'text-indigo-600 group-hover:text-blue-600' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Financeiro</span>
                </a>
            @endif

            @if($user->isAdmin() || $user->isSuporte())
                <a href="{{ route('suporte.index') }}" 
                   class="sidebar-link flex items-center px-4 py-3.5 {{ str_contains($currentRoute, 'suporte') ? 'active text-blue-800 bg-gradient-to-r from-blue-50 to-white border-l-4 border-blue-600 shadow-md' : 'text-indigo-900 hover:bg-blue-50/80 hover:text-blue-700' }} rounded-r-lg transition-all duration-200 group"
                   @click="sidebarOpen = false">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0 {{ str_contains($currentRoute, 'suporte') ? 'text-blue-600' : 'text-indigo-600 group-hover:text-blue-600' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    <span>Suporte</span>
                </a>
            @endif

            @if($user->isAdmin())
                <a href="{{ route('admin.users.index') }}" 
                   class="sidebar-link flex items-center px-4 py-3.5 {{ str_contains($currentRoute, 'admin.users') ? 'active text-blue-800 bg-gradient-to-r from-blue-50 to-white border-l-4 border-blue-600 shadow-md' : 'text-indigo-900 hover:bg-blue-50/80 hover:text-blue-700' }} rounded-r-lg transition-all duration-200 group"
                   @click="sidebarOpen = false">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0 {{ str_contains($currentRoute, 'admin.users') ? 'text-blue-600' : 'text-indigo-600 group-hover:text-blue-600' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    <span>Usuários</span>
                </a>
            @endif
        </nav>
    </aside>

    <!-- Script para expor função global para abrir o sidebar -->
    <script>
        // Define a função imediatamente, antes do Alpine carregar
        (function() {
            window.openSidebar = function() {
                window.dispatchEvent(new CustomEvent('sidebar-open'));
            };
            
            window.closeSidebar = function() {
                window.dispatchEvent(new CustomEvent('sidebar-close'));
            };
        })();
    </script>
</div>
