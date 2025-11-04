<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Entrar - Agenda Você</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Source+Sans+Pro:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            300: '#93c5fd',
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                        },
                    },
                    fontFamily: {
                        sans: ['Inter', 'system-ui', '-apple-system', 'sans-serif'],
                    },
                },
            },
        }
    </script>
    <style>
        * {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        body {
            letter-spacing: -0.011em;
        }
        .input-field {
            width: 100%;
            padding: 0.875rem 1rem;
            border: 1.5px solid #e5e7eb;
            border-radius: 0.625rem;
            transition: all 0.2s ease;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            font-size: 0.9375rem;
            font-weight: 400;
            line-height: 1.5;
            letter-spacing: -0.01em;
            background-color: #ffffff;
        }
        .input-field:hover {
            border-color: #93c5fd;
        }
        .input-field:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            background-color: #ffffff;
        }
        .input-field::placeholder {
            font-weight: 400;
            color: #9ca3af;
            letter-spacing: -0.01em;
        }
        .login-card {
            animation: slideUp 0.5s ease-out;
        }
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .btn-submit {
            transition: all 0.2s ease;
            position: relative;
            overflow: hidden;
            letter-spacing: 0.01em;
            line-height: 1.5;
        }
        .btn-submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 20px rgba(37, 99, 235, 0.3);
        }
        .btn-submit:active {
            transform: translateY(0);
        }
        .logo-agenda-voce {
            font-family: 'Source Sans Pro', sans-serif;
            font-weight: 700;
            letter-spacing: -0.015em;
            line-height: 1.2;
        }
        .login-title {
            letter-spacing: -0.02em;
            line-height: 1.2;
        }
        .label-text {
            letter-spacing: -0.01em;
            line-height: 1.5;
        }
        .text-relaxed {
            line-height: 1.6;
        }
        .background-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 25%, #f093fb 50%, #4facfe 75%, #00f2fe 100%);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
        }
        @keyframes gradientShift {
            0% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
            100% {
                background-position: 0% 50%;
            }
        }
        .background-pattern {
            position: relative;
            overflow: hidden;
        }
        .background-pattern::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: 
                radial-gradient(circle at 20% 50%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 40% 20%, rgba(255, 255, 255, 0.05) 0%, transparent 50%);
            pointer-events: none;
        }
        .background-blur {
            position: relative;
        }
        .background-blur::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(147, 197, 253, 0.15) 0%, transparent 70%);
            animation: pulse 8s ease-in-out infinite;
        }
        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
                opacity: 0.5;
            }
            50% {
                transform: scale(1.1);
                opacity: 0.8;
            }
        }
        @keyframes blob {
            0% {
                transform: translate(0px, 0px) scale(1);
            }
            33% {
                transform: translate(30px, -50px) scale(1.1);
            }
            66% {
                transform: translate(-20px, 20px) scale(0.9);
            }
            100% {
                transform: translate(0px, 0px) scale(1);
            }
        }
        .animate-blob {
            animation: blob 7s infinite;
        }
        .animation-delay-2000 {
            animation-delay: 2s;
        }
        .animation-delay-4000 {
            animation-delay: 4s;
        }
    </style>
    @vite(['resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen flex flex-col items-center justify-center px-4 py-12 relative background-pattern background-blur" style="background: linear-gradient(135deg, #e0f2fe 0%, #f0f9ff 25%, #ffffff 50%, #f0f9ff 75%, #dbeafe 100%);">
        <!-- Decorative circles -->
        <div class="absolute top-20 left-10 w-72 h-72 bg-blue-200 rounded-full mix-blend-multiply filter blur-xl opacity-30 animate-blob"></div>
        <div class="absolute top-40 right-10 w-72 h-72 bg-purple-200 rounded-full mix-blend-multiply filter blur-xl opacity-30 animate-blob animation-delay-2000"></div>
        <div class="absolute -bottom-8 left-1/2 w-72 h-72 bg-pink-200 rounded-full mix-blend-multiply filter blur-xl opacity-30 animate-blob animation-delay-4000"></div>
        <!-- Login Card -->
        <div class="w-full max-w-md bg-white/95 backdrop-blur-sm rounded-2xl shadow-2xl p-8 login-card relative z-10 border border-white/20">
            <!-- Card Header -->
            <div class="text-center mb-10">
                <h1 class="logo-agenda-voce text-3xl mb-3 leading-tight">Agenda Você</h1>
                <p class="text-gray-700 text-base font-medium mb-1.5 leading-relaxed">Entrar na sua conta</p>
                <p class="text-gray-500 text-sm leading-relaxed">Acesse sua conta em nosso painel</p>
            </div>

            <!-- Messages -->
            @if(session('success'))
                <div class="mb-6 p-3.5 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm leading-relaxed">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-3.5 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm leading-relaxed">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-lg text-sm">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 mr-2.5 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <ul class="list-disc list-inside space-y-1.5 leading-relaxed">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <!-- Form -->
            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf
                
                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2.5 label-text">
                        E-mail
                    </label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        value="{{ old('email') }}" 
                        placeholder="seu@email.com"
                        required 
                        autofocus
                        class="input-field @error('email') border-red-400 bg-red-50 @enderror"
                    >
                    @error('email')
                        <p class="mt-1.5 text-sm text-red-600 leading-relaxed">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-2.5 label-text">
                        Senha
                    </label>
                    <div class="relative">
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            placeholder="Sua senha"
                            required
                            class="input-field @error('password') border-red-400 bg-red-50 @enderror pr-12"
                        >
                        <button 
                            type="button" 
                            onclick="togglePassword()"
                            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors p-1"
                            aria-label="Mostrar/Ocultar senha"
                        >
                            <svg id="eye-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            <svg id="eye-off-icon" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1.5 text-sm text-red-600 leading-relaxed">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember & Forgot -->
                <div class="flex items-center justify-between text-sm">
                    <label class="flex items-center cursor-pointer group">
                        <input 
                            type="checkbox" 
                            name="remember" 
                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:ring-offset-0 cursor-pointer transition-colors"
                        >
                        <span class="ml-2.5 text-gray-700 group-hover:text-gray-900 transition-colors leading-relaxed">Lembrar de mim</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button 
                    type="submit" 
                    class="btn-submit w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3.5 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 text-base shadow-lg"
                >
                    Entrar
                </button>
            </form>

        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');
            const eyeOffIcon = document.getElementById('eye-off-icon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.add('hidden');
                eyeOffIcon.classList.remove('hidden');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('hidden');
                eyeOffIcon.classList.add('hidden');
            }
        }
    </script>
</body>
</html>
