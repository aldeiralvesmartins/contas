@extends('layout')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50/30 pb-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            <!-- Header Mobile Otimizado -->
            <div class="sticky top-0 z-10 bg-gradient-to-b from-slate-50/95 to-transparent backdrop-blur-sm pt-6 pb-4">
                <div class="flex flex-col gap-4">
                    <!-- Top Row -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div>
                                <h1 class="text-2xl font-bold text-slate-900">Dashboard</h1>
                                <p class="text-sm text-slate-600">{{ $selected_month_name }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Filtros e Data -->
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <!-- Filtro de Mês -->
                        <form method="GET" action="{{ route('dashboard') }}" class="flex-1 min-w-[200px]">
                            <div class="relative">
                                <select name="month"
                                        onchange="this.form.submit()"
                                        class="w-full appearance-none rounded-xl border border-slate-200 bg-white/80 py-2.5 pl-4 pr-10 text-sm text-slate-700 backdrop-blur-sm shadow-sm transition-all focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                                    @foreach($monthly_options as $option)
                                        <option value="{{ $option['value'] }}"
                                                {{ $option['is_selected'] ? 'selected' : '' }}
                                                class="py-2">
                                            {{ $option['label'] }}
                                            @if($option['is_current']) (Atual) @endif
                                        </option>
                                    @endforeach
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                    <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                            </div>
                        </form>

                        <div class="flex items-center gap-2">
                            <a href="{{ route('dashboard.export', ['month' => request('month', now()->format('Y-m'))]) }}" target="_blank"
                               class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-emerald-600 text-white text-xs font-medium hover:bg-emerald-700">
                                <span>🖨️</span>
                                <span>Exportar (PDF)</span>
                            </a>
                            @if(request()->has('month') && request('month') !== now()->format('Y-m'))
                                <a href="{{ route('dashboard') }}"
                                   class="rounded-xl bg-slate-100 px-3 py-2.5 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-200">
                                    Hoje
                                </a>
                            @endif

                            <div class="hidden rounded-xl border border-slate-200 bg-white/80 px-4 py-2.5 shadow-sm backdrop-blur-sm lg:block">
                                <div class="text-sm text-slate-600">{{ now()->translatedFormat('d \\d\\e F') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alertas Flutuantes Mobile -->
            @if($overdue_totals['count'] > 0)
                <div class="fixed bottom-20 left-4 right-4 z-50 animate-slide-up lg:hidden">
                    <div class="rounded-xl border-l-4 border-l-rose-500 bg-white p-4 shadow-lg">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-rose-500/10">
                                    <span class="text-rose-600">⚠️</span>
                                </div>
                                <div>
                                    <p class="font-medium text-slate-900">{{ $overdue_totals['count'] }} alertas</p>
                                    <p class="text-sm text-rose-600">Precisam de atenção</p>
                                </div>
                            </div>
                            <a href="{{ route('transactions.index') }}"
                               class="rounded-lg bg-rose-500 px-3 py-1.5 text-sm font-medium text-white">
                                Ver
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Main Content Grid -->
            <div class="space-y-6">
                <!-- Cards de Resumo Rápidos -->
                <div class="scrollbar-hide -mx-4 flex snap-x snap-mandatory gap-4 overflow-x-auto px-4 pb-2 lg:grid lg:grid-cols-3 lg:overflow-visible lg:px-0">
                    <!-- Saldo Total -->
                    <div class="glass-card flex-shrink-0 snap-start p-5 lg:flex-shrink">
                        <div class="flex items-start justify-between">
                            <div>
                                <div class="mb-3 flex items-center gap-2">
                                    <p class="text-sm font-medium text-slate-600">Saldo Total</p>
                                    <span class="rounded-full bg-blue-100 px-2 py-1 text-xs text-blue-600">
                                        {{ $selected_month_name }}
                                    </span>
                                </div>
                                <h3 class="mb-2 text-2xl font-bold text-slate-900">
                                    R$ {{ number_format($balance, 2, ',', '.') }}
                                </h3>
                                <div class="flex items-center gap-2">
                                    <div class="h-2 w-2 rounded-full {{ $balance >= 0 ? 'bg-emerald-400' : 'bg-rose-400' }}"></div>
                                    <span class="text-xs {{ $balance >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                        {{ $balance >= 0 ? 'Positivo' : 'Negativo' }}
                                    </span>
                                </div>
                            </div>
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500/10 to-blue-600/10">
                                <span class="text-blue-600 text-xl">💰</span>
                            </div>
                        </div>
                    </div>

                    <!-- Entradas -->
                    <div class="glass-card flex-shrink-0 snap-start p-5 lg:flex-shrink">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="mb-3 text-sm font-medium text-slate-600">Entradas</p>
                                <h3 class="mb-2 text-2xl font-bold text-emerald-600">
                                    R$ {{ number_format($income, 2, ',', '.') }}
                                </h3>
                                <div class="flex items-center gap-2">
                                    <div class="h-2 w-2 rounded-full bg-emerald-400"></div>
                                    <span class="text-xs text-slate-600">
                                        {{ $accounts_pending['count']['income'] }} pendentes
                                    </span>
                                </div>
                            </div>
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500/10 to-emerald-600/10">
                                <span class="text-emerald-600 text-xl">📈</span>
                            </div>
                        </div>
                    </div>

                    <!-- Saídas -->
                    <div class="glass-card flex-shrink-0 snap-start p-5 lg:flex-shrink">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="mb-3 text-sm font-medium text-slate-600">Saídas</p>
                                <h3 class="mb-2 text-2xl font-bold text-rose-600">
                                    R$ {{ number_format($expense, 2, ',', '.') }}
                                </h3>
                                <div class="flex items-center gap-2">
                                    <div class="h-2 w-2 rounded-full bg-rose-400"></div>
                                    <span class="text-xs text-slate-600">
                                        {{ $accounts_pending['count']['expense'] }} pendentes
                                    </span>
                                </div>
                            </div>
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-rose-500/10 to-rose-600/10">
                                <span class="text-rose-600 text-xl">📉</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Grid Principal -->
                <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                    <!-- Coluna Esquerda - Transações -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Status Overview -->
                        <div class="glass-card p-5">
                            <div class="mb-6">
                                <h2 class="text-lg font-semibold text-slate-900">Status das Transações</h2>
                                <p class="text-sm text-slate-600">Visão geral por status</p>
                            </div>

                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <!-- Entradas -->
                                <div class="space-y-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-500/10">
                                            <span class="text-emerald-600">📈</span>
                                        </div>
                                        <span class="font-medium text-slate-900">Entradas</span>
                                    </div>

                                    <div class="space-y-3">
                                        <div class="rounded-xl border border-emerald-100 bg-gradient-to-r from-emerald-50 to-white p-4">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center gap-3">
                                                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-500/20">
                                                        <span class="text-xs text-emerald-600">✓</span>
                                                    </div>
                                                    <span class="text-sm text-slate-700">Pagas</span>
                                                </div>
                                                <span class="font-semibold text-emerald-600">
                                                    R$ {{ number_format($transactions_by_status['paid']['income'], 2, ',', '.') }}
                                                </span>
                                            </div>
                                        </div>

                                        <div class="rounded-xl border border-amber-100 bg-gradient-to-r from-amber-50 to-white p-4">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center gap-3">
                                                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-500/20">
                                                        <span class="text-xs text-amber-600">⏳</span>
                                                    </div>
                                                    <span class="text-sm text-slate-700">Pendentes</span>
                                                </div>
                                                <span class="font-semibold text-amber-600">
                                                    R$ {{ number_format($transactions_by_status['pending']['income'], 2, ',', '.') }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Saídas -->
                                <div class="space-y-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-rose-500/10">
                                            <span class="text-rose-600">📉</span>
                                        </div>
                                        <span class="font-medium text-slate-900">Saídas</span>
                                    </div>

                                    <div class="space-y-3">
                                        <div class="rounded-xl border border-rose-100 bg-gradient-to-r from-rose-50 to-white p-4">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center gap-3">
                                                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-rose-500/20">
                                                        <span class="text-xs text-rose-600">✓</span>
                                                    </div>
                                                    <span class="text-sm text-slate-700">Pagas</span>
                                                </div>
                                                <span class="font-semibold text-rose-600">
                                                    R$ {{ number_format($transactions_by_status['paid']['expense'], 2, ',', '.') }}
                                                </span>
                                            </div>
                                        </div>

                                        <div class="rounded-xl border border-amber-100 bg-gradient-to-r from-amber-50 to-white p-4">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center gap-3">
                                                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-500/20">
                                                        <span class="text-xs text-amber-600">⏳</span>
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
                        </div>

                        <!-- Transações Recentes -->
                        <div class="glass-card p-5">
                            <div class="mb-6 flex items-center justify-between">
                                <div>
                                    <h2 class="text-lg font-semibold text-slate-900">Transações Recentes</h2>
                                    <p class="text-sm text-slate-600">{{ $selected_month_name }}</p>
                                </div>
                                <a href="{{ route('transactions.index') }}"
                                   class="text-sm font-medium text-blue-600 transition-colors hover:text-blue-700">
                                    Ver todas
                                </a>
                            </div>

                            @if($recent_transactions->count() > 0)
                                <div class="space-y-3">
                                    @foreach($recent_transactions as $transaction)
                                        <div class="group flex items-center justify-between rounded-xl border border-transparent p-3 transition-all hover:border-slate-200 hover:bg-slate-50/50">
                                            <div class="flex items-center gap-3">
                                                <div class="flex h-10 w-10 items-center justify-center rounded-lg {{ $transaction->type === 'income' ? 'bg-emerald-500/10' : 'bg-rose-500/10' }}">
                                                    <span class="{{ $transaction->type === 'income' ? 'text-emerald-600' : 'text-rose-600' }}">
                                                        {{ $transaction->category->getDisplayIconAttribute() }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <p class="text-sm font-medium text-slate-900">{{ $transaction->description }}</p>
                                                    <div class="mt-1 flex items-center gap-2">
                                                        <span class="text-xs text-slate-500">{{ $transaction->category->name }}</span>
                                                        <div class="h-1 w-1 rounded-full bg-slate-300"></div>
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
                                                    {{ $transaction->transaction_date ? \Carbon\Carbon::parse($transaction->transaction_date)->format('d/m') : $transaction->created_at->format('d/m') }}
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="py-8 text-center">
                                    <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-100 mx-auto">
                                        <span class="text-2xl text-slate-400">💸</span>
                                    </div>
                                    <p class="mb-2 text-sm text-slate-600">Nenhuma transação neste mês</p>
                                    <a href="{{ route('transactions.create') }}" class="text-sm font-medium text-blue-600 hover:text-blue-700">
                                        Criar primeira transação
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Coluna Direita - Sidebar -->
                    <div class="space-y-6">
                        <!-- Comparativo Mensal -->
                        @if(isset($monthly_comparison) && $monthly_comparison['current']['income'] > 0 && $monthly_comparison['current']['expense'] > 0)
                            <div class="glass-card p-5">
                                <div class="mb-4">
                                    <h3 class="font-semibold text-slate-900">Comparativo Mensal</h3>
                                    <p class="text-sm text-slate-600">vs mês anterior</p>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <!-- Entradas -->
                                    <div class="rounded-xl bg-gradient-to-br from-emerald-50 to-white p-4">
                                        <div class="mb-2 text-sm text-slate-600">Entradas</div>
                                        <div class="mb-2 text-lg font-bold text-emerald-600">
                                            R$ {{ number_format($monthly_comparison['current']['income'], 2, ',', '.') }}
                                        </div>
                                        @php
                                            $incomeTrend = $monthly_comparison['trend']['income'];
                                        @endphp
                                        <div class="flex items-center gap-1 text-sm {{ $incomeTrend >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                            @if($incomeTrend >= 0)
                                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                                </svg>
                                            @else
                                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                </svg>
                                            @endif
                                            <span>{{ number_format(abs($incomeTrend), 1) }}%</span>
                                        </div>
                                    </div>

                                    <!-- Saídas -->
                                    <div class="rounded-xl bg-gradient-to-br from-rose-50 to-white p-4">
                                        <div class="mb-2 text-sm text-slate-600">Saídas</div>
                                        <div class="mb-2 text-lg font-bold text-rose-600">
                                            R$ {{ number_format($monthly_comparison['current']['expense'], 2, ',', '.') }}
                                        </div>
                                        @php
                                            $expenseTrend = $monthly_comparison['trend']['expense'];
                                        @endphp
                                        <div class="flex items-center gap-1 text-sm {{ $expenseTrend <= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                            @if($expenseTrend <= 0)
                                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                                </svg>
                                            @else
                                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                </svg>
                                            @endif
                                            <span>{{ number_format(abs($expenseTrend), 1) }}%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Pendências -->
                        <div class="glass-card p-5">
                            <h3 class="mb-4 font-semibold text-slate-900">Pendências</h3>

                            <div class="space-y-3">
                                <!-- A Receber -->
                                <div class="rounded-xl border border-emerald-100 bg-gradient-to-r from-emerald-50 to-white p-4">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-500/10">
                                                <span class="text-emerald-600">📈</span>
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
                                </div>

                                <!-- A Pagar -->
                                <div class="rounded-xl border border-rose-100 bg-gradient-to-r from-rose-50 to-white p-4">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-rose-500/10">
                                                <span class="text-rose-600">📉</span>
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
                        </div>

                        <!-- Próximas Contas -->
                        @if($upcoming_bills->count() > 0)
                            <div class="glass-card p-5">
                                <div class="mb-4 flex items-center justify-between">
                                    <h3 class="font-semibold text-slate-900">Próximas Contas</h3>
                                    <span class="rounded-full bg-slate-100 px-2 py-1 text-xs text-slate-600">7 dias</span>
                                </div>

                                <div class="space-y-3">
                                    @foreach($upcoming_bills as $bill)
                                        <div class="flex items-center justify-between rounded-lg p-3 transition-colors hover:bg-slate-50/50">
                                            <div class="flex items-center gap-3">
                                                <div class="flex h-8 w-8 items-center justify-center rounded-lg {{ $bill->category->type === 'income' ? 'bg-emerald-500/10' : 'bg-rose-500/10' }}">
                                                    <span class="{{ $bill->category->type === 'income' ? 'text-emerald-600' : 'text-rose-600' }} text-xs">
                                                        {{ $bill->category->getDisplayIconAttribute() }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <p class="text-sm font-medium text-slate-900">{{ Str::limit($bill->title, 20) }}</p>
                                                    <p class="text-xs text-slate-500">
                                                        {{ \Carbon\Carbon::parse($bill->due_date)->diffForHumans() }}
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-sm font-semibold text-slate-900">
                                                    R$ {{ number_format($bill->amount, 2, ',', '.') }}
                                                </p>
                                                <span class="text-xs text-amber-600">Pendente</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Navigation Mobile -->

    <style>
        /* Estilos base */
        .glass-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.8);
            border-radius: 16px;
            box-shadow:
                0 4px 6px -1px rgba(0, 0, 0, 0.05),
                0 2px 4px -1px rgba(0, 0, 0, 0.03),
                inset 0 0 0 1px rgba(255, 255, 255, 0.8);
            transition: all 0.3s ease;
        }

        .glass-card:hover {
            box-shadow:
                0 8px 25px -5px rgba(0, 0, 0, 0.08),
                0 4px 10px -2px rgba(0, 0, 0, 0.04),
                inset 0 0 0 1px rgba(255, 255, 255, 0.9);
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

        .btn-mobile-primary {
            background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
            color: white;
            padding: 0.75rem;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow:
                0 4px 6px -1px rgba(59, 130, 246, 0.2),
                0 2px 4px -1px rgba(59, 130, 246, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
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

        /* Animações */
        @keyframes slide-up {
            from {
                transform: translateY(100%);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .animate-slide-up {
            animation: slide-up 0.3s ease-out;
        }

        /* Scrollbar para mobile */
        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }

        /* Snap scrolling para cards no mobile */
        .snap-x {
            scroll-snap-type: x mandatory;
        }

        .snap-start {
            scroll-snap-align: start;
        }

        /* Otimizações para mobile */
        @media (max-width: 768px) {
            .glass-card {
                border-radius: 14px;
                padding: 1rem;
            }

            .btn-modern-primary {
                padding: 0.625rem 1.25rem;
                font-size: 0.875rem;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const monthKey = 'finance_selected_month';
            const monthSelect = document.querySelector('form[action*="dashboard"] select[name="month"]');
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
        });
    </script>
@endsection
