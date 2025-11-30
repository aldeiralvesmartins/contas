@extends('layout')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="mb-8">
            <a href="{{ route('transactions.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-700 font-medium mb-4 group">
                <span class="group-hover:-translate-x-1 transition-transform">←</span>
                <span class="ml-2">Voltar para Transações</span>
            </a>
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h2 class="text-3xl font-bold text-slate-800 flex items-center gap-3">
                        <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                            <span class="text-xl">👁️</span>
                        </div>
                        Detalhes da Transação
                    </h2>
                    <p class="text-slate-600 mt-2">Informações completas desta transação</p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('transactions.edit', $transaction->id) }}" class="btn-secondary flex items-center gap-2">
                        <span>✏️</span>
                        <span>Editar</span>
                    </a>
                    <form action="{{ route('transactions.destroy', $transaction->id) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-xl font-semibold shadow-lg hover:bg-red-700 transition-all duration-300 flex items-center gap-2"
                                data-confirm="Tem certeza que deseja excluir esta transação?">
                            <span>🗑️</span>
                            <span>Excluir</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Informações Principais -->
            <div class="lg:col-span-2 space-y-6">
                <div class="card p-6">
                    <h3 class="font-semibold text-slate-800 mb-4 text-lg">Informações da Transação</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Descrição</label>
                            <p class="text-slate-900 font-medium">{{ $transaction->description }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Valor</label>
                            <p class="text-2xl font-bold {{ $transaction->type === 'income' ? 'text-emerald-600' : 'text-rose-600' }}">
                                {{ $transaction->type === 'income' ? '+' : '-' }}R$ {{ number_format($transaction->amount, 2, ',', '.') }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Tipo</label>
                            @if($transaction->type === 'income')
                                <span class="badge-success">Entrada</span>
                            @else
                                <span class="badge-danger">Saída</span>
                            @endif
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Categoria</label>
                            <span class="badge-info">{{ $transaction->category->name }}</span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Data da Transação</label>
                            <p class="text-slate-900">{{ $transaction->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Data de Criação</label>
                            <p class="text-slate-900">{{ $transaction->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>

                    @if($transaction->notes)
                        <div class="mt-6 pt-6 border-t border-slate-100">
                            <label class="block text-sm font-medium text-slate-700 mb-2">Observações</label>
                            <p class="text-slate-700 leading-relaxed">{{ $transaction->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Tipo -->
                <div class="card p-6 text-center">
                    <div class="w-16 h-16 {{ $transaction->type === 'income' ? 'bg-emerald-100' : 'bg-rose-100' }} rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <span class="text-2xl {{ $transaction->type === 'income' ? 'text-emerald-600' : 'text-rose-600' }}">
                        {{ $transaction->type === 'income' ? '📈' : '📉' }}
                    </span>
                    </div>
                    <h3 class="font-semibold text-slate-800 mb-2">
                        {{ $transaction->type === 'income' ? 'Entrada' : 'Saída' }}
                    </h3>
                    <p class="text-slate-600 text-sm">
                        @if($transaction->type === 'income')
                            Valor adicionado ao seu saldo
                        @else
                            Valor subtraído do seu saldo
                        @endif
                    </p>
                </div>

                <!-- Informações da Categoria -->
                <div class="card p-6">
                    <h3 class="font-semibold text-slate-800 mb-4">Categoria</h3>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                            <span class="text-blue-600">🏷️</span>
                        </div>
                        <div>
                            <p class="font-medium text-slate-800">{{ $transaction->category->name }}</p>
                            <p class="text-sm text-slate-500">
                                {{ $transaction->category->type === 'income' ? 'Entrada' : 'Saída' }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Impacto no Saldo -->
                <div class="card p-6">
                    <h3 class="font-semibold text-slate-800 mb-4">Impacto no Saldo</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-slate-600">Valor da transação:</span>
                            <span class="font-semibold {{ $transaction->type === 'income' ? 'text-emerald-600' : 'text-rose-600' }}">
                            {{ $transaction->type === 'income' ? '+' : '-' }}R$ {{ number_format($transaction->amount, 2, ',', '.') }}
                        </span>
                        </div>
                        <div class="pt-3 border-t border-slate-100">
                            <p class="text-sm text-slate-600 text-center">
                                @if($transaction->type === 'income')
                                    🎉 Esta transação aumentou seu saldo
                                @else
                                    💡 Esta transação reduziu seu saldo
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
