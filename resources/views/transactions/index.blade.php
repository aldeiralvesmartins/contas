@extends('layout')

@section('content')
    <!-- Header Responsivo -->
    <div class="sticky top-0 z-10 bg-white/95 backdrop-blur-sm border-b border-slate-200 -mx-6 px-6 py-4 lg:py-3">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <span class="text-xl">💸</span>
                </div>
                <div>
                    <h2 class="text-lg lg:text-2xl font-bold text-slate-800">Transações</h2>
                    <p class="text-xs lg:text-sm text-slate-600">Histórico de entradas e saídas</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <!-- Data atual - Desktop -->
                <div class="hidden lg:block text-sm text-slate-600 bg-slate-50 px-4 py-2 rounded-lg">
                    {{ now()->translatedFormat('d \\d\\e F, Y') }}
                </div>

                <!-- Botão Nova Transação -->
                <a href="{{ route('transactions.create') }}"
                   class="btn-modern-primary flex items-center gap-2 group ml-auto lg:ml-0">
                    <span class="text-lg group-hover:scale-110 transition-transform duration-200">+</span>
                    <span class="hidden lg:inline">Nova Transação</span>
                    <span class="lg:hidden">Nova</span>
                </a>
            </div>
        </div>
    </div>

    <div class="space-y-6 pt-4 pb-6">
        <!-- Stats Cards Responsivo -->
        <div class="relative">
            <!-- Mobile: Scroll Horizontal -->
            <div class="lg:hidden overflow-x-auto scrollbar-hide -mx-6 px-6 pb-2">
                <div class="flex gap-4 min-w-max" id="stats-scroll">
                    <!-- Card 1: Total -->
                    <div class="bg-white rounded-xl p-4 shadow-sm border border-slate-100 min-w-[140px]">
                        <div class="text-xs text-slate-500 mb-1">Total</div>
                        <div class="text-xl font-bold text-slate-800">{{ $transactions->total() }}</div>
                        <div class="text-[10px] text-slate-400 mt-1">transações</div>
                    </div>

                    <!-- Card 2: Entradas -->
                    <div class="bg-gradient-to-br from-emerald-50 to-white rounded-xl p-4 shadow-sm border border-emerald-100 min-w-[140px]">
                        <div class="text-xs text-emerald-600 mb-1">Entradas</div>
                        <div class="text-xl font-bold text-emerald-700">R$ {{ number_format($total_income, 2, ',', '.') }}</div>
                        <div class="text-[10px] text-emerald-500 mt-1">receitas</div>
                    </div>

                    <!-- Card 3: Saídas -->
                    <div class="bg-gradient-to-br from-rose-50 to-white rounded-xl p-4 shadow-sm border border-rose-100 min-w-[140px]">
                        <div class="text-xs text-rose-600 mb-1">Saídas</div>
                        <div class="text-xl font-bold text-rose-700">R$ {{ number_format($total_expense, 2, ',', '.') }}</div>
                        <div class="text-[10px] text-rose-500 mt-1">despesas</div>
                    </div>

                    <!-- Card 4: Saldo -->
                    <div class="bg-gradient-to-br from-blue-50 to-white rounded-xl p-4 shadow-sm border border-blue-100 min-w-[140px]">
                        <div class="text-xs text-blue-600 mb-1">Saldo</div>
                        <div class="text-xl font-bold {{ $balance >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                            R$ {{ number_format($balance, 2, ',', '.') }}
                        </div>
                        <div class="text-[10px] {{ $balance >= 0 ? 'text-emerald-500' : 'text-rose-500' }} mt-1">
                            {{ $balance >= 0 ? 'positivo' : 'negativo' }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Desktop: Grid 4 Colunas -->
            <div class="hidden lg:grid lg:grid-cols-4 gap-6">
                <div class="card p-6 text-center hover:shadow-md transition-shadow">
                    <div class="text-2xl font-bold text-slate-800">{{ $transactions->total() }}</div>
                    <p class="text-slate-600 text-sm mt-1">Total de Transações</p>
                </div>
                <div class="card p-6 text-center hover:shadow-md transition-shadow bg-gradient-to-br from-emerald-50 to-white">
                    <div class="text-2xl font-bold text-emerald-600">R$ {{ number_format($total_income, 2, ',', '.') }}</div>
                    <p class="text-emerald-500 text-sm mt-1">Total de Entradas</p>
                </div>
                <div class="card p-6 text-center hover:shadow-md transition-shadow bg-gradient-to-br from-rose-50 to-white">
                    <div class="text-2xl font-bold text-rose-600">R$ {{ number_format($total_expense, 2, ',', '.') }}</div>
                    <p class="text-rose-500 text-sm mt-1">Total de Saídas</p>
                </div>
                <div class="card p-6 text-center hover:shadow-md transition-shadow bg-gradient-to-br from-blue-50 to-white">
                    <div class="text-2xl font-bold {{ $balance >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                        R$ {{ number_format($balance, 2, ',', '.') }}
                    </div>
                    <p class="text-slate-600 text-sm mt-1">Saldo Atual</p>
                </div>
            </div>

            <!-- Indicadores de Scroll - Mobile -->
            <div class="lg:hidden flex justify-center gap-1 mt-2">
                <div class="w-1.5 h-1.5 rounded-full bg-emerald-400"></div>
                <div class="w-1.5 h-1.5 rounded-full bg-slate-300"></div>
                <div class="w-1.5 h-1.5 rounded-full bg-slate-300"></div>
                <div class="w-1.5 h-1.5 rounded-full bg-slate-300"></div>
            </div>
        </div>

        <!-- Seção de Filtros e Data -->
        <div class="space-y-4">
            <!-- Toggle Filtros -->
            <div class="flex items-center justify-between">
                <button type="button" id="toggle-filters" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-slate-200 bg-white text-slate-700 text-sm font-medium hover:bg-slate-50">
                    <span>🔽</span>
                    <span>Filtros</span>
                </button>
                <a href="{{ route('transactions.export', ['month' => request('month', now()->format('Y-m'))]) }}" target="_blank"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700">
                    <span>🖨️</span>
                    <span>Exportar (PDF)</span>
                </a>
            </div>
            <!-- Linha 1: Filtro de Data + Botão Hoje -->
            <div id="filters-panel" class="flex flex-col lg:flex-row gap-4 items-start lg:items-center justify-between hidden">
                <!-- Filtro de Mês -->
                <div class="w-full lg:w-auto lg:flex-1">
                    <form method="GET" action="{{ route('transactions.index') }}" class="w-full">
                        <div class="relative">
                            <div class="absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-500">
                                📅
                            </div>
                            <select name="month"
                                    onchange="this.form.submit()"
                                    class="w-full lg:w-auto lg:max-w-xs pl-12 pr-10 py-3 rounded-xl border border-slate-200 bg-white appearance-none text-sm font-medium text-slate-700 focus:outline-none focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 hover:border-slate-300 transition-colors">
                                @foreach($monthly_options as $option)
                                    <option value="{{ $option['value'] }}"
                                        {{ $option['is_selected'] ? 'selected' : '' }}>
                                        {{ $option['label'] }}
                                        @if($option['is_current']) (Atual) @endif
                                    </option>
                                @endforeach
                            </select>
                            <div class="absolute right-4 top-1/2 transform -translate-y-1/2 text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Botão Voltar ao Mês Atual -->
                @if(request()->has('month') && request('month') !== now()->format('Y-m'))
                    <div class="w-full lg:w-auto">
                        <a href="{{ route('transactions.index') }}"
                           class="inline-flex items-center gap-2 w-full lg:w-auto justify-center bg-emerald-50 text-emerald-700 hover:bg-emerald-100 px-4 py-3 rounded-xl font-medium text-sm transition-colors active:scale-95">
                            <span>↶</span>
                            <span>Voltar ao mês atual</span>
                        </a>
                    </div>
                @endif
            </div>

            <!-- Linha 2: Filtros de Busca -->
            <div class="card p-6 hidden" id="filters-advanced">
                <div class="flex flex-col lg:flex-row gap-4 lg:items-end">
                    <!-- Busca -->
                    <div class="lg:flex-1">
                        <label class="block text-sm font-medium text-slate-700 mb-2">Buscar Transações</label>
                        <div class="relative">
                            <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-slate-500">
                                🔍
                            </div>
                            <input type="text"
                                   id="search-input"
                                   placeholder="Busque por descrição, notas..."
                                   class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100">
                        </div>
                    </div>

                    <!-- Filtros em Linha -->
                    <div class="grid grid-cols-2 lg:flex lg:flex-nowrap gap-4">
                        <!-- Tipo -->
                        <div class="lg:w-32">
                            <label class="block text-sm font-medium text-slate-700 mb-2">Tipo</label>
                            <select id="type-filter" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white text-sm appearance-none focus:outline-none focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100">
                                <option value="">Todos</option>
                                <option value="income">Entrada</option>
                                <option value="expense">Saída</option>
                            </select>
                        </div>

                        <!-- Status -->
                        <div class="lg:w-32">
                            <label class="block text-sm font-medium text-slate-700 mb-2">Status</label>
                            <select id="status-filter" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white text-sm appearance-none focus:outline-none focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100">
                                <option value="">Todos</option>
                                <option value="paid">Pago</option>
                                <option value="pending">Pendente</option>
                            </select>
                        </div>

                        <!-- Categoria -->
                        <div class="lg:w-48">
                            <label class="block text-sm font-medium text-slate-700 mb-2">Categoria</label>
                            <select id="category-filter" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white text-sm appearance-none focus:outline-none focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100">
                                <option value="">Todas categorias</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Botão Limpar Filtros -->
                        <div class="lg:w-auto flex items-end">
                            <button onclick="clearFilters()"
                                    class="w-full lg:w-auto px-4 py-3 rounded-xl border border-slate-200 bg-white text-slate-700 text-sm font-medium hover:bg-slate-50 transition-colors active:scale-95 flex items-center justify-center gap-2">
                                <span>↶</span>
                                <span class="hidden lg:inline">Limpar</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contador de Resultados -->
        <div class="flex items-center justify-between">
            <div class="text-sm lg:text-base font-medium text-slate-700" id="results-counter">
                {{ $transactions->total() }} transações encontradas
            </div>
            <div class="text-sm text-slate-500 hidden lg:block">
                {{ $transactions->firstItem() }}-{{ $transactions->lastItem() }} de {{ $transactions->total() }}
            </div>
        </div>

        <!-- Lista/Grid de Transações Responsivo -->
        <!-- Mobile: Lista -->
        <div class="lg:hidden space-y-3" id="transactions-list-mobile">
            @foreach($transactions as $transaction)
                @include('transactions.partials.mobile-card', ['transaction' => $transaction])
            @endforeach
        </div>

        <!-- Desktop: Grid -->
        <div class="hidden lg:grid lg:grid-cols-2 xl:grid-cols-3 gap-6" id="transactions-grid-desktop">
            @foreach($transactions as $transaction)
                @include('transactions.partials.desktop-card', ['transaction' => $transaction])
            @endforeach
        </div>

        <!-- Empty State -->
        @if($transactions->isEmpty())
            <div class="text-center py-12 lg:py-16">
                <div class="w-20 h-20 lg:w-24 lg:h-24 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4 lg:mb-6">
                    <span class="text-2xl lg:text-3xl">💸</span>
                </div>
                <h3 class="font-semibold text-slate-700 text-lg lg:text-xl mb-2">Nenhuma transação encontrada</h3>
                <p class="text-sm lg:text-base text-slate-500 mb-6 lg:mb-8">Registre sua primeira transação financeira</p>
                <a href="{{ route('transactions.create') }}"
                   class="btn-modern-primary inline-flex items-center gap-2 group">
                    <span class="text-lg group-hover:scale-110 transition-transform duration-200">+</span>
                    <span>Nova Transação</span>
                </a>
            </div>
        @endif

        <!-- Paginação -->
        @if($transactions->hasPages())
            <div class="pt-4 lg:pt-6">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>




    <style>
        /* Classes Responsivas */
        .btn-modern-primary {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 0.75rem;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.2);
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-modern-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 15px -3px rgba(16, 185, 129, 0.3);
        }

        .btn-modern-primary:active {
            transform: translateY(0);
        }

        .card {
            background: white;
            border-radius: 1rem;
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }

        /* Ocultar scrollbar mas manter funcionalidade */
        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }

        /* Animações */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .transaction-item {
            animation: fadeIn 0.3s ease-out;
            cursor: pointer;
        }

        .transaction-item:hover {
            border-color: #cbd5e1;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        /* Limitador de linhas */
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* Estilos para a paginação responsiva */
        .pagination {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .pagination a,
        .pagination span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 2.5rem;
            height: 2.5rem;
            padding: 0 0.75rem;
            border-radius: 0.75rem;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .pagination a {
            background-color: white;
            border: 1px solid #e2e8f0;
            color: #475569;
        }

        .pagination a:hover {
            background-color: #f8fafc;
            border-color: #cbd5e1;
        }

        .pagination .active span {
            background-color: #10b981;
            color: white;
            border-color: #10b981;
        }

        /* Responsividade para desktop */
        @media (min-width: 1024px) {
            .sticky {
                position: sticky;
            }

            .transaction-item {
                cursor: default;
            }

            .transaction-item:hover {
                transform: translateY(-2px);
            }
        }

        /* Tooltip para desktop */
        @media (min-width: 768px) {
            [data-tooltip] {
                position: relative;
            }

            [data-tooltip]:hover::after {
                content: attr(data-tooltip);
                position: absolute;
                bottom: 100%;
                left: 50%;
                transform: translateX(-50%);
                background-color: #1e293b;
                color: white;
                padding: 0.375rem 0.75rem;
                border-radius: 0.5rem;
                font-size: 0.75rem;
                white-space: nowrap;
                margin-bottom: 0.5rem;
                z-index: 50;
                pointer-events: none;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const monthKey = 'finance_selected_month';
            const monthSelect = document.querySelector('select[name="month"]');
            const url = new URL(window.location.href);
            const urlMonth = url.searchParams.get('month');
            if (urlMonth) {
                try { localStorage.setItem(monthKey, urlMonth); } catch (e) {}
            } else if (monthSelect) {
                try {
                    const saved = localStorage.getItem(monthKey);
                    if (saved && monthSelect.value !== saved) {
                        monthSelect.value = saved;
                        monthSelect.form.submit();
                        return;
                    }
                } catch (e) {}
            }
            if (monthSelect) {
                monthSelect.addEventListener('change', function() {
                    try { localStorage.setItem(monthKey, this.value); } catch (e) {}
                });
            }
            // Toggle filtros (colapsar/expandir)
            const toggleBtn = document.getElementById('toggle-filters');
            const panel = document.getElementById('filters-panel');
            const advanced = document.getElementById('filters-advanced');
            if (toggleBtn && panel && advanced) {
                toggleBtn.addEventListener('click', () => {
                    panel.classList.toggle('hidden');
                    advanced.classList.toggle('hidden');
                });
            }
            // Elementos dos filtros
            const searchInput = document.getElementById('search-input');
            const typeFilter = document.getElementById('type-filter');
            const categoryFilter = document.getElementById('category-filter');
            const statusFilter = document.getElementById('status-filter');

            // Configuração do scroll horizontal dos cards mobile
            const statsScroll = document.getElementById('stats-scroll');
            const dots = document.querySelectorAll('.lg\\:hidden.flex.justify-center.gap-1.mt-2 > div');

            if (statsScroll && dots.length > 0) {
                statsScroll.addEventListener('scroll', function() {
                    const scrollPercentage = statsScroll.scrollLeft / (statsScroll.scrollWidth - statsScroll.clientWidth);
                    const activeDot = Math.floor(scrollPercentage * dots.length);

                    dots.forEach((dot, index) => {
                        dot.classList.toggle('bg-emerald-400', index === activeDot);
                        dot.classList.toggle('bg-slate-300', index !== activeDot);
                    });
                });
            }

            // Aplicar filtros dinâmicos
            function applyFilters() {
                const searchValue = searchInput.value.toLowerCase();
                const typeValue = typeFilter.value;
                const categoryValue = categoryFilter.value;
                const statusValue = statusFilter.value;

                // Mobile
                const mobileItems = document.querySelectorAll('#transactions-list-mobile .transaction-item');
                // Desktop
                const desktopItems = document.querySelectorAll('#transactions-grid-desktop .transaction-card');

                let visibleCount = 0;

                // Filtrar itens mobile
                mobileItems.forEach(item => {
                    const cardType = item.getAttribute('data-type');
                    const cardCategory = item.getAttribute('data-category');
                    const cardDescription = item.getAttribute('data-description');
                    const cardNotes = item.getAttribute('data-notes');
                    const cardStatus = item.getAttribute('data-status');

                    const typeMatch = !typeValue || cardType === typeValue;
                    const categoryMatch = !categoryValue || cardCategory === categoryValue;
                    const statusMatch = !statusValue || cardStatus === statusValue;
                    const searchMatch = !searchValue ||
                        cardDescription.includes(searchValue) ||
                        cardNotes.includes(searchValue);

                    if (typeMatch && categoryMatch && statusMatch && searchMatch) {
                        item.style.display = 'block';
                        item.style.animation = 'fadeIn 0.3s ease-out';
                        visibleCount++;
                    } else {
                        item.style.display = 'none';
                    }
                });

                // Filtrar itens desktop
                desktopItems.forEach(item => {
                    const cardType = item.getAttribute('data-type');
                    const cardCategory = item.getAttribute('data-category');
                    const cardDescription = item.getAttribute('data-description');
                    const cardNotes = item.getAttribute('data-notes');
                    const cardStatus = item.getAttribute('data-status');

                    const typeMatch = !typeValue || cardType === typeValue;
                    const categoryMatch = !categoryValue || cardCategory === categoryValue;
                    const statusMatch = !statusValue || cardStatus === statusValue;
                    const searchMatch = !searchValue ||
                        cardDescription.includes(searchValue) ||
                        cardNotes.includes(searchValue);

                    if (typeMatch && categoryMatch && statusMatch && searchMatch) {
                        item.style.display = 'block';
                        item.style.animation = 'fadeIn 0.3s ease-out';
                    } else {
                        item.style.display = 'none';
                    }
                });

                // Atualizar contador
                const counter = document.getElementById('results-counter');
                if (counter) {
                    counter.textContent = `${visibleCount} transações encontradas`;
                }

                // Mostrar/ocultar empty state para filtros
                const existingEmptyState = document.getElementById('filter-empty-state');
                if (visibleCount === 0 && mobileItems.length > 0 && !existingEmptyState) {
                    showFilterEmptyState();
                } else if (existingEmptyState && visibleCount > 0) {
                    existingEmptyState.remove();
                }
            }

            function showFilterEmptyState() {
                const container = document.querySelector('.space-y-6');
                const emptyState = document.createElement('div');
                emptyState.id = 'filter-empty-state';
                emptyState.className = 'text-center py-12 card';
                emptyState.innerHTML = `
                    <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-2xl">🔍</span>
                    </div>
                    <h3 class="font-semibold text-slate-700 text-lg mb-2">Nenhum resultado encontrado</h3>
                    <p class="text-sm text-slate-500 mb-6">Tente ajustar os filtros da sua busca</p>
                    <button onclick="clearFilters()"
                            class="inline-flex items-center gap-2 bg-slate-100 text-slate-700 px-6 py-3 rounded-xl font-medium hover:bg-slate-200 transition-colors active:scale-95">
                        <span class="text-lg">↶</span>
                        <span>Limpar todos os filtros</span>
                    </button>
                `;

                // Inserir após a lista de transações
                const transactionsList = document.getElementById('transactions-list-mobile') ||
                    document.getElementById('transactions-grid-desktop');
                if (transactionsList) {
                    transactionsList.parentNode.insertBefore(emptyState, transactionsList.nextSibling);
                }
            }

            // Função para limpar filtros
            window.clearFilters = function() {
                searchInput.value = '';
                typeFilter.value = '';
                categoryFilter.value = '';
                statusFilter.value = '';
                applyFilters();

                // Foco no campo de busca
                searchInput.focus();
            };

            // Event listeners para filtros
            searchInput.addEventListener('input', function() {
                clearTimeout(this.timer);
                this.timer = setTimeout(applyFilters, 300);
            });

            typeFilter.addEventListener('change', applyFilters);
            categoryFilter.addEventListener('change', applyFilters);
            statusFilter.addEventListener('change', applyFilters);

            // Inicializar contador se houver filtros ativos
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('search') || urlParams.has('type') || urlParams.has('category') || urlParams.has('status')) {
                applyFilters();
            }
        });

        // Prevenir envio de formulário duplo
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function(e) {
                const submitBtn = this.querySelector('button[type="submit"]');
                if (submitBtn && !this.classList.contains('prevented')) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = 'Processando...';
                    this.classList.add('prevented');
                }
            });
        });
    </script>
@endsection
