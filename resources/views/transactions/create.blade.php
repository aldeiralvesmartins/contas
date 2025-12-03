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
        <div class="fixed bottom-6 left-6 z-50">
            <button type="button"
                    id="voice-btn"
                    class="w-16 h-16 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-full shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-300 flex items-center justify-center group">
                <div class="relative">
                    <span class="text-2xl">🎤</span>
                    <div class="absolute -top-1 -left-1 w-3 h-3 bg-red-500 rounded-full animate-pulse hidden" id="recording-indicator"></div>
                </div>
            </button>
        </div>
        <p id="voice-status" class="text-sm text-slate-500 mt-2"></p>
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
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const btn = document.getElementById('voice-btn');
            const status = document.getElementById('voice-status');

            const description = document.querySelector('input[name="description"]');
            const amount = document.querySelector('input[name="amount"]');
            const type = document.getElementById('transaction-type');
            const category = document.getElementById('category-select');
            const dateInput = document.querySelector('input[name="transaction_date"]');

            // Categorias do banco (adicionei description também)
            const categories = [
                    @foreach($categories as $c)
                { id: {{ $c->id }}, name: "{{ strtolower($c->name) }}", type: "{{ $c->type }}", description: "{{ strtolower($c->description) }}" },
                @endforeach
            ];

            if (!("webkitSpeechRecognition" in window)) {
                btn.disabled = true;
                status.textContent = "Seu navegador não suporta reconhecimento de voz.";
                return;
            }

            const recognition = new webkitSpeechRecognition();
            recognition.lang = "pt-BR";
            recognition.continuous = false;
            recognition.interimResults = false;

            btn.onclick = () => {
                recognition.start();
                status.textContent = "🎤 Ouvindo... fale a transação.";
            };

            recognition.onresult = (event) => {
                const text = event.results[0][0].transcript.toLowerCase();
                status.textContent = "Você disse: " + text;

                // ============================
                //  DEFINIR TIPO (entrada/saída)
                // ============================
                if (text.includes("entrada") || text.includes("recebi") || text.includes("ganhei")) {
                    type.value = "income";
                } else if (text.includes("saída") || text.includes("gastei") || text.includes("paguei")) {
                    type.value = "expense";
                }

                const selectedType = type.value;

                // ============================
                //  PEGAR VALOR
                // ============================
                const valor = text.match(/(\d+[,\.]?\d*)/);
                if (valor) {
                    let v = valor[1].replace(",", ".");
                    amount.value = parseFloat(v);
                }

                // ============================
                //  SELECIONAR CATEGORIA AUTOMÁTICA
                // ============================
                let possibleCategories = categories.filter(c => c.type === selectedType);
                let chosenCategory = null;

                // 1 — Match por nome ou descrição
                for (let c of possibleCategories) {
                    if (text.includes(c.name) || text.includes(c.description)) {
                        chosenCategory = c;
                        break;
                    }
                }

                // 2 — Match por palavras
                if (!chosenCategory) {
                    for (let c of possibleCategories) {
                        let words = (c.name + " " + c.description).split(" ");
                        for (let w of words) {
                            if (w.length > 3 && text.includes(w)) {
                                chosenCategory = c;
                                break;
                            }
                        }
                        if (chosenCategory) break;
                    }
                }

                // 3 — Se ainda não achou, seleciona a primeira válida do tipo
                if (!chosenCategory && possibleCategories.length > 0) {
                    chosenCategory = possibleCategories[0];
                }

                if (chosenCategory) {
                    category.value = chosenCategory.id;
                }

                // ============================
                //  DEFINIR A DATA ATUAL SE NÃO FALAR NENHUMA DATA
                // ============================
                const dataRegex = /(dia\s+\d{1,2})|(hoje)|(ontem)|(amanhã)/;

                if (dataRegex.test(text)) {
                    const now = new Date();
                    if (text.includes("hoje")) dateInput.value = now.toISOString().slice(0,16);
                    if (text.includes("ontem")) { now.setDate(now.getDate()-1); dateInput.value = now.toISOString().slice(0,16); }
                    if (text.includes("amanhã")) { now.setDate(now.getDate()+1); dateInput.value = now.toISOString().slice(0,16); }

                    const diaMatch = text.match(/dia\s+(\d{1,2})/);
                    if (diaMatch) {
                        const day = parseInt(diaMatch[1]);
                        const fixed = new Date();
                        fixed.setDate(day);
                        dateInput.value = fixed.toISOString().slice(0,16);
                    }
                } else {
                    const now = new Date();
                    dateInput.value = now.toISOString().slice(0,16);
                }

                // ============================
                //  PREENCHER DESCRIÇÃO
                // ============================
                description.value = text.charAt(0).toUpperCase() + text.slice(1);
            };

            recognition.onerror = (e) => {
                status.textContent = "Erro: " + e.error;
            };
        });
    </script>



@endsection
