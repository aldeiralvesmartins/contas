@extends('layout')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="mb-8">
            <a href="{{ route('accounts.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-700 font-medium mb-4 group">
                <span class="group-hover:-translate-x-1 transition-transform">←</span>
                <span class="ml-2">Voltar para Contas</span>
            </a>
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h2 class="text-3xl font-bold text-slate-800 flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                            <span class="text-xl">👁️</span>
                        </div>
                        Detalhes da Conta
                    </h2>
                    <p class="text-slate-600 mt-2">Informações completas desta conta</p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('accounts.edit', $account->id) }}" class="btn-secondary flex items-center gap-2">
                        <span>✏️</span>
                        <span>Editar</span>
                    </a>
                    <form action="{{ route('accounts.destroy', $account->id) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-xl font-semibold shadow-lg hover:bg-red-700 transition-all duration-300 flex items-center gap-2"
                                data-confirm="Tem certeza que deseja excluir esta conta?">
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
                    <h3 class="font-semibold text-slate-800 mb-4 text-lg">Informações da Conta</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Título</label>
                            <p class="text-slate-900 font-medium">{{ $account->title }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Valor</label>
                            <p class="text-2xl font-bold text-slate-900">R$ {{ number_format($account->amount, 2, ',', '.') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Data de Vencimento</label>
                            <p class="text-slate-900">{{ \Carbon\Carbon::parse($account->due_date)->format('d/m/Y') }}</p>
                            @if($account->isOverdue())
                                <span class="badge-danger text-xs mt-1">Vencida</span>
                            @elseif($account->status === 'pending')
                                <p class="text-sm text-amber-600 mt-1">
                                    Vence {{ \Carbon\Carbon::parse($account->due_date)->diffForHumans() }}
                                </p>
                            @endif
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Status</label>
                            @if($account->status === 'pending')
                                <span class="badge-warning">⏳ Pendente</span>
                            @else
                                <span class="badge-success">✅ Pago</span>
                            @endif
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Categoria</label>
                            <span class="badge-info">{{ $account->category->name }}</span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Data de Criação</label>
                            <p class="text-slate-900">{{ $account->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>

                    @if($account->description)
                        <div class="mt-6 pt-6 border-t border-slate-100">
                            <label class="block text-sm font-medium text-slate-700 mb-2">Descrição</label>
                            <p class="text-slate-700 leading-relaxed">{{ $account->description }}</p>
                        </div>
                    @endif
                </div>

                <!-- Ações Rápidas -->
                @if($account->status === 'pending')
                    <div class="card p-6">
                        <h3 class="font-semibold text-slate-800 mb-4">Ações Rápidas</h3>
                        <div class="flex flex-col sm:flex-row gap-4">
                            <form action="{{ route('accounts.pay', $account->id) }}" method="POST" class="flex-1">
                                @csrf
                                <button type="submit" class="btn-success w-full flex items-center justify-center gap-2">
                                    <span>✓</span>
                                    <span>Marcar como Paga</span>
                                </button>
                            </form>
                            <a href="{{ route('accounts.edit', $account->id) }}" class="btn-secondary flex-1 flex items-center justify-center gap-2">
                                <span>✏️</span>
                                <span>Editar Conta</span>
                            </a>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Status -->
                <div class="card p-6 text-center">
                    <div class="w-16 h-16 {{ $account->status === 'pending' ? 'bg-amber-100' : 'bg-emerald-100' }} rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <span class="text-2xl {{ $account->status === 'pending' ? 'text-amber-600' : 'text-emerald-600' }}">
                        {{ $account->status === 'pending' ? '⏳' : '✅' }}
                    </span>
                    </div>
                    <h3 class="font-semibold text-slate-800 mb-2">
                        {{ $account->status === 'pending' ? 'Pendente' : 'Paga' }}
                    </h3>
                    <p class="text-slate-600 text-sm">
                        @if($account->status === 'pending')
                            @if($account->isOverdue())
                                Conta vencida
                            @else
                                A vencer em {{ \Carbon\Carbon::parse($account->due_date)->diffForHumans() }}
                            @endif
                        @else
                            Conta liquidada
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
                            <p class="font-medium text-slate-800">{{ $account->category->name }}</p>
                            <p class="text-sm text-slate-500">
                                {{ $account->category->type === 'income' ? 'Entrada' : 'Saída' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
