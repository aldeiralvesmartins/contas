@extends('layout')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50 py-8 px-4">
        <div class="max-w-2xl mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <a href="{{ route('transactions.index') }}"
                   class="inline-flex items-center text-blue-600 hover:text-blue-700 font-medium mb-6 group transition-all duration-300">
                    <span class="inline-flex items-center justify-center w-8 h-8 bg-white rounded-lg shadow-sm group-hover:shadow-md group-hover:-translate-x-1 transition-all duration-300 mr-2">
                        <span class="text-lg">←</span>
                    </span>
                    <span>Voltar para Transações</span>
                </a>

                <div class="flex items-start gap-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl flex items-center justify-center shadow-lg">
                        <span class="text-2xl text-white">💸</span>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-slate-800 mb-2">Nova Transação</h1>
                        <p class="text-slate-600">Registre uma nova entrada ou saída de forma rápida</p>
                    </div>
                </div>
            </div>

            <!-- Voice Assistant -->
            <div class="fixed bottom-8 right-8 z-50 flex flex-col items-end">
                <div id="voice-panel" class="mb-4 bg-white rounded-2xl shadow-xl p-4 max-w-xs hidden transform transition-all duration-300">
                    <div class="flex items-center justify-between mb-2">
                        <span class="font-medium text-slate-700">Assistente de Voz</span>
                        <button id="close-voice-panel" class="text-slate-400 hover:text-slate-600">
                            ✕
                        </button>
                    </div>
                    <p id="voice-status" class="text-sm text-slate-600">Clique no microfone para começar</p>
                </div>

                <button type="button"
                        id="voice-btn"
                        class="w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-600 text-white rounded-full shadow-xl hover:shadow-2xl hover:scale-110 active:scale-95 transition-all duration-300 flex items-center justify-center group relative">
                    <div class="relative">
                        <span class="text-2xl transition-transform group-hover:scale-110">🎤</span>
                        <div class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 rounded-full animate-ping hidden" id="recording-indicator"></div>
                    </div>
                    <div class="absolute -top-2 -right-2 w-6 h-6 bg-gradient-to-r from-green-400 to-blue-500 rounded-full flex items-center justify-center shadow-lg">
                        <span class="text-xs">AI</span>
                    </div>
                </button>
            </div>

            <!-- Form Card -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden mb-8">
                <!-- Form Header -->
                <div class="px-8 pt-8 pb-6 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="w-3 h-3 rounded-full bg-gradient-to-r from-green-400 to-blue-500"></div>
                        <h2 class="text-xl font-semibold text-slate-800">Detalhes da Transação</h2>
                    </div>
                </div>

                <!-- Form Content -->
                <form action="{{ route('transactions.store') }}" method="POST" class="p-8 space-y-8" data-validate>
                    @csrf

                    <!-- Description -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-3 flex items-center justify-between">
                            <span>Descrição</span>
                            <span class="text-xs font-normal text-slate-500">* Obrigatório</span>
                        </label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <span class="text-slate-400">📝</span>
                            </div>
                            <input type="text"
                                   name="description"
                                   required
                                   class="w-full pl-12 pr-4 py-3.5 bg-slate-50 border-2 border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all duration-300 placeholder:text-slate-400"
                                   placeholder="Ex: Salário, Compras do mês, Conta de luz..."
                                   maxlength="200"
                                   value="{{ old('description') }}">
                            <div class="absolute right-3 top-1/2 transform -translate-y-1/2 text-sm text-slate-400">
                                {{ old('description') ? strlen(old('description')) : 0 }}/200
                            </div>
                        </div>
                    </div>

                    <!-- Amount & Type -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-3">
                                Valor
                            </label>
                            <div class="relative group">
                                <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-700 font-medium">R$</span>
                                <input type="number"
                                       name="amount"
                                       step="0.01"
                                       required
                                       min="0.01"
                                       class="w-full pl-12 pr-4 py-3.5 bg-slate-50 border-2 border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all duration-300 placeholder:text-slate-400"
                                       placeholder="0,00"
                                       value="{{ old('amount') }}">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-3">
                                Tipo
                            </label>
                            <div class="relative">
                                <select name="type"
                                        class="w-full px-4 py-3.5 bg-slate-50 border-2 border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all duration-300 appearance-none cursor-pointer"
                                        id="transaction-type">
                                    <option value="income" {{ old('type') == 'income' ? 'selected' : '' }}>💰 Entrada</option>
                                    <option value="expense" {{ old('type') == 'expense' ? 'selected' : '' }}>💳 Saída</option>
                                </select>
                                <div class="absolute inset-y-0 right-4 flex items-center pointer-events-none">
                                    <span class="text-slate-400">▼</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Category -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-3">
                            Categoria
                        </label>
                        <div class="relative">
                            <select name="category_id"
                                    class="w-full px-4 py-3.5 bg-slate-50 border-2 border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all duration-300 appearance-none cursor-pointer"
                                    id="category-select">
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}"
                                            data-type="{{ $cat->type }}"
                                            class="py-2"
                                        {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                        @if($cat->type == 'income') 💰
                                        @elseif($cat->type == 'expense') 💳
                                        @else 📁
                                        @endif
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-4 flex items-center pointer-events-none">
                                <span class="text-slate-400">▼</span>
                            </div>
                        </div>
                    </div>

                    <!-- Date -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-3">
                            Data da Transação
                        </label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <span class="text-slate-400">📅</span>
                            </div>
                            <input type="datetime-local"
                                   name="transaction_date"
                                   class="w-full pl-12 pr-4 py-3.5 bg-slate-50 border-2 border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all duration-300 cursor-pointer">
                        </div>
                    </div>

                    <!-- Notes -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-3">
                            Observações <span class="text-slate-400 font-normal">(Opcional)</span>
                        </label>
                        <div class="relative group">
                            <div class="absolute left-4 top-4 pointer-events-none">
                                <span class="text-slate-400">✏️</span>
                            </div>
                            <textarea name="notes"
                                      rows="3"
                                      class="w-full pl-12 pr-4 py-3.5 bg-slate-50 border-2 border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all duration-300 resize-none placeholder:text-slate-400"
                                      placeholder="Alguma observação adicional sobre esta transação...">{{ old('notes') }}</textarea>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-4">
                        <button type="submit"
                                class="w-full py-4 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-medium rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-300 flex items-center justify-center gap-3">
                            <span class="text-xl">💾</span>
                            <span class="text-lg">Salvar Transação</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Quick Tips -->
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl p-6 border border-blue-100">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center shadow-sm">
                        <span class="text-blue-600">💡</span>
                    </div>
                    <div>
                        <h3 class="font-medium text-slate-800 mb-2">Dica rápida</h3>
                        <p class="text-sm text-slate-600">
                            Use o botão do microfone para registrar transações por voz. Diga algo como
                            <span class="font-medium text-blue-600">"Recebi R$ 500 de salário"</span> ou
                            <span class="font-medium text-blue-600">"Gastei R$ 89,90 no mercado"</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Category Filtering
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

                // Select first valid option
                for (let i = 0; i < options.length; i++) {
                    if (!options[i].disabled) {
                        categorySelect.value = options[i].value;
                        break;
                    }
                }
            }

            typeSelect.addEventListener('change', filterCategories);
            filterCategories();

            // Voice Assistant
            const voiceBtn = document.getElementById('voice-btn');
            const voicePanel = document.getElementById('voice-panel');
            const voiceStatus = document.getElementById('voice-status');
            const closeVoicePanel = document.getElementById('close-voice-panel');
            const recordingIndicator = document.getElementById('recording-indicator');

            const descriptionInput = document.querySelector('input[name="description"]');
            const amountInput = document.querySelector('input[name="amount"]');
            const dateInput = document.querySelector('input[name="transaction_date"]');

            const categories = [
                    @foreach($categories as $c)
                {
                    id: {{ $c->id }},
                    name: "{{ strtolower($c->name) }}",
                    type: "{{ $c->type }}",
                    description: "{{ strtolower($c->description) }}",
                    emoji: "{{ $c->type == 'income' ? '💰' : ($c->type == 'expense' ? '💳' : '📁') }}"
                },
                @endforeach
            ];

            // Set default date to now
            const now = new Date();
            now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
            dateInput.value = now.toISOString().slice(0, 16);

            // Voice Recognition
            if (!("webkitSpeechRecognition" in window)) {
                voiceBtn.disabled = true;
                voiceBtn.classList.add('opacity-50', 'cursor-not-allowed');
                voiceStatus.textContent = "❌ Navegador não suporta reconhecimento de voz";
                voicePanel.classList.remove('hidden');
                return;
            }

            const recognition = new webkitSpeechRecognition();
            recognition.lang = "pt-BR";
            recognition.continuous = false;
            recognition.interimResults = true;

            let isRecording = false;

            voiceBtn.addEventListener('click', toggleRecording);
            closeVoicePanel.addEventListener('click', () => {
                voicePanel.classList.add('hidden');
            });

            function toggleRecording() {
                if (isRecording) {
                    recognition.stop();
                    stopRecording();
                } else {
                    recognition.start();
                    startRecording();
                }
            }

            function startRecording() {
                isRecording = true;
                recordingIndicator.classList.remove('hidden');
                voiceBtn.classList.remove('from-blue-500', 'to-purple-600');
                voiceBtn.classList.add('from-red-500', 'to-pink-600');
                voiceStatus.textContent = "🎤 Gravando... fale agora!";
                voicePanel.classList.remove('hidden');
            }

            function stopRecording() {
                isRecording = false;
                recordingIndicator.classList.add('hidden');
                voiceBtn.classList.remove('from-red-500', 'to-pink-600');
                voiceBtn.classList.add('from-blue-500', 'to-purple-600');
            }

            recognition.onstart = () => startRecording();
            recognition.onend = () => stopRecording();

            recognition.onresult = (event) => {
                const text = event.results[0][0].transcript.toLowerCase();
                voiceStatus.textContent = `📝 Reconhecido: "${text}"`;

                // Process voice input
                processVoiceCommand(text);
            };

            recognition.onerror = (event) => {
                voiceStatus.textContent = `❌ Erro: ${event.error}`;
                stopRecording();
            };

            function processVoiceCommand(text) {
                // Type detection
                if (text.includes("entrada") || text.includes("recebi") || text.includes("ganhei") || text.includes("salário")) {
                    typeSelect.value = "income";
                    typeSelect.dispatchEvent(new Event('change'));
                } else if (text.includes("saída") || text.includes("gastei") || text.includes("paguei") || text.includes("comprei")) {
                    typeSelect.value = "expense";
                    typeSelect.dispatchEvent(new Event('change'));
                }

                // Amount detection
                const amountMatch = text.match(/(\d+(?:[.,]\d{1,2})?)(?:\s*reais?)?/);
                if (amountMatch) {
                    let amount = amountMatch[1].replace(",", ".");
                    amountInput.value = parseFloat(amount).toFixed(2);
                }

                // Category detection
                const selectedType = typeSelect.value;
                let matchedCategory = null;

                for (const category of categories) {
                    if (category.type === selectedType || category.type === 'both') {
                        if (text.includes(category.name.toLowerCase()) ||
                            text.includes(category.description.toLowerCase()) ||
                            category.name.toLowerCase().split(' ').some(word =>
                                word.length > 3 && text.includes(word))) {
                            matchedCategory = category;
                            break;
                        }
                    }
                }

                if (matchedCategory) {
                    categorySelect.value = matchedCategory.id;
                    voiceStatus.textContent += ` | Categoria: ${matchedCategory.emoji} ${matchedCategory.name}`;
                }

                // Date detection
                const today = new Date();
                if (text.includes("hoje")) {
                    dateInput.value = today.toISOString().slice(0, 16);
                } else if (text.includes("ontem")) {
                    today.setDate(today.getDate() - 1);
                    dateInput.value = today.toISOString().slice(0, 16);
                } else if (text.includes("amanhã")) {
                    today.setDate(today.getDate() + 1);
                    dateInput.value = today.toISOString().slice(0, 16);
                }

                // Set description
                descriptionInput.value = text.charAt(0).toUpperCase() + text.slice(1);

                // Animate form update
                document.querySelectorAll('input, select').forEach(el => {
                    el.classList.add('ring-2', 'ring-blue-200');
                    setTimeout(() => {
                        el.classList.remove('ring-2', 'ring-blue-200');
                    }, 1000);
                });
            }

            // Character counter for description
            descriptionInput.addEventListener('input', function() {
                const counter = this.parentElement.querySelector('.absolute.right-3');
                if (counter) {
                    counter.textContent = `${this.value.length}/200`;
                }
            });
        });
    </script>
@endsection
