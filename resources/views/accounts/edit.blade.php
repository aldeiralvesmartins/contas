@extends('layout')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="mb-8">
            <a href="{{ route('accounts.show', $account->id) }}" class="inline-flex items-center text-blue-600 hover:text-blue-700 font-medium mb-4 group">
                <span class="group-hover:-translate-x-1 transition-transform">←</span>
                <span class="ml-2">Voltar para Detalhes</span>
            </a>
            <h2 class="text-3xl font-bold text-slate-800 flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                    <span class="text-xl">✏️</span>
                </div>
                Editar Conta
            </h2>
            <p class="text-slate-600 mt-2">Atualize as informações desta conta</p>
        </div>

        <div class="card p-8">
            <form action="{{ route('accounts.update', $account->id) }}" method="POST" class="space-y-6" data-validate>
                @csrf
                @method('PUT')

                <!-- Título -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        Título da Conta *
                    </label>
                    <input type="text"
                           name="title"
                           value="{{ old('title', $account->title) }}"
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
                                   value="{{ old('amount', $account->amount) }}"
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
                               value="{{ old('due_date', $account->due_date) }}"
                               required
                               class="input-primary">
                    </div>
                </div>

                <!-- Categoria e Status -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            Categoria *
                        </label>
                        <select name="category_id" class="input-primary">
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ $account->category_id == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            Status
                        </label>
                        <select name="status" class="input-primary">
                            <option value="pending" {{ $account->status == 'pending' ? 'selected' : '' }}>Pendente</option>
                            <option value="paid" {{ $account->status == 'paid' ? 'selected' : '' }}>Pago</option>
                        </select>
                    </div>
                </div>

                <!-- Observações -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        Observações (Opcional)
                    </label>
                    <textarea name="description"
                              rows="3"
                              class="input-primary"
                              placeholder="Alguma observação importante sobre esta conta...">{{ old('description', $account->description) }}</textarea>
                </div>

                <!-- Submit Buttons -->
                <div class="flex gap-4 pt-4">
                    <button type="submit" class="btn-primary flex-1 py-4 flex items-center justify-center gap-2">
                        <span>💾</span>
                        <span>Atualizar Conta</span>
                    </button>
                    <a href="{{ route('accounts.show', $account->id) }}" class="btn-secondary flex items-center justify-center gap-2 px-6">
                        <span>↶</span>
                        <span>Cancelar</span>
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
