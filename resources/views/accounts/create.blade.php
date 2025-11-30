@extends('layout')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="mb-8">
            <a href="{{ route('accounts.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-700 font-medium mb-4 group">
                <span class="group-hover:-translate-x-1 transition-transform">←</span>
                <span class="ml-2">Voltar para Contas</span>
            </a>
            <h2 class="text-3xl font-bold text-slate-800 flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                    <span class="text-xl">➕</span>
                </div>
                Criar Nova Conta
            </h2>
            <p class="text-slate-600 mt-2">Adicione uma nova conta para gerenciar</p>
        </div>

        <div class="card p-8">
            <form action="{{ route('accounts.store') }}" method="POST" class="space-y-6" data-validate>
                @csrf

                <!-- Título -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        Título da Conta *
                    </label>
                    <input type="text"
                           name="title"
                           required
                           class="input-primary"
                           placeholder="Ex: Aluguel, Luz, Internet..."
                           maxlength="100">
                    <p class="text-slate-500 text-xs mt-1">Máximo 100 caracteres</p>
                </div>

                <!-- Valor e Data -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            Valor *
                        </label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-500">R$</span>
                            <input type="number"
                                   step="0.01"
                                   name="amount"
                                   required
                                   min="0.01"
                                   class="input-with-icon"
                                   placeholder="0,00">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            Data de Vencimento *
                        </label>
                        <input type="date"
                               name="due_date"
                               required
                               min="{{ date('Y-m-d') }}"
                               class="input-primary">
                    </div>
                </div>

                <!-- Categoria -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        Categoria *
                    </label>
                    <select name="category_id" class="input-primary">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Observações -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        Observações (Opcional)
                    </label>
                    <textarea name="description"
                              rows="3"
                              class="input-primary"
                              placeholder="Alguma observação importante sobre esta conta..."></textarea>
                </div>

                <!-- Submit Button -->
                <div class="pt-4">
                    <button type="submit" class="btn-primary w-full py-4 flex items-center justify-center gap-2">
                        <span>💾</span>
                        <span>Criar Conta</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
