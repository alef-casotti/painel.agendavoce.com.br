@extends('layouts.app')

@section('content')
<div class="flex min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <x-sidebar />
    <x-header />

    <!-- Main Content -->
    <main class="flex-1 lg:ml-3 mt-16 overflow-y-auto">
        <div class="p-4 lg:p-8">
            <!-- Page Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Editar Usuário</h1>
                <p class="text-gray-600">Atualize as informações do usuário</p>
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

            <!-- Form Card -->
            <div class="card p-6">
                <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Nome -->
                    <div class="mb-6">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nome <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="name" 
                            name="name" 
                            value="{{ old('name', $user->name) }}" 
                            required
                            class="input-field @error('name') border-red-500 @enderror"
                            placeholder="Nome completo do usuário"
                        >
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="mb-6">
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            value="{{ old('email', $user->email) }}" 
                            required
                            class="input-field @error('email') border-red-500 @enderror"
                            placeholder="usuario@exemplo.com"
                        >
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Senha -->
                    <div class="mb-6">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            Nova Senha <span class="text-gray-500 text-xs">(deixe em branco para manter a atual)</span>
                        </label>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            minlength="8"
                            class="input-field @error('password') border-red-500 @enderror"
                            placeholder="Mínimo de 8 caracteres"
                        >
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirmar Senha -->
                    <div class="mb-6">
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                            Confirmar Nova Senha
                        </label>
                        <input 
                            type="password" 
                            id="password_confirmation" 
                            name="password_confirmation" 
                            minlength="8"
                            class="input-field"
                            placeholder="Digite a senha novamente"
                        >
                    </div>

                    <!-- Perfil/Role -->
                    <div class="mb-6">
                        <label for="role" class="block text-sm font-medium text-gray-700 mb-2">
                            Perfil <span class="text-red-500">*</span>
                        </label>
                        <select 
                            id="role" 
                            name="role" 
                            required
                            class="input-field @error('role') border-red-500 @enderror"
                        >
                            <option value="">Selecione um perfil</option>
                            <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="financeiro" {{ old('role', $user->role) == 'financeiro' ? 'selected' : '' }}>Financeiro</option>
                            <option value="suporte" {{ old('role', $user->role) == 'suporte' ? 'selected' : '' }}>Suporte</option>
                            <option value="customer_success_manager" {{ old('role', $user->role) == 'customer_success_manager' ? 'selected' : '' }}>Customer Success Manager</option>
                        </select>
                        @error('role')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">
                            <strong>Admin:</strong> Acesso completo ao sistema<br>
                            <strong>Financeiro:</strong> Acesso à área financeira<br>
                            <strong>Suporte:</strong> Acesso à área de suporte<br>
                            <strong>Customer Success Manager:</strong> Acesso ampliado ao suporte e relacionamento
                        </p>
                    </div>

                    <!-- Buttons -->
                    <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200">
                        <a href="{{ route('admin.users.index') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                            Cancelar
                        </a>
                        <button type="submit" class="btn-primary">
                            Atualizar Usuário
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>
@endsection

