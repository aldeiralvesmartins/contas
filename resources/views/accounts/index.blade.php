@extends('layout')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="text-3xl font-bold text-slate-800 flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                        <span class="text-xl">📄</span>
                    </div>
                    Contas
                </h2>
                <p class="text-slate-600 mt-2">Gerencie suas contas e pagamentos</p>
            </div>
            <a href="{{ route('accounts.create') }}" class="btn-primary flex items-center gap-2">
                <span>+</span>
                <span>Nova Conta</span>
            </a>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="card p-6 text-center">
                <div class="text-2xl font-bold text-slate-800">{{ $accounts->total() }}</div>
                <p class="text-slate-600 text-sm">Total</p>
            </div>
            <div class="card p-6 text-center">
                <div class="text-2xl font-bold text-amber-600">{{ $accounts->where('status', 'pending')->count() }}</div>
                <p class="text-slate-600 text-sm">Pendentes</p>
            </div>
            <div class="card p-6 text-center">
                <div class="text-2xl font-bold text-emerald-600">{{ $accounts->where('status', 'paid')->count() }}</div>
                <p class="text-slate-600 text-sm">Pagas</p>
            </div>
            <div class="card p-6 text-center">
                <div class="text-2xl font-bold text-red-600">{{ $accounts->where('status', 'pending')->filter(function($account) { return $account->isOverdue(); })->count() }}</div>
                <p class="text-slate-600 text-sm">Vencidas</p>
            </div>
        </div>

        <!-- Filtros -->
        <div class="card p-6">
            <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-end">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Buscar Contas</label>
                    <input type="text" id="search-input" placeholder="Digite para filtrar..." class="input-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Status</label>
                    <select class="input-primary" id="status-filter">
                        <option value="">Todos</option>
                        <option value="pending">Pendentes</option>
                        <option value="paid">Pagas</option>
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
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6" id="accounts-grid">
            @foreach($accounts as $account)
                <div class="account-card card p-6 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1"
                     data-status="{{ $account->status }}"
                     data-category="{{ $account->category_id }}"
                     data-title="{{ strtolower($account->title) }}"
                     data-description="{{ strtolower($account->description ?? '') }}"
                     data-overdue="{{ $account->isOverdue() ? 'true' : 'false' }}">

                    <!-- Header -->
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 {{ $account->status === 'pending' ? 'bg-amber-100' : 'bg-emerald-100' }} rounded-xl flex items-center justify-center">
                        <span class="text-xl {{ $account->status === 'pending' ? 'text-amber-600' : 'text-emerald-600' }}">
                            {{ $account->status === 'pending' ? '⏳' : '✅' }}
                        </span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="font-semibold text-slate-800 text-lg leading-tight truncate">{{ $account->title }}</h3>
                                <div class="flex items-center gap-2 mt-1">
                            <span class="badge-info text-xs">
                                {{ $account->category->name }}
                            </span>
                                    @if($account->status === 'pending')
                                        <span class="badge-warning text-xs">Pendente</span>
                                    @else
                                        <span class="badge-success text-xs">Pago</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Valor -->
                    <div class="mb-4">
                        <div class="text-2xl font-bold text-slate-900 mb-1">
                            R$ {{ number_format($account->amount, 2, ',', '.') }}
                        </div>
                    </div>

                    <!-- Informações de Vencimento -->
                    <div class="mb-4 p-3 bg-slate-50 rounded-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-sm font-medium text-slate-700">
                                    Vencimento: {{ \Carbon\Carbon::parse($account->due_date)->format('d/m/Y') }}
                                </div>
                                @if($account->isOverdue())
                                    <div class="text-xs text-red-600 font-medium mt-1">🔄 Vencida</div>
                                @elseif($account->status === 'pending')
                                    <div class="text-xs text-amber-600 mt-1">
                                        ⏰ {{ \Carbon\Carbon::parse($account->due_date)->diffForHumans() }}
                                    </div>
                                @else
                                    <div class="text-xs text-emerald-600 mt-1">✅ Liquidada</div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Descrição -->
                    @if($account->description)
                        <div class="mb-4 p-3 bg-blue-50 rounded-lg">
                            <p class="text-sm text-slate-600 leading-relaxed">{{ $account->description }}</p>
                        </div>
                    @endif

                    <!-- Ações -->
                    <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                        <div class="flex items-center gap-2">
                            @if($account->status === 'pending')
                                <form action="{{ route('accounts.pay', $account->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit"
                                            class="p-2 text-slate-600 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors"
                                            data-tooltip="Marcar como Paga"
                                            data-confirm="Deseja marcar esta conta como paga?">
                                        ✓
                                    </button>
                                </form>
                            @else
                                <span class="p-2 text-emerald-600" data-tooltip="Conta Paga">✔️</span>
                            @endif

                            <a href="{{ route('accounts.show', $account->id) }}"
                               class="p-2 text-slate-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                               data-tooltip="Visualizar">
                                👁️
                            </a>

                            <form action="{{ route('accounts.duplicate', $account->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit"
                                        class="p-2 text-slate-600 hover:text-purple-600 hover:bg-purple-50 rounded-lg transition-colors"
                                        data-tooltip="Duplicar Conta"
                                        data-confirm="Deseja duplicar esta conta?">
                                    ⎘
                                </button>
                            </form>
                        </div>

                        <div class="flex items-center gap-2">
                            <a href="{{ route('accounts.edit', $account->id) }}"
                               class="p-2 text-slate-600 hover:text-green-600 hover:bg-green-50 rounded-lg transition-colors"
                               data-tooltip="Editar">
                                ✏️
                            </a>

                            <form action="{{ route('accounts.destroy', $account->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="p-2 text-slate-600 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                        data-tooltip="Excluir"
                                        data-confirm="Tem certeza que deseja excluir esta conta?">
                                    🗑️
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Empty State -->
        @if($accounts->isEmpty())
            <div class="card p-12 text-center">
                <div class="w-24 h-24 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-3xl">📄</span>
                </div>
                <h3 class="text-lg font-semibold text-slate-700 mb-2">Nenhuma conta encontrada</h3>
                <p class="text-slate-500 mb-4">Comece criando sua primeira conta</p>
                <a href="{{ route('accounts.create') }}" class="btn-primary inline-flex items-center gap-2">
                    <span>+</span>
                    <span>Criar Primeira Conta</span>
                </a>
            </div>
        @endif

        <!-- Paginação -->
        @if($accounts->hasPages())
            <div class="card p-6">
                {{ $accounts->links() }}
            </div>
        @endif
    </div>

    <style>
        .account-card {
            border-left: 4px solid transparent;
            position: relative;
        }

        .account-card[data-status="pending"] {
            border-left-color: #f59e0b;
        }

        .account-card[data-status="paid"] {
            border-left-color: #10b981;
        }

        .account-card[data-overdue="true"] {
            border-left-color: #ef4444;
            animation: pulse-overdue 2s ease-in-out infinite;
        }

        @keyframes pulse-overdue {
            0%, 100% {
                box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.1);
            }
            50% {
                box-shadow: 0 0 0 8px rgba(239, 68, 68, 0);
            }
        }

        .urgent-warning {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #ef4444;
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
            animation: bounce 1s infinite;
        }

        @keyframes bounce {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-4px);
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('search-input');
            const statusFilter = document.getElementById('status-filter');
            const categoryFilter = document.getElementById('category-filter');
            const accountCards = document.querySelectorAll('.account-card');

            function applyFilters() {
                const searchValue = searchInput.value.toLowerCase();
                const statusValue = statusFilter.value;
                const categoryValue = categoryFilter.value;

                accountCards.forEach(card => {
                    const cardStatus = card.getAttribute('data-status');
                    const cardCategory = card.getAttribute('data-category');
                    const cardTitle = card.getAttribute('data-title');
                    const cardDescription = card.getAttribute('data-description');

                    const statusMatch = !statusValue || cardStatus === statusValue;
                    const categoryMatch = !categoryValue || cardCategory === categoryValue;
                    const searchMatch = !searchValue ||
                        cardTitle.includes(searchValue) ||
                        cardDescription.includes(searchValue);

                    card.style.display = statusMatch && categoryMatch && searchMatch ? 'block' : 'none';
                });

                // Verificar se há resultados
                const visibleCards = document.querySelectorAll('.account-card[style="display: block"]');
                const emptyState = document.querySelector('.empty-state');

                if (visibleCards.length === 0 && !emptyState) {
                    showEmptyState();
                } else if (emptyState && visibleCards.length > 0) {
                    emptyState.remove();
                }
            }

            function showEmptyState() {
                const grid = document.getElementById('accounts-grid');
                const emptyState = document.createElement('div');
                emptyState.className = 'empty-state col-span-full card p-12 text-center';
                emptyState.innerHTML = `
            <div class="w-24 h-24 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <span class="text-3xl">🔍</span>
            </div>
            <h3 class="text-lg font-semibold text-slate-700 mb-2">Nenhuma conta encontrada</h3>
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
                statusFilter.value = '';
                categoryFilter.value = '';
                applyFilters();
            }

            // Adicionar indicador de urgência para contas vencidas
            function addUrgencyIndicators() {
                accountCards.forEach(card => {
                    const isOverdue = card.getAttribute('data-overdue') === 'true';
                    const status = card.getAttribute('data-status');

                    if (isOverdue && status === 'pending') {
                        const urgentBadge = document.createElement('div');
                        urgentBadge.className = 'urgent-warning';
                        urgentBadge.textContent = 'URGENTE';
                        urgentBadge.setAttribute('data-tooltip', 'Conta vencida!');
                        card.style.position = 'relative';
                        card.appendChild(urgentBadge);
                    }
                });
            }

            // Event listeners
            searchInput.addEventListener('input', applyFilters);
            statusFilter.addEventListener('change', applyFilters);
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

            // Inicializar
            initTooltips();
            addUrgencyIndicators();
            applyFilters(); // Aplicar filtros iniciais se houver valores nos selects
        });

        // Adicionar função global para limpar filtros
        window.clearFilters = function() {
            document.getElementById('search-input').value = '';
            document.getElementById('status-filter').value = '';
            document.getElementById('category-filter').value = '';

            const event = new Event('input', { bubbles: true });
            document.getElementById('search-input').dispatchEvent(event);
        };
    </script>
@endsection
