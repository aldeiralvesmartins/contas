<!DOCTYPE html>
<html lang="pt-br" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>{{ $title ?? 'Finance Control' }}</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-full bg-gradient-to-br from-slate-50 to-blue-50 font-inter">

<!-- Navigation (APENAS DESKTOP) -->
<nav class="hidden lg:block bg-white/80 backdrop-blur-md border-b border-slate-200 sticky top-0 z-40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Logo -->
            <a href="/">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-r from-blue-600 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                            <span class="text-white font-bold">
                                <i class="fas fa-wallet"></i>
                            </span>
                    </div>
                    <h1 class="text-2xl font-bold bg-gradient-to-r from-slate-800 to-slate-600 bg-clip-text text-transparent">
                        FinanceControl
                    </h1>
                </div>
            </a>

            <!-- Navigation Links -->
            <div class="flex items-center space-x-1">
                <a href="{{ route('dashboard') }}"
                   class="px-4 py-2 rounded-lg font-medium text-slate-600 hover:text-blue-600 hover:bg-blue-50 transition-all duration-200 flex items-center space-x-2 {{ request()->routeIs('dashboard') ? 'text-blue-600 bg-blue-50' : '' }}">
                    <i class="fas fa-chart-line w-4"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('accounts.index') }}"
                   class="px-4 py-2 rounded-lg font-medium text-slate-600 hover:text-blue-600 hover:bg-blue-50 transition-all duration-200 flex items-center space-x-2 {{ request()->routeIs('accounts.*') ? 'text-blue-600 bg-blue-50' : '' }}">
                    <i class="fas fa-file-invoice-dollar w-4"></i>
                    <span>Contas</span>
                </a>
                <a href="{{ route('transactions.index') }}"
                   class="px-4 py-2 rounded-lg font-medium text-slate-600 hover:text-blue-600 hover:bg-blue-50 transition-all duration-200 flex items-center space-x-2 {{ request()->routeIs('transactions.*') ? 'text-blue-600 bg-blue-50' : '' }}">
                    <i class="fas fa-exchange-alt w-4"></i>
                    <span>Transações</span>
                </a>
                <a href="{{ route('categories.index') }}"
                   class="px-4 py-2 rounded-lg font-medium text-slate-600 hover:text-blue-600 hover:bg-blue-50 transition-all duration-200 flex items-center space-x-2 {{ request()->routeIs('categories.*') ? 'text-blue-600 bg-blue-50' : '' }}">
                    <i class="fas fa-tags w-4"></i>
                    <span>Categorias</span>
                </a>
            </div>
        </div>
    </div>
</nav>

<!-- Header Mobile (para telas pequenas) -->
<div class="lg:hidden bg-white/90 backdrop-blur-sm border-b border-slate-200/80 sticky top-0 z-40">
    <div class="flex items-center justify-between px-4 py-3">
        <!-- Logo Mini -->
        <div class="flex items-center space-x-2">
            <div class="w-8 h-8 bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg flex items-center justify-center shadow-md">
                    <span class="text-white text-sm">
                        <i class="fas fa-wallet"></i>
                    </span>
            </div>
            <h2 class="text-lg font-bold bg-gradient-to-r from-slate-800 to-slate-600 bg-clip-text text-transparent">
                FinanceControl
            </h2>
        </div>

        <!-- Data Atual (Opcional) -->
        <div class="text-sm text-slate-600 font-medium flex items-center space-x-1">
            <i class="far fa-clock mr-1"></i>
            <span>{{ now()->translatedFormat('d/m/Y H:i:s') }}</span>
        </div>
    </div>
</div>

<!-- Main Content -->
<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 lg:py-8 pb-20 lg:pb-8">
    @yield('content')
</main>

<!-- Bottom Navigation Mobile (APENAS MOBILE) -->
<div class="lg:hidden fixed bottom-0 left-0 right-0 z-50 border-t border-slate-200 bg-white/95 backdrop-blur-lg shadow-[0_-4px_20px_-8px_rgba(0,0,0,0.08)] safe-area-bottom">
    <div class="flex justify-around items-stretch px-2 py-2">
        <!-- Dashboard -->
        <a href="{{ route('dashboard') }}"
           class="flex-1 min-w-0 flex flex-col items-center justify-center p-1.5 rounded-lg transition-all duration-200 active:scale-95 {{ request()->routeIs('dashboard') ? 'text-blue-600' : 'text-slate-600' }}">
            <div class="w-9 h-9 flex items-center justify-center rounded-lg {{ request()->routeIs('dashboard') ? 'bg-blue-50' : 'hover:bg-slate-50' }}">
                <i class="fas fa-chart-line text-lg"></i>
            </div>
            <span class="mt-1 text-[10px] xs:text-xs font-medium truncate w-full text-center">Dashboard</span>
        </a>

        <!-- Transações -->
        <a href="{{ route('transactions.index') }}"
           class="flex-1 min-w-0 flex flex-col items-center justify-center p-1.5 rounded-lg transition-all duration-200 active:scale-95 {{ request()->routeIs('transactions.*') && !request()->routeIs('transactions.create') ? 'text-blue-600' : 'text-slate-600' }}">
            <div class="w-9 h-9 flex items-center justify-center rounded-lg {{ request()->routeIs('transactions.*') && !request()->routeIs('transactions.create') ? 'bg-blue-50' : 'hover:bg-slate-50' }}">
                <i class="fas fa-exchange-alt text-lg"></i>
            </div>
            <span class="mt-1 text-[10px] xs:text-xs font-medium truncate w-full text-center">Transações</span>
        </a>

        <!-- Nova Transação (Botão Central) -->
        <a href="{{ route('transactions.create') }}"
           class="relative flex-shrink-0 -top-4 mx-1 flex flex-col items-center justify-center group">
            <div class="flex h-14 w-14 items-center justify-center rounded-full bg-gradient-to-br from-blue-600 to-purple-600 shadow-lg hover:shadow-xl transition-all duration-300 group-active:scale-95">
                <i class="fas fa-plus text-2xl text-white"></i>
            </div>
            <span class="mt-1 text-[10px] xs:text-xs font-medium text-slate-700">Nova</span>
        </a>

        <!-- Contas -->
        <a href="{{ route('accounts.index') }}"
           class="flex-1 min-w-0 flex flex-col items-center justify-center p-1.5 rounded-lg transition-all duration-200 active:scale-95 {{ request()->routeIs('accounts.*') ? 'text-blue-600' : 'text-slate-600' }}">
            <div class="w-9 h-9 flex items-center justify-center rounded-lg {{ request()->routeIs('accounts.*') ? 'bg-blue-50' : 'hover:bg-slate-50' }}">
                <i class="fas fa-file-invoice-dollar text-lg"></i>
            </div>
            <span class="mt-1 text-[10px] xs:text-xs font-medium truncate w-full text-center">Contas</span>
        </a>

        <!-- Categorias -->
        <a href="{{ route('categories.index') }}"
           class="flex-1 min-w-0 flex flex-col items-center justify-center p-1.5 rounded-lg transition-all duration-200 active:scale-95 {{ request()->routeIs('categories.*') ? 'text-blue-600' : 'text-slate-600' }}">
            <div class="w-9 h-9 flex items-center justify-center rounded-lg {{ request()->routeIs('categories.*') ? 'bg-blue-50' : 'hover:bg-slate-50' }}">
                <i class="fas fa-tags text-lg"></i>
            </div>
            <span class="mt-1 text-[10px] xs:text-xs font-medium truncate w-full text-center">Categorias</span>
        </a>
    </div>
</div>

<!-- Footer (APENAS DESKTOP) -->
<footer class="hidden lg:block border-t border-slate-200 mt-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <p class="text-center text-slate-500 text-sm">
            <i class="far fa-copyright mr-1"></i>
            {{ date('Y') }} FinanceControl. Todos os direitos reservados.
        </p>
    </div>
</footer>

<script>
    // Prevenir comportamento padrão de links no mobile para melhor experiência
    document.querySelectorAll('.bottom-nav-link').forEach(link => {
        link.addEventListener('touchstart', function() {
            this.classList.add('active');
        });

        link.addEventListener('touchend', function() {
            this.classList.remove('active');
        });
    });

    // Prevenir zoom em inputs no iOS
    document.addEventListener('touchstart', function(e) {
        if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA' || e.target.tagName === 'SELECT') {
            document.body.style.zoom = "100%";
        }
    });
</script>

<style>
    /* Otimizações para mobile */
    @media (max-width: 360px) {
        .text-\[10px\] {
            font-size: 9px;
        }
        .text-lg {
            font-size: 1rem;
        }
        .h-14 {
            height: 3rem;
        }
        .w-14 {
            width: 3rem;
        }
        .text-2xl {
            font-size: 1.5rem;
        }
    }

    @media (min-width: 361px) and (max-width: 639px) {
        .text-xs {
            font-size: 0.7rem;
        }
    }

    /* Garante que os textos não quebrem linha */
    .truncate {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    /* Melhora o feedback tátil no mobile */
    .active:scale-95 {
        transform: scale(0.95);
    }

    /* Adiciona espaço seguro para iPhones com notch */
    .safe-area-bottom {
        padding-bottom: env(safe-area-inset-bottom, 0);
    }

    /* Garantir que os ícones sejam centralizados */
    .fa, .fas, .far, .fab {
        display: inline-block;
        text-align: center;
    }

    /* Tamanhos consistentes para ícones */
    .w-4 {
        width: 1rem;
    }
</style>
</body>
</html>
