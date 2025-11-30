@extends('layout')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="mb-8">
            <a href="{{ route('categories.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-700 font-medium mb-4 group">
                <span class="group-hover:-translate-x-1 transition-transform">←</span>
                <span class="ml-2">Voltar para Categorias</span>
            </a>
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h2 class="text-3xl font-bold text-slate-800 flex items-center gap-3">
                        <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center">
                            <span class="text-xl">👁️</span>
                        </div>
                        Detalhes da Categoria
                    </h2>
                    <p class="text-slate-600 mt-2">Informações completas desta categoria</p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('categories.edit', $category->id) }}" class="btn-secondary flex items-center gap-2">
                        <span>✏️</span>
                        <span>Editar</span>
                    </a>
                    @if($category->transactions_count == 0 && $category->accounts_count == 0)
                        <form action="{{ route('categories.destroy', $category->id) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-xl font-semibold shadow-lg hover:bg-red-700 transition-all duration-300 flex items-center gap-2"
                                    data-confirm="Tem certeza que deseja excluir esta categoria?">
                                <span>🗑️</span>
                                <span>Excluir</span>
                            </button>
                        </form>
                    @else
                        <button class="px-4 py-2 bg-slate-400 text-white rounded-xl font-semibold cursor-not-allowed flex items-center gap-2"
                                data-tooltip="Categoria em uso - não pode ser excluída">
                            <span>🗑️</span>
                            <span>Excluir</span>
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Informações Principais -->
            <div class="lg:col-span-2 space-y-6">
                <div class="card p-6">
                    <h3 class="font-semibold text-slate-800 mb-4 text-lg">Informações da Categoria</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Nome</label>
                            <p class="text-slate-900 font-medium text-lg">{{ $category->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Tipo</label>
                            @if($category->type === 'income')
                                <span class="badge-success text-base">Entrada</span>
                            @else
                                <span class="badge-danger text-base">Saída</span>
                            @endif
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Cor</label>
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full border-2 border-slate-200"
                                     style="background-color: {{ $category->color ?? '#6b7280' }}"></div>
                                <span class="text-slate-600">{{ $category->color ?? 'Não definida' }}</span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Ícone</label>
                            <div class="flex items-center gap-3">
                                @if($category->icon)
                                    <div class="w-8 h-8 bg-slate-100 rounded-lg flex items-center justify-center">
                                        <span class="text-slate-600">{{ $category->icon }}</span>
                                    </div>
                                    <span class="text-slate-600">{{ $category->icon }}</span>
                                @else
                                    <span class="text-slate-500">Não definido</span>
                                @endif
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Data de Criação</label>
                            <p class="text-slate-900">{{ $category->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Última Atualização</label>
                            <p class="text-slate-900">{{ $category->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>

                    @if($category->description)
                        <div class="mt-6 pt-6 border-t border-slate-100">
                            <label class="block text-sm font-medium text-slate-700 mb-2">Descrição</label>
                            <p class="text-slate-700 leading-relaxed">{{ $category->description }}</p>
                        </div>
                    @endif
                </div>

                <!-- Estatísticas de Uso -->
                <div class="card p-6">
                    <h3 class="font-semibold text-slate-800 mb-4">Estatísticas de Uso</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="text-center p-4 bg-slate-50 rounded-xl">
                            <div class="text-2xl font-bold text-blue-600">{{ $category->transactions_count ?? 0 }}</div>
                            <p class="text-slate-600 text-sm">Transações</p>
                            <p class="text-xs text-slate-500 mt-1">Total associado</p>
                        </div>
                        <div class="text-center p-4 bg-slate-50 rounded-xl">
                            <div class="text-2xl font-bold text-green-600">{{ $category->accounts_count ?? 0 }}</div>
                            <p class="text-slate-600 text-sm">Contas</p>
                            <p class="text-xs text-slate-500 mt-1">Total associado</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Resumo -->
                <div class="card p-6 text-center">
                    <div class="w-16 h-16 {{ $category->type === 'income' ? 'bg-emerald-100' : 'bg-rose-100' }} rounded-2xl flex items-center justify-center mx-auto mb-4">
                        @if($category->icon)
                            <span class="text-2xl {{ $category->type === 'income' ? 'text-emerald-600' : 'text-rose-600' }}">
                            {{ $category->icon }}
                        </span>
                        @else
                            <span class="text-2xl {{ $category->type === 'income' ? 'text-emerald-600' : 'text-rose-600' }}">
                            🏷️
                        </span>
                        @endif
                    </div>
                    <h3 class="font-semibold text-slate-800 mb-2">{{ $category->name }}</h3>
                    <p class="text-slate-600 text-sm mb-3">
                        {{ $category->type === 'income' ? 'Categoria de Entrada' : 'Categoria de Saída' }}
                    </p>
                    <div class="w-full bg-slate-200 rounded-full h-2">
                        @php
                            $totalUsage = ($category->transactions_count ?? 0) + ($category->accounts_count ?? 0);
                            $usagePercentage = min(($totalUsage / max($totalUsage, 1)) * 100, 100);
                        @endphp
                        <div class="h-2 rounded-full {{ $category->type === 'income' ? 'bg-emerald-500' : 'bg-rose-500' }}"
                             style="width: {{ $usagePercentage }}%">
                        </div>
                    </div>
                    <p class="text-xs text-slate-500 mt-2">{{ $totalUsage }} usos totais</p>
                </div>

                <!-- Ações Rápidas -->
                <div class="card p-6">
                    <h3 class="font-semibold text-slate-800 mb-4">Ações Rápidas</h3>
                    <div class="space-y-3">
                        <a href="{{ route('transactions.create') }}?category_id={{ $category->id }}"
                           class="w-full py-2 px-4 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors duration-200 flex items-center justify-center gap-2">
                            <span>💸</span>
                            <span>Nova Transação</span>
                        </a>
                        <a href="{{ route('accounts.create') }}?category_id={{ $category->id }}"
                           class="w-full py-2 px-4 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 transition-colors duration-200 flex items-center justify-center gap-2">
                            <span>📄</span>
                            <span>Nova Conta</span>
                        </a>
                    </div>
                </div>

                <!-- Informações do Tipo -->
                <div class="card p-6">
                    <h3 class="font-semibold text-slate-800 mb-4">Sobre o Tipo</h3>
                    <div class="flex items-center gap-3 p-3 {{ $category->type === 'income' ? 'bg-emerald-50' : 'bg-rose-50' }} rounded-lg">
                        <div class="w-8 h-8 {{ $category->type === 'income' ? 'bg-emerald-100' : 'bg-rose-100' }} rounded-lg flex items-center justify-center">
                        <span class="{{ $category->type === 'income' ? 'text-emerald-600' : 'text-rose-600' }}">
                            {{ $category->type === 'income' ? '📈' : '📉' }}
                        </span>
                        </div>
                        <div>
                            <p class="font-medium text-slate-800">
                                {{ $category->type === 'income' ? 'Entrada' : 'Saída' }}
                            </p>
                            <p class="text-sm text-slate-600">
                                {{ $category->type === 'income' ? 'Aumenta o saldo' : 'Reduz o saldo' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
