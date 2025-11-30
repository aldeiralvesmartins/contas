@extends('layout')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="mb-8">
            <a href="{{ route('transactions.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-700 font-medium mb-4 group">
                <span class="group-hover:-translate-x-1 transition-transform">←</span>
                <span class="ml-2">Voltar para Transações</span>
            </a>
            <h2 class="text-3xl font-bold text-slate-800 flex items-center gap-3">
                <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                    <span class="text-xl">➕</span>
                </div>
                Nova Transação
            </h2>
            <p class="text-slate-600 mt-2">Registre uma nova entrada ou saída</p>
        </div>

        <div class="card p-8">
            <form action="{{ route('transactions.store') }}" method="POST" class="space-y-6" data-validate>
                @csrf

                <!-- Descrição -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        Descrição *
                    </label>
                    <input type="text"
                           name="description"
                           required
                           class="input-primary"
                           placeholder="Ex: Salário, Compras do mês, Pagamento de conta..."
                           maxlength="200"
                           value="{{ old('description') }}">
                </div>

                <!-- Valor e Tipo -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            Valor *
                        </label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-500">R$</span>
                            <input type="number"
                                   name="amount"
                                   step="0.01"
                                   required
                                   min="0.01"
                                   class="input-with-icon"
                                   placeholder="0,00"
                                   value="{{ old('amount') }}">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            Tipo *
                        </label>
                        <select name="type" class="input-primary" id="transaction-type">
                            <option value="income" {{ old('type') == 'income' ? 'selected' : '' }}>Entrada</option>
                            <option value="expense" {{ old('type') == 'expense' ? 'selected' : '' }}>Saída</option>
                        </select>
                    </div>
                </div>

                <!-- Categoria -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        Categoria *
                    </label>
                    <select name="category_id" class="input-primary" id="category-select">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}"
                                    data-type="{{ $cat->type }}"
                                {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Data -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        Data da Transação
                    </label>
                    <input type="datetime-local"
                           name="transaction_date"
                           value="{{ old('transaction_date', now()->format('Y-m-d\TH:i')) }}"
                           class="input-primary">
                </div>

                <!-- Observações -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        Observações (Opcional)
                    </label>
                    <textarea name="notes"
                              rows="3"
                              class="input-primary"
                              placeholder="Alguma observação adicional...">{{ old('notes') }}</textarea>
                </div>

                <!-- Submit Button -->
                <div class="pt-4">
                    <button type="submit" class="btn-primary w-full py-4 flex items-center justify-center gap-2">
                        <span>💾</span>
                        <span>Registrar Transação</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const typeSelect = document.getElementById('transaction-type');
            const categorySelect = document.getElementById('category-select');

            function filterCategories() {
                const selectedType = typeSelect.value;
                const options = categorySelect.options;

                for (let i = 0; i < options.length; i++) {
                    const option = options[i];
                    const optionType = option.getAttribute('data-type');

                    if (optionType === selectedType || optionType === 'both') {
                        option.style.display = '';
                        option.disabled = false;
                    } else {
                        option.style.display = 'none';
                        option.disabled = true;
                    }
                }

                // Selecionar primeira opção válida
                for (let i = 0; i < options.length; i++) {
                    if (!options[i].disabled) {
                        categorySelect.value = options[i].value;
                        break;
                    }
                }
            }

            typeSelect.addEventListener('change', filterCategories);
            filterCategories(); // Initial filter
        });
    </script>
@endsection
