@extends('layout')

@section('content')
    <div class="space-y-8">
        <!-- Header -->
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
            <div>
                <h2 class="text-3xl font-bold text-slate-900 flex items-center gap-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg">
                        <span class="text-xl text-white">📊</span>
                    </div>
                    Dashboard Financeiro
                </h2>
                <p class="text-slate-600 mt-2">Visão geral completa das suas finanças</p>
            </div>
            <div class="flex gap-3">
                <div class="text-sm text-slate-600 bg-white/80 backdrop-blur-sm px-4 py-2 rounded-xl border border-slate-200 shadow-sm">
                    📅 {{ now()->translatedFormat('l, d \\d\\e F \\d\\e Y') }}
                </div>
                <a href="{{ route('transactions.create') }}" class="btn-primary flex items-center gap-2 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 shadow-lg shadow-green-500/25">
                    <span class="text-lg">+</span>
                    <span>Nova Transação</span>
                </a>
            </div>
        </div>

        <!-- Stats Grid Moderno -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Saldo Total -->
            <div class="bg-gradient-to-br from-blue-50 to-cyan-50 rounded-2xl p-6 shadow-sm border border-blue-100 hover:shadow-lg transition-all duration-300 group">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-600 text-sm font-semibold">Saldo Total</p>
                        <h3 class="text-2xl font-bold text-slate-900 mt-2">
                            R$ {{ number_format($balance, 2, ',', '.') }}
                        </h3>
                        <div class="flex items-center gap-1 mt-1">
                            <span class="text-xs {{ $balance >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                {{ $balance >= 0 ? '📈 Positivo' : '📉 Negativo' }}
                            </span>
                        </div>
                    </div>
                    <div class="w-12 h-12 bg-blue-500/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
                        <span class="text-xl text-blue-600">💰</span>
                    </div>
                </div>
            </div>

            <!-- Total Entradas -->
            <div class="bg-gradient-to-br from-emerald-50 to-green-50 rounded-2xl p-6 shadow-sm border border-emerald-100 hover:shadow-lg transition-all duration-300 group">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-emerald-600 text-sm font-semibold">Total Entradas</p>
                        <h3 class="text-2xl font-bold text-emerald-700 mt-2">
                            R$ {{ number_format($total_income, 2, ',', '.') }}
                        </h3>
                        <div class="flex items-center gap-1 mt-1">
                            <span class="text-xs text-slate-600">
                                {{ $pending_income_bills }} a receber
                            </span>
                        </div>
                    </div>
                    <div class="w-12 h-12 bg-emerald-500/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
                        <span class="text-xl text-emerald-600">📈</span>
                    </div>
                </div>
            </div>

            <!-- Total Saídas -->
            <div class="bg-gradient-to-br from-rose-50 to-red-50 rounded-2xl p-6 shadow-sm border border-rose-100 hover:shadow-lg transition-all duration-300 group">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-rose-600 text-sm font-semibold">Total Saídas</p>
                        <h3 class="text-2xl font-bold text-rose-700 mt-2">
                            R$ {{ number_format($total_expense, 2, ',', '.') }}
                        </h3>
                        <div class="flex items-center gap-1 mt-1">
                            <span class="text-xs text-slate-600">
                                {{ $pending_expense_bills }} a pagar
                            </span>
                        </div>
                    </div>
                    <div class="w-12 h-12 bg-rose-500/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
                        <span class="text-xl text-rose-600">📉</span>
                    </div>
                </div>
            </div>

            <!-- Contas Pendentes -->
            <div class="bg-gradient-to-br from-amber-50 to-orange-50 rounded-2xl p-6 shadow-sm border border-amber-100 hover:shadow-lg transition-all duration-300 group">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-amber-600 text-sm font-semibold">Contas Pendentes</p>
                        <h3 class="text-2xl font-bold text-amber-700 mt-2">{{ $pending_bills }}</h3>
                        <div class="flex items-center gap-1 mt-1">
                            <span class="text-xs text-rose-600">
                                {{ $late_bills }} vencidas
                            </span>
                        </div>
                    </div>
                    <div class="w-12 h-12 bg-amber-500/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
                        <span class="text-xl text-amber-600">⏳</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grid Principal -->
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
            <!-- Coluna 1 - Gráficos e Transações -->
            <div class="xl:col-span-2 space-y-8">
                <!-- Resumo de Pendências -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="font-bold text-slate-900 text-lg flex items-center gap-2">
                            <span>💎</span>
                            Resumo de Pendências
                        </h3>
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <!-- A Receber -->
                        <div class="text-center p-4 bg-emerald-50 rounded-xl border border-emerald-100">
                            <div class="text-lg font-bold text-emerald-700 mb-2">
                                R$ {{ number_format($pending_income_amount, 2, ',', '.') }}
                            </div>
                            <div class="w-full bg-emerald-200 rounded-full h-2 mb-2">
                                <div class="bg-emerald-500 h-2 rounded-full" style="width: 100%"></div>
                            </div>
                            <p class="text-sm text-emerald-700 font-medium">{{ $pending_income_bills }} contas a receber</p>
                        </div>

                        <!-- A Pagar -->
                        <div class="text-center p-4 bg-rose-50 rounded-xl border border-rose-100">
                            <div class="text-lg font-bold text-rose-700 mb-2">
                                R$ {{ number_format($pending_expense_amount, 2, ',', '.') }}
                            </div>
                            <div class="w-full bg-rose-200 rounded-full h-2 mb-2">
                                <div class="bg-rose-500 h-2 rounded-full" style="width: 100%"></div>
                            </div>
                            <p class="text-sm text-rose-700 font-medium">{{ $pending_expense_bills }} contas a pagar</p>
                        </div>
                    </div>
                </div>

                <!-- Últimas Transações -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="font-bold text-slate-900 text-lg flex items-center gap-2">
                            <span>🔄</span>
                            Últimas Transações
                        </h3>
                        <a href="{{ route('transactions.index') }}" class="text-blue-600 hover:text-blue-700 text-sm font-semibold flex items-center gap-1">
                            <span>Ver todas</span>
                            <span>→</span>
                        </a>
                    </div>

                    @if($recent_transactions->count() > 0)
                        <div class="space-y-4">
                            @foreach($recent_transactions as $transaction)
                                <div class="flex items-center justify-between p-4 hover:bg-slate-50 rounded-xl transition-all duration-200 group">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 {{ $transaction->type === 'income' ? 'bg-emerald-100' : 'bg-rose-100' }} rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
                                            <span class="{{ $transaction->type === 'income' ? 'text-emerald-600' : 'text-rose-600' }} text-sm">
                                                {{ $transaction->category->getDisplayIconAttribute() }}
                                            </span>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-slate-900 text-sm">{{ $transaction->description }}</p>
                                            <div class="flex items-center gap-2 mt-1">
                                                <span class="text-xs text-slate-500">{{ $transaction->category->name }}</span>
                                                <span class="text-xs {{ $transaction->type === 'income' ? 'text-emerald-600' : 'text-rose-600' }}">
                                                    {{ $transaction->created_at->format('d/m H:i') }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-bold {{ $transaction->type === 'income' ? 'text-emerald-600' : 'text-rose-600' }}">
                                            {{ $transaction->type === 'income' ? '+' : '-' }}R$ {{ number_format($transaction->amount, 2, ',', '.') }}
                                        </p>
                                        <span class="text-xs text-slate-500">
                                            {{ $transaction->type === 'income' ? 'Entrada' : 'Saída' }}
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
                            <a href="{{ route('transactions.create') }}" class="text-blue-600 hover:text-blue-700 text-sm font-semibold">
                                Criar primeira transação
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Coluna 2 - Contas e Categorias -->
            <div class="space-y-8">
                <!-- Contas Vencidas -->
                @if($overdue_bills->count() > 0)
                    <div class="bg-gradient-to-br from-rose-50 to-red-50 rounded-2xl p-6 shadow-sm border border-rose-100">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="font-bold text-rose-900 text-lg flex items-center gap-2">
                                <span>⚠️</span>
                                Contas Vencidas
                            </h3>
                            <span class="text-rose-700 font-bold text-lg">{{ $late_bills }}</span>
                        </div>

                        <div class="space-y-3">
                            @foreach($overdue_bills as $bill)
                                @php
                                    $isIncome = $bill->category->type === 'income';
                                @endphp
                                <div class="bg-white/80 rounded-xl p-3 border border-rose-200">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="font-semibold text-slate-900 text-sm">{{ $bill->title }}</p>
                                            <div class="flex items-center gap-2 mt-1">
                                            <span class="text-xs {{ $isIncome ? 'text-emerald-600' : 'text-rose-600' }}">
                                                {{ $isIncome ? 'A Receber' : 'A Pagar' }}
                                            </span>
                                                <span class="text-xs text-rose-600">
                                                Venceu em {{ \Carbon\Carbon::parse($bill->due_date)->format('d/m/Y') }}
                                            </span>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-bold text-rose-700">R$ {{ number_format($bill->amount, 2, ',', '.') }}</p>
                                            <span class="badge-danger text-xs">Vencida</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-4 pt-4 border-t border-rose-200/50">
                            <a href="{{ route('accounts.index') }}" class="text-rose-700 hover:text-rose-800 text-sm font-semibold flex items-center gap-1 justify-center">
                                <span>Gerenciar contas</span>
                                <span>→</span>
                            </a>
                        </div>
                    </div>
                @endif

                <!-- Próximas Contas -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-bold text-slate-900 text-lg flex items-center gap-2">
                            <span>📅</span>
                            Próximas Contas
                        </h3>
                        <span class="text-xs text-slate-500">7 dias</span>
                    </div>

                    @if($upcoming_bills->count() > 0)
                        <div class="space-y-3">
                            @foreach($upcoming_bills as $bill)
                                @php
                                    $isIncome = $bill->category->type === 'income';
                                @endphp
                                <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl hover:bg-slate-100 transition-colors group">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 {{ $isIncome ? 'bg-emerald-100' : 'bg-amber-100' }} rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
                                            <span class="{{ $isIncome ? 'text-emerald-600' : 'text-amber-600' }} text-sm">
                                                {{ $bill->category->getDisplayIconAttribute() }}
                                            </span>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-slate-900 text-sm">{{ $bill->title }}</p>
                                            <div class="flex items-center gap-2 mt-1">
                                                <span class="text-xs {{ $isIncome ? 'text-emerald-600' : 'text-amber-600' }}">
                                                    {{ $isIncome ? 'A Receber' : 'A Pagar' }}
                                                </span>
                                                <span class="text-xs text-slate-500">
                                                    {{ \Carbon\Carbon::parse($bill->due_date)->diffForHumans() }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-bold text-slate-900">R$ {{ number_format($bill->amount, 2, ',', '.') }}</p>
                                        <span class="badge-warning text-xs">Pendente</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <div class="w-12 h-12 bg-slate-100 rounded-xl flex items-center justify-center mx-auto mb-3">
                                <span class="text-xl text-slate-400">🎉</span>
                            </div>
                            <p class="text-slate-600 text-sm">Nenhuma conta próxima</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <style>
        .badge-warning {
            @apply px-2 py-1 bg-amber-100 text-amber-700 rounded-full text-xs font-medium;
        }

        .badge-danger {
            @apply px-2 py-1 bg-rose-100 text-rose-700 rounded-full text-xs font-medium;
        }
    </style>
@endsection
