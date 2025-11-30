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
            <a href="{{ route('transactions.create') }}" class="btn-modern-primary flex items-center gap-2 group">
                <span class="group-hover:scale-110 transition-transform duration-200">+</span>
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
                    <input type="text" id="search-input" placeholder="Digite para filtrar..." class="input-modern">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Tipo</label>
                    <select class="input-modern" id="type-filter">
                        <option value="">Todos</option>
                        <option value="income">Entrada</option>
                        <option value="expense">Saída</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Categoria</label>
                    <select class="input-modern" id="category-filter">
                        <option value="">Todas</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Status</label>
                    <select class="input-modern" id="status-filter">
                        <option value="">Todos</option>
                        <option value="paid">Pago</option>
                        <option value="pending">Pendente</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6" id="transactions-grid">
            @foreach($transactions as $transaction)
                <div class="transaction-card card p-6 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 relative overflow-hidden
                    {{ $transaction->status === 'paid' ? 'ring-2 ring-emerald-200 bg-gradient-to-br from-white to-emerald-50' : '' }}"
                     data-type="{{ $transaction->type }}"
                     data-category="{{ $transaction->category_id }}"
                     data-description="{{ strtolower($transaction->description) }}"
                     data-notes="{{ strtolower($transaction->notes ?? '') }}"
                     data-status="{{ $transaction->status }}">

                    <!-- Badge de Status Pago -->
                    @if($transaction->status === 'paid')
                        <div class="absolute top-4 right-4">
                            <div class="badge-paid flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold">
                                <div class="w-2 h-2 bg-white rounded-full animate-pulse"></div>
                                Pago
                            </div>
                        </div>
                    @else
                        <div class="absolute top-4 right-4">
                            <div class="badge-pending flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold">
                                <div class="w-2 h-2 bg-amber-500 rounded-full animate-pulse"></div>
                                Pendente
                            </div>
                        </div>
                    @endif

                    <!-- Header -->
                    <div class="flex items-start justify-between mb-4 {{ $transaction->status === 'paid' ? 'pr-16' : '' }}">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 {{ $transaction->type === 'income' ? 'bg-emerald-100' : 'bg-rose-100' }} rounded-xl flex items-center justify-center shadow-sm">
                        <span class="text-xl {{ $transaction->type === 'income' ? 'text-emerald-600' : 'text-rose-600' }}">
                            {{ $transaction->type === 'income' ? '📈' : '📉' }}
                        </span>
                            </div>
                            <div>
                                <h3 class="font-semibold text-slate-800 text-lg leading-tight">{{ $transaction->description }}</h3>
                                <div class="flex items-center gap-2 mt-1">
                            <span class="badge-modern-info">
                                {{ $transaction->category->name }}
                            </span>
                                    @if($transaction->type === 'income')
                                        <span class="badge-modern-success">Entrada</span>
                                    @else
                                        <span class="badge-modern-danger">Saída</span>
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
                        <div class="mb-4 p-3 bg-slate-50 rounded-lg border border-slate-100">
                            <p class="text-sm text-slate-600 leading-relaxed">{{ $transaction->notes }}</p>
                        </div>
                    @endif

                    <!-- Ações -->
                    <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                        <div class="flex items-center gap-1">
                            <!-- Visualizar -->
                            <button onclick="window.location.href='{{ route('transactions.show', $transaction->id) }}'"
                                    class="btn-action group"
                                    data-tooltip="Visualizar">
                                <span class="group-hover:scale-110 transition-transform">👁️</span>
                            </button>

                            <!-- Duplicar -->
                            <form action="{{ route('transactions.duplicate', $transaction->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit"
                                        class="btn-action group"
                                        data-tooltip="Duplicar Transação"
                                        data-confirm="Deseja duplicar esta transação?">
                                    <span class="group-hover:scale-110 transition-transform">⎘</span>
                                </button>
                            </form>
                        </div>

                        <!-- Botão de Status (Pago/Pendente) -->
                        <div class="flex items-center gap-1">
                            @if($transaction->status === 'pending')
                                <form action="{{ route('transactions.markPaid', $transaction->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit"
                                            class="btn-action-success group"
                                            data-tooltip="Marcar como Paga">
                                        <span class="group-hover:scale-110 transition-transform">✅</span>
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('transactions.markPending', $transaction->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit"
                                            class="btn-action-warning group"
                                            data-tooltip="Marcar como Pendente">
                                        <span class="group-hover:scale-110 transition-transform">⏳</span>
                                    </button>
                                </form>
                            @endif
                        </div>

                        <div class="flex items-center gap-1">
                            <!-- Editar -->
                            <button onclick="window.location.href='{{ route('transactions.edit', $transaction->id) }}'"
                                    class="btn-action group"
                                    data-tooltip="Editar">
                                <span class="group-hover:scale-110 transition-transform">✏️</span>
                            </button>

                            <!-- Excluir -->
                            <form action="{{ route('transactions.destroy', $transaction->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="btn-action-danger group"
                                        data-confirm="Tem certeza que deseja excluir esta transação?">
                                    <span class="group-hover:scale-110 transition-transform">🗑️</span>
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
                <a href="{{ route('transactions.create') }}" class="btn-modern-primary inline-flex items-center gap-2 group">
                    <span class="group-hover:scale-110 transition-transform duration-200">+</span>
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
            transition: all 0.3s ease;
        }

        .transaction-card[data-type="income"] {
            border-left-color: #10b981;
        }

        .transaction-card[data-type="expense"] {
            border-left-color: #ef4444;
        }

        .transaction-card[data-status="paid"] {
            border-left-color: #059669;
        }

        .transaction-card[data-status="pending"] {
            border-left-color: #f59e0b;
        }

        /* Botões Modernos */
        .btn-modern-primary {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 0.75rem;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.2);
        }

        .btn-modern-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 15px -3px rgba(16, 185, 129, 0.3);
        }

        .btn-action {
            width: 2.5rem;
            height: 2.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.75rem;
            background: white;
            border: 1px solid #e2e8f0;
            color: #64748b;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .btn-action:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
            color: #475569;
            transform: translateY(-1px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .btn-action-success {
            width: 2.5rem;
            height: 2.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.75rem;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border: none;
            color: white;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .btn-action-success:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 12px -3px rgba(16, 185, 129, 0.4);
        }

        .btn-action-warning {
            width: 2.5rem;
            height: 2.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.75rem;
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            border: none;
            color: white;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .btn-action-warning:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 12px -3px rgba(245, 158, 11, 0.4);
        }

        .btn-action-danger {
            width: 2.5rem;
            height: 2.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.75rem;
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            border: none;
            color: white;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .btn-action-danger:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 12px -3px rgba(239, 68, 68, 0.4);
        }

        /* Badges Modernas */
        .badge-modern-info {
            background: #e0f2fe;
            color: #0369a1;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
            border: 1px solid #bae6fd;
        }

        .badge-modern-success {
            background: #d1fae5;
            color: #065f46;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
            border: 1px solid #a7f3d0;
        }

        .badge-modern-danger {
            background: #fee2e2;
            color: #991b1b;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
            border: 1px solid #fecaca;
        }

        .badge-paid {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            box-shadow: 0 2px 4px rgba(5, 150, 105, 0.3);
        }

        .badge-pending {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
            box-shadow: 0 2px 4px rgba(245, 158, 11, 0.3);
        }

        /* Input Moderno */
        .input-modern {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            background: white;
            transition: all 0.3s ease;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        .input-modern:focus {
            outline: none;
            border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
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

        /* Tooltip melhorado */
        .tooltip {
            font-size: 0.75rem;
            white-space: nowrap;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('search-input');
            const typeFilter = document.getElementById('type-filter');
            const categoryFilter = document.getElementById('category-filter');
            const statusFilter = document.getElementById('status-filter');
            const transactionCards = document.querySelectorAll('.transaction-card');

            function applyFilters() {
                const searchValue = searchInput.value.toLowerCase();
                const typeValue = typeFilter.value;
                const categoryValue = categoryFilter.value;
                const statusValue = statusFilter.value;

                transactionCards.forEach(card => {
                    const cardType = card.getAttribute('data-type');
                    const cardCategory = card.getAttribute('data-category');
                    const cardDescription = card.getAttribute('data-description');
                    const cardNotes = card.getAttribute('data-notes');
                    const cardStatus = card.getAttribute('data-status');

                    const typeMatch = !typeValue || cardType === typeValue;
                    const categoryMatch = !categoryValue || cardCategory === categoryValue;
                    const statusMatch = !statusValue || cardStatus === statusValue;
                    const searchMatch = !searchValue ||
                        cardDescription.includes(searchValue) ||
                        cardNotes.includes(searchValue);

                    card.style.display = typeMatch && categoryMatch && statusMatch && searchMatch ? 'block' : 'none';
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
            <button onclick="clearFilters()" class="btn-modern-primary inline-flex items-center gap-2 group">
                <span class="group-hover:scale-110 transition-transform duration-200">↶</span>
                <span>Limpar Filtros</span>
            </button>
        `;
                grid.appendChild(emptyState);
            }

            function clearFilters() {
                searchInput.value = '';
                typeFilter.value = '';
                categoryFilter.value = '';
                statusFilter.value = '';
                applyFilters();
            }

            // Event listeners
            searchInput.addEventListener('input', applyFilters);
            typeFilter.addEventListener('change', applyFilters);
            categoryFilter.addEventListener('change', applyFilters);
            statusFilter.addEventListener('change', applyFilters);

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

            // Adicionar confirmação para ações
            document.querySelectorAll('[data-confirm]').forEach(button => {
                button.addEventListener('click', function(e) {
                    if (!confirm(this.getAttribute('data-confirm'))) {
                        e.preventDefault();
                    }
                });
            });
        });

        // Adicionar função global para limpar filtros
        window.clearFilters = function() {
            document.getElementById('search-input').value = '';
            document.getElementById('type-filter').value = '';
            document.getElementById('category-filter').value = '';
            document.getElementById('status-filter').value = '';

            const event = new Event('input', { bubbles: true });
            document.getElementById('search-input').dispatchEvent(event);
        };
    </script>
@endsection
