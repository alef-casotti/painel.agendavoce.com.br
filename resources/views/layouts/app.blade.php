<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Painel - Agenda Você')</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@400;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
    <script>
        console.log('Script iniciado - tentando carregar Tailwind...');
        
        // Função para mostrar a página quando tudo estiver carregado
        function showPage() {
            // Aguarda alguns frames para garantir que Tailwind e Alpine processaram tudo
            requestAnimationFrame(function() {
                requestAnimationFrame(function() {
                    document.body.classList.add('loaded');
                });
            });
        }
        
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
        
        // Aguarda o carregamento completo da página e recursos
        if (document.readyState === 'loading') {
            window.addEventListener('load', function() {
                // Aguarda um pouco mais para garantir que Alpine.js inicializou
                setTimeout(showPage, 200);
            });
        } else {
            // Se já estiver carregado, aguarda um pouco e mostra
            setTimeout(showPage, 200);
        }
    </script>
    
    <!-- Estilos customizados -->
    <style>
        * {
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        body {
            opacity: 0;
            transition: opacity 0.3s ease-in;
        }
        body.loaded {
            opacity: 1;
        }
        .logo-agenda-voce {
            font-family: 'Source Sans Pro', sans-serif;
            font-weight: 700;
            letter-spacing: -0.015em;
            line-height: 1.2;
        }
        .btn-primary {
            background-color: #2563eb !important;
            color: white !important;
            font-weight: 500;
            padding: 0.625rem 1.5rem;
            border-radius: 0.5rem;
            transition: all 0.2s ease;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }
        .btn-primary:hover {
            background-color: #1d4ed8 !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            transform: translateY(-1px);
        }
        .input-field {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            transition: all 0.2s ease;
            background-color: #ffffff;
        }
        .input-field:hover {
            border-color: #93c5fd;
        }
        .input-field:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        .card {
            background: white;
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
        }
        .card:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        .sidebar-link {
            position: relative;
            transition: all 0.2s ease;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }
        .sidebar-link span {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            font-weight: 500;
            font-size: 14px;
            letter-spacing: -0.01em;
            line-height: 1.4;
        }
        .sidebar-link.active {
            transform: translateX(2px);
        }
        /* Alpine.js fallback para dropdown */
        [x-cloak] { display: none !important; }
    </style>
    
    <!-- Alpine.js para interatividade -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    @vite(['resources/js/app.js'])
    @yield('styles')
    @stack('scripts')
</head>
<body class="bg-gray-50 antialiased">
    @yield('content')
    @yield('scripts')
</body>
</html>
