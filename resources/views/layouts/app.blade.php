<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Painel - Agenda Você')</title>
    
    <!-- Tailwind CSS CDN -->
    <script>
        console.log('Script iniciado - tentando carregar Tailwind...');
        var tailwindScript = document.createElement('script');
        tailwindScript.src = 'https://cdn.tailwindcss.com';
        tailwindScript.onload = function() {
            console.log('Tailwind CDN carregado!');
            if (typeof tailwind !== 'undefined') {
                console.log('Tailwind objeto disponível');
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
                        },
                    },
                }
                console.log('Tailwind configurado com sucesso');
            } else {
                console.error('Tailwind objeto não encontrado após carregar script');
            }
        };
        tailwindScript.onerror = function() {
            console.error('ERRO: Falha ao carregar Tailwind CDN');
        };
        document.head.appendChild(tailwindScript);
    </script>
    
    <!-- Estilos customizados -->
    <style>
        .btn-primary {
            background-color: #2563eb !important;
            color: white !important;
            font-weight: 500;
            padding: 0.625rem 1.5rem;
            border-radius: 0.5rem;
            transition: background-color 0.2s;
        }
        .btn-primary:hover {
            background-color: #1d4ed8 !important;
        }
        .input-field {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid #93c5fd;
            border-radius: 0.5rem;
            transition: all 0.2s;
        }
        .input-field:focus {
            outline: none;
            border-color: transparent;
            box-shadow: 0 0 0 2px #3b82f6;
        }
    </style>
    
    @vite(['resources/js/app.js'])
    @yield('styles')
</head>
<body class="bg-gray-50">
    @yield('content')
    @yield('scripts')
</body>
</html>
