@extends('layout')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50/30 pb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="pt-8 pb-6">
                <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
                    <div class="flex items-center gap-4">
                        <div class="relative">
                            <div class="w-14 h-14 bg-gradient-to-br from-blue-600 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg shadow-blue-500/25">
                                <span class="text-xl text-white">💎</span>
                            </div>
                            <div class="absolute -top-1 -right-1 w-5 h-5 bg-emerald-400 border-2 border-white rounded-full"></div>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold text-slate-900">Dashboard</h1>
                            <p class="text-slate-600 mt-1">Visão geral das suas finanças</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="bg-white/80 backdrop-blur-sm px-4 py-2.5 rounded-xl border border-slate-200 shadow-sm">
                            <div class="text-sm text-slate-600">{{ now()->translatedFormat('d \\d\\e F') }}</div>
                        </div>
                        <a href="{{ route('transactions.create') }}" class="btn-modern-primary group">
                            <span class="group-hover:scale-110 transition-transform">+</span>
                            Nova Transação
                        </a>
                    </div>
                </div>
            </div>

            <!-- Main Grid -->
            <div class="grid grid-cols-12 gap-6">
                <!-- Left Column - Main Stats -->
                <div class="col-span-12 xl:col-span-8 space-y-6">
                    <!-- Quick Stats -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Balance Card -->
                        <div class="glass-card group p-5">
                            <div class="flex items-start justify-between">
                                <div>
                                    <p class="text-slate-600 text-sm font-medium mb-2">Saldo Total</p>
                                    <h3 class="text-2xl font-bold text-slate-900 mb-1">
                                        R$ {{ number_format($balance, 2, ',', '.') }}
                                    </h3>
                                    <div class="flex items-center gap-1.5">
                                        <div class="w-2 h-2 rounded-full {{ $balance >= 0 ? 'bg-emerald-400' : 'bg-rose-400' }}"></div>
                                        <span class="text-xs {{ $balance >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                            {{ $balance >= 0 ? 'Positivo' : 'Negativo' }}
                                        </span>
                                    </div>
                                </div>
                                <div class="w-10 h-10 bg-blue-500/10 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <span class="text-blue-600 text-lg">💰</span>
                                </div>
                            </div>
                        </div>

                        <!-- Income Card -->
                        <div class="glass-card group p-5">
                            <div class="flex items-start justify-between">
                                <div>
                                    <p class="text-slate-600 text-sm font-medium mb-2">Entradas</p>
                                    <h3 class="text-2xl font-bold text-emerald-600 mb-1">
                                        R$ {{ number_format($income, 2, ',', '.') }}
                                    </h3>
                                    <div class="flex items-center gap-1.5">
                                        <div class="w-2 h-2 rounded-full bg-emerald-400"></div>
                                        <span class="text-xs text-slate-600">{{ $accounts_pending['count']['income'] }} a receber</span>
                                    </div>
                                </div>
                                <div class="w-10 h-10 bg-emerald-500/10 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <span class="text-emerald-600 text-lg">📈</span>
                                </div>
                            </div>
                        </div>

                        <!-- Expense Card -->
                        <div class="glass-card group p-5">
                            <div class="flex items-start justify-between">
                                <div>
                                    <p class="text-slate-600 text-sm font-medium mb-2">Saídas</p>
                                    <h3 class="text-2xl font-bold text-rose-600 mb-1">
                                        R$ {{ number_format($expense, 2, ',', '.') }}
                                    </h3>
                                    <div class="flex items-center gap-1.5">
                                        <div class="w-2 h-2 rounded-full bg-rose-400"></div>
                                        <span class="text-xs text-slate-600">{{ $accounts_pending['count']['expense'] }} a pagar</span>
                                    </div>
                                </div>
                                <div class="w-10 h-10 bg-rose-500/10 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <span class="text-rose-600 text-lg">📉</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Status Overview -->
                    <div class="glass-card p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-lg font-semibold text-slate-900">Status das Transações</h2>
                            <div class="flex items-center gap-2">
                                <div class="flex items-center gap-1.5">
                                    <div class="w-2 h-2 rounded-full bg-emerald-400"></div>
                                    <span class="text-xs text-slate-600">Pagas</span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <div class="w-2 h-2 rounded-full bg-amber-400"></div>
                                    <span class="text-xs text-slate-600">Pendentes</span>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Income Status -->
                            <div class="space-y-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 bg-emerald-500/10 rounded-lg flex items-center justify-center">
                                        <span class="text-emerald-600 text-sm">📈</span>
                                    </div>
                                    <span class="font-medium text-slate-900">Entradas</span>
                                </div>

                                <div class="space-y-3">
                                    <div class="flex items-center justify-between p-3 bg-emerald-50 rounded-xl border border-emerald-100">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 bg-emerald-500/20 rounded-lg flex items-center justify-center">
                                                <span class="text-emerald-600 text-xs">💰</span>
                                            </div>
                                            <span class="text-sm text-slate-700">Pagas</span>
                                        </div>
                                        <span class="font-semibold text-emerald-600">
                                            R$ {{ number_format($transactions_by_status['paid']['income'], 2, ',', '.') }}
                                        </span>
                                    </div>

                                    <div class="flex items-center justify-between p-3 bg-amber-50 rounded-xl border border-amber-100">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 bg-amber-500/20 rounded-lg flex items-center justify-center">
                                                <span class="text-amber-600 text-xs">⏳</span>
                                            </div>
                                            <span class="text-sm text-slate-700">Pendentes</span>
                                        </div>
                                        <span class="font-semibold text-amber-600">
                                            R$ {{ number_format($transactions_by_status['pending']['income'], 2, ',', '.') }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Expense Status -->
                            <div class="space-y-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 bg-rose-500/10 rounded-lg flex items-center justify-center">
                                        <span class="text-rose-600 text-sm">📉</span>
                                    </div>
                                    <span class="font-medium text-slate-900">Saídas</span>
                                </div>

                                <div class="space-y-3">
                                    <div class="flex items-center justify-between p-3 bg-rose-50 rounded-xl border border-rose-100">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 bg-rose-500/20 rounded-lg flex items-center justify-center">
                                                <span class="text-rose-600 text-xs">💰</span>
                                            </div>
                                            <span class="text-sm text-slate-700">Pagas</span>
                                        </div>
                                        <span class="font-semibold text-rose-600">
                                            R$ {{ number_format($transactions_by_status['paid']['expense'], 2, ',', '.') }}
                                        </span>
                                    </div>

                                    <div class="flex items-center justify-between p-3 bg-amber-50 rounded-xl border border-amber-100">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 bg-amber-500/20 rounded-lg flex items-center justify-center">
                                                <span class="text-amber-600 text-xs">⏳</span>
                                            </div>
                                            <span class="text-sm text-slate-700">Pendentes</span>
                                        </div>
                                        <span class="font-semibold text-amber-600">
                                            R$ {{ number_format($transactions_by_status['pending']['expense'], 2, ',', '.') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Transactions -->
                    <div class="glass-card p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-lg font-semibold text-slate-900">Transações Recentes</h2>
                            <a href="{{ route('transactions.index') }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium flex items-center gap-1 group">
                                Ver todas
                                <span class="group-hover:translate-x-0.5 transition-transform">→</span>
                            </a>
                        </div>

                        @if($recent_transactions->count() > 0)
                            <div class="space-y-3">
                                @foreach($recent_transactions as $transaction)
                                    <div class="flex items-center justify-between p-4 hover:bg-slate-50/50 rounded-xl transition-all duration-200 group border border-transparent hover:border-slate-200">
                                        <div class="flex items-center gap-4">
                                            <div class="w-10 h-10 {{ $transaction->type === 'income' ? 'bg-emerald-500/10' : 'bg-rose-500/10' }} rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                                                <span class="{{ $transaction->type === 'income' ? 'text-emerald-600' : 'text-rose-600' }} text-sm">
                                                    {{ $transaction->category->getDisplayIconAttribute() }}
                                                </span>
                                            </div>
                                            <div>
                                                <p class="font-medium text-slate-900 text-sm">{{ $transaction->description }}</p>
                                                <div class="flex items-center gap-2 mt-1">
                                                    <span class="text-xs text-slate-500">{{ $transaction->category->name }}</span>
                                                    <div class="w-1 h-1 bg-slate-300 rounded-full"></div>
                                                    <span class="text-xs {{ $transaction->status === 'paid' ? 'text-emerald-600' : 'text-amber-600' }}">
                                                        {{ $transaction->status === 'paid' ? 'Pago' : 'Pendente' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-semibold {{ $transaction->type === 'income' ? 'text-emerald-600' : 'text-rose-600' }}">
                                                {{ $transaction->type === 'income' ? '+' : '-' }}R$ {{ number_format($transaction->amount, 2, ',', '.') }}
                                            </p>
                                            <span class="text-xs text-slate-500">
                                                {{ $transaction->transaction_date ? \Carbon\Carbon::parse($transaction->transaction_date)->format('d/m/Y') : $transaction->created_at->format('d/m H:i') }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                    <span class="text-2xl text-slate-400">💸</span>
                                </div>
                                <p class="text-slate-600 text-sm mb-2">Nenhuma transação recente</p>
                                <a href="{{ route('transactions.create') }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                                    Criar primeira transação
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Right Column - Sidebar -->
                <div class="col-span-12 xl:col-span-4 space-y-6">
                    <!-- Alerts -->
                    @if($overdue_totals['count'] > 0)
                        <div class="glass-card p-6 border-l-4 border-l-rose-500">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-10 h-10 bg-rose-500/10 rounded-xl flex items-center justify-center">
                                    <span class="text-rose-600 text-lg">⚠️</span>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-slate-900">Alertas Importantes</h3>
                                    <p class="text-sm text-rose-600">{{ $overdue_totals['count'] }} itens precisam de atenção</p>
                                </div>
                            </div>

                            <div class="space-y-3">
                                @if($overdue_totals['accounts'] > 0)
                                    <div class="flex items-center justify-between p-3 bg-rose-50 rounded-lg">
                                        <span class="text-sm text-rose-700">{{ $overdue_totals['accounts'] }} contas vencidas</span>
                                        <a href="{{ route('accounts.index') }}" class="text-rose-700 hover:text-rose-800 text-sm font-medium flex items-center gap-1">
                                            Ver
                                            <span>→</span>
                                        </a>
                                    </div>
                                @endif
                                @if($overdue_totals['transactions'] > 0)
                                    <div class="flex items-center justify-between p-3 bg-rose-50 rounded-lg">
                                        <span class="text-sm text-rose-700">{{ $overdue_totals['transactions'] }} transações atrasadas</span>
                                        <a href="{{ route('transactions.index') }}" class="text-rose-700 hover:text-rose-800 text-sm font-medium flex items-center gap-1">
                                            Ver
                                            <span>→</span>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Pending Summary -->
                    <div class="glass-card p-6">
                        <h3 class="font-semibold text-slate-900 mb-4">Resumo de Pendências</h3>

                        <div class="space-y-4">
                            <!-- A Receber -->
                            <div class="flex items-center justify-between p-4 bg-emerald-50 rounded-xl border border-emerald-100">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-emerald-500/10 rounded-lg flex items-center justify-center">
                                        <span class="text-emerald-600 text-sm">📈</span>
                                    </div>
                                    <div>
                                        <p class="font-medium text-slate-900">A Receber</p>
                                        <p class="text-xs text-slate-600">{{ $accounts_pending['count']['income'] }} contas</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-emerald-600">
                                        R$ {{ number_format($accounts_pending['amount']['income'], 2, ',', '.') }}
                                    </p>
                                </div>
                            </div>

                            <!-- A Pagar -->
                            <div class="flex items-center justify-between p-4 bg-rose-50 rounded-xl border border-rose-100">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-rose-500/10 rounded-lg flex items-center justify-center">
                                        <span class="text-rose-600 text-sm">📉</span>
                                    </div>
                                    <div>
                                        <p class="font-medium text-slate-900">A Pagar</p>
                                        <p class="text-xs text-slate-600">{{ $accounts_pending['count']['expense'] }} contas</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-rose-600">
                                        R$ {{ number_format($accounts_pending['amount']['expense'], 2, ',', '.') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Upcoming Bills -->
                    <div class="glass-card p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="font-semibold text-slate-900">Próximas Contas</h3>
                            <span class="text-xs text-slate-500 bg-slate-100 px-2 py-1 rounded-full">7 dias</span>
                        </div>

                        @if($upcoming_bills->count() > 0)
                            <div class="space-y-3">
                                @foreach($upcoming_bills as $bill)
                                    <div class="flex items-center justify-between p-3 hover:bg-slate-50/50 rounded-lg transition-colors group">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 {{ $bill->category->type === 'income' ? 'bg-emerald-500/10' : 'bg-rose-500/10' }} rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                                <span class="{{ $bill->category->type === 'income' ? 'text-emerald-600' : 'text-rose-600' }} text-xs">
                                                    {{ $bill->category->getDisplayIconAttribute() }}
                                                </span>
                                            </div>
                                            <div>
                                                <p class="font-medium text-slate-900 text-sm">{{ Str::limit($bill->title, 20) }}</p>
                                                <p class="text-xs text-slate-500">
                                                    {{ \Carbon\Carbon::parse($bill->due_date)->diffForHumans() }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-semibold text-slate-900 text-sm">
                                                R$ {{ number_format($bill->amount, 2, ',', '.') }}
                                            </p>
                                            <span class="text-xs text-amber-600">Pendente</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-6">
                                <div class="w-12 h-12 bg-slate-100 rounded-xl flex items-center justify-center mx-auto mb-3">
                                    <span class="text-xl text-slate-400">🎉</span>
                                </div>
                                <p class="text-slate-600 text-sm">Nenhuma conta próxima</p>
                            </div>
                        @endif
                    </div>

                    <!-- Quick Actions -->
                    <div class="glass-card p-6">
                        <h3 class="font-semibold text-slate-900 mb-4">Ações Rápidas</h3>
                        <div class="grid grid-cols-2 gap-3">
                            <a href="{{ route('transactions.create') }}" class="quick-action-btn bg-blue-500/10 text-blue-600 hover:bg-blue-500/20">
                                <span>+</span>
                                Transação
                            </a>
                            <a href="{{ route('accounts.create') }}" class="quick-action-btn bg-emerald-500/10 text-emerald-600 hover:bg-emerald-500/20">
                                <span>💳</span>
                                Conta
                            </a>
                            <a href="{{ route('transactions.index') }}" class="quick-action-btn bg-purple-500/10 text-purple-600 hover:bg-purple-500/20">
                                <span>📊</span>
                                Relatório
                            </a>
                            <a href="{{ route('categories.index') }}" class="quick-action-btn bg-amber-500/10 text-amber-600 hover:bg-amber-500/20">
                                <span>📁</span>
                                Categorias
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .glass-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.8);
            border-radius: 16px;
            box-shadow:
                0 4px 6px -1px rgba(0, 0, 0, 0.05),
                0 2px 4px -1px rgba(0, 0, 0, 0.03),
                inset 0 0 0 1px rgba(255, 255, 255, 0.8);
        }

        .btn-modern-primary {
            background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow:
                0 4px 6px -1px rgba(59, 130, 246, 0.2),
                0 2px 4px -1px rgba(59, 130, 246, 0.1);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-modern-primary:hover {
            transform: translateY(-2px);
            box-shadow:
                0 8px 15px -3px rgba(59, 130, 246, 0.3),
                0 4px 6px -2px rgba(59, 130, 246, 0.2);
        }

        .quick-action-btn {
            padding: 0.75rem;
            border-radius: 12px;
            font-weight: 500;
            font-size: 0.875rem;
            transition: all 0.2s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.25rem;
            text-align: center;
        }

        .quick-action-btn:hover {
            transform: translateY(-1px);
        }

        /* Animações suaves */
        .glass-card {
            transition: all 0.3s ease;
        }

        .glass-card:hover {
            box-shadow:
                0 8px 25px -5px rgba(0, 0, 0, 0.08),
                0 4px 10px -2px rgba(0, 0, 0, 0.04),
                inset 0 0 0 1px rgba(255, 255, 255, 0.9);
        }
    </style>
@endsection
