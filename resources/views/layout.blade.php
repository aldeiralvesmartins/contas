<!DOCTYPE html>
<html lang="pt-br" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Finance Control' }}</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-full bg-gradient-to-br from-slate-50 to-blue-50 font-inter">
<!-- Navigation -->
<nav class="bg-white/80 backdrop-blur-md border-b border-slate-200 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Logo -->
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-gradient-to-r from-blue-600 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                    <span class="text-white font-bold text-lg">💰</span>
                </div>
                <h1 class="text-2xl font-bold bg-gradient-to-r from-slate-800 to-slate-600 bg-clip-text text-transparent">
                    FinanceControl
                </h1>
            </div>

            <!-- Navigation Links -->
            <div class="hidden md:flex items-center space-x-1">
                <a href="{{ route('dashboard') }}"
                   class="px-4 py-2 rounded-lg font-medium text-slate-600 hover:text-blue-600 hover:bg-blue-50 transition-all duration-200 flex items-center space-x-2 {{ request()->routeIs('dashboard') ? 'text-blue-600 bg-blue-50' : '' }}">
                    <span>📊</span>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('accounts.index') }}"
                   class="px-4 py-2 rounded-lg font-medium text-slate-600 hover:text-blue-600 hover:bg-blue-50 transition-all duration-200 flex items-center space-x-2 {{ request()->routeIs('accounts.*') ? 'text-blue-600 bg-blue-50' : '' }}">
                    <span>📄</span>
                    <span>Contas</span>
                </a>
                <a href="{{ route('transactions.index') }}"
                   class="px-4 py-2 rounded-lg font-medium text-slate-600 hover:text-blue-600 hover:bg-blue-50 transition-all duration-200 flex items-center space-x-2 {{ request()->routeIs('transactions.*') ? 'text-blue-600 bg-blue-50' : '' }}">
                    <span>💸</span>
                    <span>Transações</span>
                </a>
                <a href="{{ route('categories.index') }}"
                   class="px-4 py-2 rounded-lg font-medium text-slate-600 hover:text-blue-600 hover:bg-blue-50 transition-all duration-200 flex items-center space-x-2 {{ request()->routeIs('categories.*') ? 'text-blue-600 bg-blue-50' : '' }}">
                    <span>🏷️</span>
                    <span>Categorias</span>
                </a>
            </div>

            <!-- Mobile menu button -->
            <div class="md:hidden">
                <button type="button" class="text-slate-600 hover:text-slate-900" id="mobile-menu-button">
                    <span class="text-2xl">☰</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div class="md:hidden hidden bg-white border-t border-slate-200" id="mobile-menu">
        <div class="px-4 py-2 space-y-1">
            <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-lg font-medium text-slate-600 hover:text-blue-600 hover:bg-blue-50 transition-all duration-200 flex items-center space-x-2">
                <span>📊</span>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('accounts.index') }}" class="block px-3 py-2 rounded-lg font-medium text-slate-600 hover:text-blue-600 hover:bg-blue-50 transition-all duration-200 flex items-center space-x-2">
                <span>📄</span>
                <span>Contas</span>
            </a>
            <a href="{{ route('transactions.index') }}" class="block px-3 py-2 rounded-lg font-medium text-slate-600 hover:text-blue-600 hover:bg-blue-50 transition-all duration-200 flex items-center space-x-2">
                <span>💸</span>
                <span>Transações</span>
            </a>
            <a href="{{ route('categories.index') }}" class="block px-3 py-2 rounded-lg font-medium text-slate-600 hover:text-blue-600 hover:bg-blue-50 transition-all duration-200 flex items-center space-x-2">
                <span>🏷️</span>
                <span>Categorias</span>
            </a>
        </div>
    </div>
</nav>

<!-- Main Content -->
<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    @yield('content')
</main>

<!-- Footer -->
<footer class="border-t border-slate-200 mt-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <p class="text-center text-slate-500 text-sm">
            © {{ date('Y') }} FinanceControl. Todos os direitos reservados.
        </p>
    </div>
</footer>

<script>
    // Mobile menu toggle
    document.getElementById('mobile-menu-button')?.addEventListener('click', function() {
        const menu = document.getElementById('mobile-menu');
        menu.classList.toggle('hidden');
    });
</script>
</body>
</html>
