@extends('layout')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="text-3xl font-bold text-slate-800 flex items-center gap-3">
                    <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                        <span class="text-xl">💸</span>
                    </div>
                    Transações
                </h2>
                <p class="text-slate-600 mt-2">Histórico de entradas e saídas</p>
            </div>
            <a href="{{ route('transactions.create') }}" class="btn-primary flex items-center gap-2">
                <span>+</span>
                <span>Nova Transação</span>
            </a>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="card p-6 text-center">
                <div class="text-2xl font-bold text-slate-800">{{ $transactions->total() }}</div>
                <p class="text-slate-600 text-sm">Total</p>
            </div>
            <div class="card p-6 text-center">
                @php
                    $total_income = $transactions->where('type', 'income')->sum('amount');
                @endphp
                <div class="text-2xl font-bold text-emerald-600">R$ {{ number_format($total_income, 2, ',', '.') }}</div>
                <p class="text-slate-600 text-sm">Entradas</p>
            </div>
            <div class="card p-6 text-center">
                @php
                    $total_expense = $transactions->where('type', 'expense')->sum('amount');
                @endphp
                <div class="text-2xl font-bold text-rose-600">R$ {{ number_format($total_expense, 2, ',', '.') }}</div>
                <p class="text-slate-600 text-sm">Saídas</p>
            </div>
            <div class="card p-6 text-center">
                @php
                    $balance = $total_income - $total_expense;
                @endphp
                <div class="text-2xl font-bold {{ $balance >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                    R$ {{ number_format($balance, 2, ',', '.') }}
                </div>
                <p class="text-slate-600 text-sm">Saldo</p>
            </div>
        </div>

        <!-- Filtros -->
        <div class="card p-6">
            <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-end">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Buscar Transações</label>
                    <input type="text" id="search-input" placeholder="Digite para filtrar..." class="input-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Tipo</label>
                    <select class="input-primary" id="type-filter">
                        <option value="">Todos</option>
                        <option value="income">Entrada</option>
                        <option value="expense">Saída</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Categoria</label>
                    <select class="input-primary" id="category-filter">
                        <option value="">Todas</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6" id="transactions-grid">
            @foreach($transactions as $transaction)
                <div class="transaction-card card p-6 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1"
                     data-type="{{ $transaction->type }}"
                     data-category="{{ $transaction->category_id }}"
                     data-description="{{ strtolower($transaction->description) }}"
                     data-notes="{{ strtolower($transaction->notes ?? '') }}">
                    <!-- Header -->
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 {{ $transaction->type === 'income' ? 'bg-emerald-100' : 'bg-rose-100' }} rounded-xl flex items-center justify-center">
                        <span class="text-xl {{ $transaction->type === 'income' ? 'text-emerald-600' : 'text-rose-600' }}">
                            {{ $transaction->type === 'income' ? '📈' : '📉' }}
                        </span>
                            </div>
                            <div>
                                <h3 class="font-semibold text-slate-800 text-lg leading-tight">{{ $transaction->description }}</h3>
                                <div class="flex items-center gap-2 mt-1">
                            <span class="badge-info text-xs">
                                {{ $transaction->category->name }}
                            </span>
                                    @if($transaction->type === 'income')
                                        <span class="badge-success text-xs">Entrada</span>
                                    @else
                                        <span class="badge-danger text-xs">Saída</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Valor -->
                    <div class="mb-4">
                        <div class="text-2xl font-bold {{ $transaction->type === 'income' ? 'text-emerald-600' : 'text-rose-600' }} mb-1">
                            {{ $transaction->type === 'income' ? '+' : '-' }}R$ {{ number_format($transaction->amount, 2, ',', '.') }}
                        </div>
                        <div class="text-sm text-slate-500">
                            {{ $transaction->created_at->format('d/m/Y \\à\\s H:i') }}
                        </div>
                    </div>

                    <!-- Observações -->
                    @if($transaction->notes)
                        <div class="mb-4 p-3 bg-slate-50 rounded-lg">
                            <p class="text-sm text-slate-600 leading-relaxed">{{ $transaction->notes }}</p>
                        </div>
                    @endif

                    <!-- Ações -->
                    <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('transactions.show', $transaction->id) }}"
                               class="p-2 text-slate-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                               data-tooltip="Visualizar">
                                👁️
                            </a>

                            <form action="{{ route('transactions.duplicate', $transaction->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit"
                                        class="p-2 text-slate-600 hover:text-purple-600 hover:bg-purple-50 rounded-lg transition-colors"
                                        data-tooltip="Duplicar Transação"
                                        data-confirm="Deseja duplicar esta transação?">
                                    ⎘
                                </button>
                            </form>
                        </div>

                        <div class="flex items-center gap-2">
                            <a href="{{ route('transactions.edit', $transaction->id) }}"
                               class="p-2 text-slate-600 hover:text-green-600 hover:bg-green-50 rounded-lg transition-colors"
                               data-tooltip="Editar">
                                ✏️
                            </a>

                            <form action="{{ route('transactions.destroy', $transaction->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="p-2 text-slate-600 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                        data-confirm="Tem certeza que deseja excluir esta transação?">
                                    🗑️
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Empty State -->
        @if($transactions->isEmpty())
            <div class="card p-12 text-center">
                <div class="w-24 h-24 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-3xl">💸</span>
                </div>
                <h3 class="text-lg font-semibold text-slate-700 mb-2">Nenhuma transação encontrada</h3>
                <p class="text-slate-500 mb-4">Registre sua primeira transação</p>
                <a href="{{ route('transactions.create') }}" class="btn-primary inline-flex items-center gap-2">
                    <span>+</span>
                    <span>Nova Transação</span>
                </a>
            </div>
        @endif

        <!-- Paginação -->
        @if($transactions->hasPages())
            <div class="card p-6">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>

    <style>
        .transaction-card {
            border-left: 4px solid transparent;
        }

        .transaction-card[data-type="income"] {
            border-left-color: #10b981;
        }

        .transaction-card[data-type="expense"] {
            border-left-color: #ef4444;
        }

        .animate-pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.5;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('search-input');
            const typeFilter = document.getElementById('type-filter');
            const categoryFilter = document.getElementById('category-filter');
            const transactionCards = document.querySelectorAll('.transaction-card');

            function applyFilters() {
                const searchValue = searchInput.value.toLowerCase();
                const typeValue = typeFilter.value;
                const categoryValue = categoryFilter.value;

                transactionCards.forEach(card => {
                    const cardType = card.getAttribute('data-type');
                    const cardCategory = card.getAttribute('data-category');
                    const cardDescription = card.getAttribute('data-description');
                    const cardNotes = card.getAttribute('data-notes');

                    const typeMatch = !typeValue || cardType === typeValue;
                    const categoryMatch = !categoryValue || cardCategory === categoryValue;
                    const searchMatch = !searchValue ||
                        cardDescription.includes(searchValue) ||
                        cardNotes.includes(searchValue);

                    card.style.display = typeMatch && categoryMatch && searchMatch ? 'block' : 'none';
                });

                // Verificar se há resultados
                const visibleCards = document.querySelectorAll('.transaction-card[style="display: block"]');
                const emptyState = document.querySelector('.empty-state');

                if (visibleCards.length === 0 && !emptyState) {
                    showEmptyState();
                } else if (emptyState && visibleCards.length > 0) {
                    emptyState.remove();
                }
            }

            function showEmptyState() {
                const grid = document.getElementById('transactions-grid');
                const emptyState = document.createElement('div');
                emptyState.className = 'empty-state col-span-full card p-12 text-center';
                emptyState.innerHTML = `
            <div class="w-24 h-24 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <span class="text-3xl">🔍</span>
            </div>
            <h3 class="text-lg font-semibold text-slate-700 mb-2">Nenhuma transação encontrada</h3>
            <p class="text-slate-500 mb-4">Tente ajustar os filtros da sua busca</p>
            <button onclick="clearFilters()" class="btn-secondary inline-flex items-center gap-2">
                <span>↶</span>
                <span>Limpar Filtros</span>
            </button>
        `;
                grid.appendChild(emptyState);
            }

            function clearFilters() {
                searchInput.value = '';
                typeFilter.value = '';
                categoryFilter.value = '';
                applyFilters();
            }

            // Event listeners
            searchInput.addEventListener('input', applyFilters);
            typeFilter.addEventListener('change', applyFilters);
            categoryFilter.addEventListener('change', applyFilters);

            // Adicionar debounce para o input de busca
            let searchTimeout;
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(applyFilters, 300);
            });

            // Inicializar tooltips
            function initTooltips() {
                const tooltipElements = document.querySelectorAll('[data-tooltip]');

                tooltipElements.forEach(element => {
                    const tooltipText = element.getAttribute('data-tooltip');
                    const tooltip = document.createElement('div');

                    tooltip.className = 'tooltip hidden absolute z-50 px-3 py-2 text-sm text-white bg-slate-900 rounded-lg shadow-lg';
                    tooltip.textContent = tooltipText;

                    element.style.position = 'relative';
                    element.appendChild(tooltip);

                    element.addEventListener('mouseenter', () => {
                        tooltip.classList.remove('hidden');
                        positionTooltip(element, tooltip);
                    });

                    element.addEventListener('mouseleave', () => {
                        tooltip.classList.add('hidden');
                    });
                });
            }

            function positionTooltip(element, tooltip) {
                const rect = element.getBoundingClientRect();
                tooltip.style.bottom = '100%';
                tooltip.style.left = '50%';
                tooltip.style.transform = 'translateX(-50%)';
                tooltip.style.marginBottom = '8px';
            }

            initTooltips();
        });

        // Adicionar função global para limpar filtros
        window.clearFilters = function() {
            document.getElementById('search-input').value = '';
            document.getElementById('type-filter').value = '';
            document.getElementById('category-filter').value = '';

            const event = new Event('input', { bubbles: true });
            document.getElementById('search-input').dispatchEvent(event);
        };
    </script>
@endsection
