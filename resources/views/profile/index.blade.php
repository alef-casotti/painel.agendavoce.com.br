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
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Meu Perfil</h1>
                    <p class="text-gray-600">Gerencie suas informações pessoais e altere sua senha</p>
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

            <!-- Error Messages -->
            @if($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Editar Dados Pessoais -->
                <div class="card p-8">
                    <div class="mb-6">
                        <h2 class="text-2xl font-bold text-gray-900 mb-2">Dados Pessoais</h2>
                        <p class="text-gray-600 text-sm">Atualize suas informações de perfil</p>
                    </div>

                    <form method="POST" action="{{ route('profile.update') }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Nome -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                Nome Completo
                            </label>
                            <input 
                                type="text" 
                                id="name" 
                                name="name" 
                                value="{{ old('name', $user->name) }}" 
                                required
                                class="input-field @error('name') border-red-500 @enderror"
                                placeholder="Seu nome completo"
                            >
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                E-mail
                            </label>
                            <input 
                                type="email" 
                                id="email" 
                                name="email" 
                                value="{{ old('email', $user->email) }}" 
                                required
                                class="input-field @error('email') border-red-500 @enderror"
                                placeholder="seu@email.com"
                            >
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Role (read-only) -->
                        <div>
                            <label for="role" class="block text-sm font-medium text-gray-700 mb-2">
                                Nível de Acesso
                            </label>
                            <input 
                                type="text" 
                                id="role" 
                                value="{{ ucfirst($user->role) }}" 
                                disabled
                                class="input-field bg-gray-100 cursor-not-allowed"
                            >
                            <p class="mt-1 text-sm text-gray-500">O nível de acesso não pode ser alterado</p>
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-4">
                            <button 
                                type="submit" 
                                class="btn-primary w-full md:w-auto"
                            >
                                <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Salvar Alterações
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Alterar Senha -->
                <div class="card p-8">
                    <div class="mb-6">
                        <h2 class="text-2xl font-bold text-gray-900 mb-2">Alterar Senha</h2>
                        <p class="text-gray-600 text-sm">Altere sua senha de acesso ao sistema</p>
                    </div>

                    <form method="POST" action="{{ route('profile.password.update') }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Senha Atual -->
                        <div>
                            <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">
                                Senha Atual
                            </label>
                            <input 
                                type="password" 
                                id="current_password" 
                                name="current_password" 
                                required
                                class="input-field @error('current_password') border-red-500 @enderror"
                                placeholder="Digite sua senha atual"
                            >
                            @error('current_password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Nova Senha -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                Nova Senha
                            </label>
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                required
                                minlength="8"
                                class="input-field @error('password') border-red-500 @enderror"
                                placeholder="Mínimo de 8 caracteres"
                            >
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500">A senha deve ter no mínimo 8 caracteres</p>
                        </div>

                        <!-- Confirmar Nova Senha -->
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                                Confirmar Nova Senha
                            </label>
                            <input 
                                type="password" 
                                id="password_confirmation" 
                                name="password_confirmation" 
                                required
                                minlength="8"
                                class="input-field"
                                placeholder="Digite a nova senha novamente"
                            >
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-4">
                            <button 
                                type="submit" 
                                class="btn-primary w-full md:w-auto bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800"
                            >
                                <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                                </svg>
                                Alterar Senha
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Informações Adicionais -->
            <div class="mt-8 card p-8">
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Informações da Conta</h2>
                    <p class="text-gray-600 text-sm">Detalhes sobre sua conta no sistema</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <p class="text-sm text-gray-500 mb-1">Membro desde</p>
                        <p class="text-lg font-semibold text-gray-900">
                            {{ $user->created_at->format('d/m/Y') }}
                        </p>
                    </div>

                    <div class="p-4 bg-gray-50 rounded-lg">
                        <p class="text-sm text-gray-500 mb-1">Última atualização</p>
                        <p class="text-lg font-semibold text-gray-900">
                            {{ $user->updated_at->format('d/m/Y H:i') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection

