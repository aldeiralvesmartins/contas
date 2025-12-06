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
                <div id="voice-panel" class="mb-4 bg-white rounded-2xl shadow-xl p-4 max-w-xs transform transition-all duration-300 translate-y-4 opacity-0 pointer-events-none">
                    <div class="flex items-center justify-between mb-2">
                        <span class="font-medium text-slate-700">Assistente de Voz</span>
                        <button id="close-voice-panel" class="text-slate-400 hover:text-slate-600">
                            ✕
                        </button>
                    </div>
                    <p id="voice-status" class="text-sm text-slate-600 mb-2">Clique no microfone para começar</p>
                    <div id="voice-feedback" class="text-xs text-slate-500 space-y-1"></div>
                </div>

                <button type="button"
                        id="voice-btn"
                        class="w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-600 text-white rounded-full shadow-xl hover:shadow-2xl hover:scale-110 active:scale-95 transition-all duration-300 flex items-center justify-center group relative my-20">
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
                                   id="description-input"
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
                                       id="amount-input"
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
                                   id="date-input"
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
                    <div class="flex-1">
                        <h3 class="font-medium text-slate-800 mb-3">Exemplos práticos para uso por voz</h3>

                        <div class="space-y-4">
                            <!-- Saídas - Exemplos do dia a dia -->
                            <div class="space-y-2">
                                <p class="text-sm font-medium text-slate-700">💳 Transações de saída:</p>
                                <div class="space-y-1.5 pl-2">
                                    <p class="text-xs text-slate-600">• "Gasolina hoje, R$ 55,00"</p>
                                    <p class="text-xs text-slate-600">• "Supermercado ontem, 250 reais"</p>
                                    <p class="text-xs text-slate-600">• "Conta de luz dia 26 de setembro de 2025, R$ 150,00"</p>
                                    <p class="text-xs text-slate-600">• "Gastei R$ 89,90 no mercado hoje"</p>
                                    <p class="text-xs text-slate-600">• "Combustível na segunda-feira passada, R$ 120,50"</p>
                                    <p class="text-xs text-slate-600">• "Restaurante 15/10/2025, R$ 85,00"</p>
                                </div>
                            </div>

                            <!-- Entradas - Exemplos de receitas -->
                            <div class="space-y-2">
                                <p class="text-sm font-medium text-slate-700">💰 Transações de entrada:</p>
                                <div class="space-y-1.5 pl-2">
                                    <p class="text-xs text-slate-600">• "Salário hoje, R$ 2.500,00"</p>
                                    <p class="text-xs text-slate-600">• "Freelance ontem, R$ 800,00"</p>
                                    <p class="text-xs text-slate-600">• "Recebi R$ 1.000,50 da venda dia 10 do próximo mês"</p>
                                    <p class="text-xs text-slate-600">• "Pagamento de aluguel dia 05/11/2025, R$ 950,00"</p>
                                    <p class="text-xs text-slate-600">• "Venda de produto amanhã, 300 reais"</p>
                                    <p class="text-xs text-slate-600">• "Recebimento de consultoria, R$ 550,00"</p>
                                </div>
                            </div>

                            <!-- Outros exemplos variados -->
                            <div class="space-y-2">
                                <p class="text-sm font-medium text-slate-700">🎯 Mais exemplos úteis:</p>
                                <div class="space-y-1.5 pl-2">
                                    <p class="text-xs text-slate-600">• "Farmácia hoje, R$ 45,90"</p>
                                    <p class="text-xs text-slate-600">• "Cinema dia 3 do próximo mês, R$ 70,00"</p>
                                    <p class="text-xs text-slate-600">• "Paguei R$ 350,00 de academia ontem"</p>
                                    <p class="text-xs text-slate-600">• "Recebi R$ 1.500,00 de projeto freelance"</p>
                                    <p class="text-xs text-slate-600">• "Comprei R$ 199,90 de material de escritório"</p>
                                    <p class="text-xs text-slate-600">• "Entrada de R$ 2.000,00 de salário"</p>
                                </div>
                            </div>

                            <!-- Dicas de formato -->
                            <div class="pt-3 border-t border-blue-100">
                                <p class="text-xs text-slate-500 italic">
                                    💬 <strong>Dica:</strong> Use frases naturais como "Paguei 120 reais de gasolina ontem"
                                    ou "Recebi R$ 800,00 de freelance hoje". O sistema entende automaticamente tipo, valor,
                                    data e categoria!
                                </p>
                            </div>
                        </div>
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
            const voiceFeedback = document.getElementById('voice-feedback');
            const recordingIndicator = document.getElementById('recording-indicator');
            const closeVoicePanel = document.getElementById('close-voice-panel');

            // Form inputs
            const descriptionInput = document.getElementById('description-input');
            const amountInput = document.getElementById('amount-input');
            const dateInput = document.getElementById('date-input');

            // Data structures
            const categories = [
                    @foreach($categories as $c)
                {
                    id: {{ $c->id }},
                    name: "{{ strtolower($c->name) }}",
                    type: "{{ $c->type }}",
                    description: "{{ strtolower($c->description ?? '') }}",
                    emoji: "{{ $c->type == 'income' ? '💰' : ($c->type == 'expense' ? '💳' : '📁') }}"
                },
                @endforeach
            ];

            // Category synonyms
            const categorySynonyms = {
                'salário': ['salario', 'salário', 'pagamento', 'renda', 'proventos', 'ordenado', 'remuneração'],
                'alimentação': ['alimentacao', 'comida', 'mercado', 'supermercado', 'feira', 'restaurante', 'lanche'],
                'transporte': ['transporte', 'gasolina', 'combustível', 'uber', 'táxi', 'ônibus', 'metro'],
                'moradia': ['aluguel', 'condomínio', 'condominio', 'luz', 'água', 'agua', 'energia', 'internet'],
                'lazer': ['cinema', 'shopping', 'viagem', 'passeio', 'festas', 'entretenimento'],
                'saúde': ['saude', 'médico', 'medico', 'farmácia', 'farmacia', 'hospital', 'consulta'],
                'educação': ['educacao', 'curso', 'faculdade', 'livro', 'material', 'escola'],
                'investimento': ['investimento', 'ações', 'acoes', 'poupança', 'poupanca', 'tesouro']
            };

            // Type detection patterns
            const typePatterns = {
                income: [/recebi/, /ganhei/, /salário/, /entrada/, /renda/, /recebimento/, /provento/, /pagamento de/, /dinheiro de/, /valor de/],
                expense: [/gastei/, /paguei/, /comprei/, /saída/, /despesa/, /conta de/, /pagamento da/, /compra de/]
            };

            // Set default date
            const now = new Date();
            now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
            dateInput.value = now.toISOString().slice(0,16);

            // Voice panel control
            function showVoicePanel() {
                voicePanel.classList.remove('translate-y-4', 'opacity-0', 'pointer-events-none');
                voicePanel.classList.add('translate-y-0', 'opacity-100');
            }

            function hideVoicePanel() {
                voicePanel.classList.add('translate-y-4', 'opacity-0', 'pointer-events-none');
                voicePanel.classList.remove('translate-y-0', 'opacity-100');
            }

            closeVoicePanel.addEventListener('click', hideVoicePanel);
            voiceBtn.addEventListener('click', () => showVoicePanel());

            // Voice Recognition
            if (!("webkitSpeechRecognition" in window)) {
                voiceBtn.disabled = true;
                voiceBtn.classList.add('opacity-50', 'cursor-not-allowed');
                voiceStatus.textContent = "❌ Navegador não suporta reconhecimento de voz";
                return;
            }

            const recognition = new (webkitSpeechRecognition || SpeechRecognition)();
            recognition.lang = "pt-BR";
            recognition.continuous = false;
            recognition.interimResults = true;
            let isRecording = false;
            let lastTranscript = '';

            voiceBtn.addEventListener('click', toggleRecording);

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
                voiceBtn.classList.replace('from-blue-500', 'from-red-500');
                voiceBtn.classList.replace('to-purple-600', 'to-pink-600');
                voiceStatus.textContent = "🎤 Gravando... fale agora!";
                voiceFeedback.innerHTML = '';
                showVoicePanel();
            }

            function stopRecording() {
                isRecording = false;
                recordingIndicator.classList.add('hidden');
                voiceBtn.classList.replace('from-red-500', 'from-blue-500');
                voiceBtn.classList.replace('to-pink-600', 'to-purple-600');
            }

            recognition.onstart = startRecording;
            recognition.onend = stopRecording;

            recognition.onresult = (event) => {
                let interimTranscript = '';
                let finalTranscript = '';

                for (let i = event.resultIndex; i < event.results.length; i++) {
                    const transcript = event.results[i][0].transcript;
                    if (event.results[i].isFinal) {
                        finalTranscript += transcript;
                        lastTranscript = finalTranscript.toLowerCase();
                        voiceStatus.textContent = `📝 Reconhecido: "${lastTranscript}"`;
                        processVoiceCommand(lastTranscript);
                    } else {
                        interimTranscript += transcript;
                        voiceStatus.textContent = `🎤 Ouvindo: "${interimTranscript}"`;
                    }
                }
            };

            recognition.onerror = (event) => {
                voiceStatus.textContent = `❌ Erro: ${event.error}`;
                addFeedback(`Erro no reconhecimento: ${event.error}`, 'error');
                stopRecording();
            };

            // Main processing function
            function processVoiceCommand(text) {
                addFeedback(`Processando comando...`, 'info');

                // Extract information using separate functions
                const type = extractType(text);
                const amount = extractAmount(text);
                const date = extractDate(text);
                const category = extractCategory(text, type);
                const description = extractDescription(text, { type, amount, category });

                // Update form fields
                updateFormFields({ type, amount, date, category, description });

                // Show feedback
                showExtractionFeedback({ type, amount, date, category, description });
            }

            // Separate extraction functions
            function extractType(text) {
                for (const [type, patterns] of Object.entries(typePatterns)) {
                    for (const pattern of patterns) {
                        if (pattern.test(text)) {
                            return type;
                        }
                    }
                }

                // Default to expense if contains typical expense words
                if (text.match(/mercado|compras|conta|pagamento|despesa/)) {
                    return 'expense';
                }

                return null;
            }

            function extractAmount(text) {
                // Handle written numbers
                const writtenNumbers = {
                    'um': 1, 'dois': 2, 'três': 3, 'tres': 3, 'quatro': 4, 'cinco': 5,
                    'seis': 6, 'sete': 7, 'oito': 8, 'nove': 9, 'dez': 10,
                    'onze': 11, 'doze': 12, 'treze': 13, 'catorze': 14, 'quinze': 15,
                    'dezesseis': 16, 'dezessete': 17, 'dezoito': 18, 'dezenove': 19, 'vinte': 20,
                    'trinta': 30, 'quarenta': 40, 'cinquenta': 50, 'sessenta': 60,
                    'setenta': 70, 'oitenta': 80, 'noventa': 90,
                    'cem': 100, 'cento': 100, 'duzentos': 200, 'trezentos': 300,
                    'quatrocentos': 400, 'quinhentos': 500, 'seiscentos': 600,
                    'setecentos': 700, 'oitocentos': 800, 'novecentos': 900,
                    'mil': 1000, 'milhão': 1000000, 'milhao': 1000000
                };

                // Try to extract written numbers
                let writtenAmount = text;
                let total = 0;
                let currentNumber = 0;

                const words = text.split(/\s+/);
                for (const word of words) {
                    const cleanWord = word.replace(/[.,]/g, '');
                    if (writtenNumbers[cleanWord]) {
                        const value = writtenNumbers[cleanWord];
                        if (value >= 1000) {
                            total += (currentNumber || 1) * value;
                            currentNumber = 0;
                        } else if (value >= 100) {
                            total += currentNumber * value;
                            currentNumber = 0;
                        } else {
                            currentNumber += value;
                        }
                    } else if (cleanWord === 'e' && currentNumber > 0) {
                        // Continue building number
                    } else if (currentNumber > 0) {
                        total += currentNumber;
                        currentNumber = 0;
                    }
                }

                if (currentNumber > 0) total += currentNumber;
                if (total > 0) return total.toFixed(2);

                // Extract numeric amounts
                const patterns = [
                    /r\$\s*(\d+(?:[.,]\d{1,2})?)/i,
                    /(\d+(?:[.,]\d{3})*(?:[.,]\d{1,2})?)\s*reais/i,
                    /(\d+(?:[.,]\d{1,2})?)\s*(?:r\$)?/,
                    /valor.*?(\d+(?:[.,]\d{1,2})?)/i
                ];

                for (const pattern of patterns) {
                    const match = text.match(pattern);
                    if (match) {
                        let amountStr = match[1].replace(/\./g, '').replace(',', '.');
                        return parseFloat(amountStr).toFixed(2);
                    }
                }

                return null;
            }

            function extractCategory(text, type) {
                // Normalize text for comparison
                const normalizedText = text.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');

                // Check for exact matches in category names
                for (const category of categories) {
                    if ((type === null || category.type === type || category.type === 'both') &&
                        normalizedText.includes(category.name.toLowerCase())) {
                        return category;
                    }
                }

                // Check synonyms
                for (const [mainCategory, synonyms] of Object.entries(categorySynonyms)) {
                    for (const synonym of synonyms) {
                        if (normalizedText.includes(synonym)) {
                            // Find corresponding category
                            for (const category of categories) {
                                if (category.name.toLowerCase().includes(mainCategory.toLowerCase())) {
                                    return category;
                                }
                            }
                        }
                    }
                }

                // Fuzzy match
                for (const category of categories) {
                    if (type === null || category.type === type || category.type === 'both') {
                        const words = (category.name + ' ' + category.description).toLowerCase().split(/\s+/);
                        for (const word of words) {
                            if (word.length > 3 && normalizedText.includes(word)) {
                                return category;
                            }
                        }
                    }
                }

                return null;
            }

            function extractDate(text) {
                const today = new Date();
                let date = new Date(today);

                // Handle relative dates
                if (text.includes('hoje')) {
                    // Already today
                } else if (text.includes('ontem')) {
                    date.setDate(date.getDate() - 1);
                } else if (text.includes('anteontem')) {
                    date.setDate(date.getDate() - 2);
                } else if (text.includes('amanhã') || text.includes('amanha')) {
                    date.setDate(date.getDate() + 1);
                } else if (text.includes('depois de amanhã') || text.includes('depois de amanha')) {
                    date.setDate(date.getDate() + 2);
                } else if (text.includes('semana que vem')) {
                    date.setDate(date.getDate() + 7);
                } else if (text.includes('semana passada')) {
                    date.setDate(date.getDate() - 7);
                } else if (text.includes('próxima semana') || text.includes('proxima semana')) {
                    date.setDate(date.getDate() + 7);
                } else if (text.includes('mês que vem') || text.includes('mes que vem')) {
                    date.setMonth(date.getMonth() + 1);
                } else if (text.includes('mês passado') || text.includes('mes passado')) {
                    date.setMonth(date.getMonth() - 1);
                } else if (text.includes('próximo mês') || text.includes('proximo mes')) {
                    date.setMonth(date.getMonth() + 1);
                }

                // Handle specific dates
                const datePatterns = [
                    /dia\s+(\d{1,2})(?:\s+de\s+(\w+))?(?:\s+de\s+(\d{4}))?/i,
                    /(\d{1,2})\/(\d{1,2})(?:\/(\d{2,4}))?/,
                    /(\d{1,2})\s+de\s+(\w+)\s+de\s+(\d{4})/i
                ];

                const monthMap = {
                    'janeiro': 0, 'fevereiro': 1, 'março': 2, 'marco': 2, 'abril': 3,
                    'maio': 4, 'junho': 5, 'julho': 6, 'agosto': 7, 'setembro': 8,
                    'outubro': 9, 'novembro': 10, 'dezembro': 11
                };

                for (const pattern of datePatterns) {
                    const match = text.match(pattern);
                    if (match) {
                        if (pattern.source.includes('/')) {
                            // DD/MM/YYYY
                            const day = parseInt(match[1]);
                            const month = parseInt(match[2]) - 1;
                            const year = match[3] ?
                                parseInt(match[3].length === 2 ? '20' + match[3] : match[3]) :
                                today.getFullYear();
                            date = new Date(year, month, day);
                        } else {
                            // "dia X de Y de Z"
                            const day = parseInt(match[1]);
                            const monthStr = match[2] ? match[2].toLowerCase() : null;
                            const year = match[3] ? parseInt(match[3]) : today.getFullYear();
                            const month = monthStr ? monthMap[monthStr] : today.getMonth();
                            date = new Date(year, month, day);
                        }
                        break;
                    }
                }

                // Handle weekdays
                const weekdays = ['domingo', 'segunda', 'terça', 'terca', 'quarta', 'quinta', 'sexta', 'sábado', 'sabado'];
                for (let i = 0; i < weekdays.length; i++) {
                    if (text.includes(weekdays[i])) {
                        const targetDay = i;
                        const currentDay = today.getDay();
                        let diff = targetDay - currentDay;
                        if (diff <= 0) diff += 7; // Next occurrence

                        if (text.includes('passada') || text.includes('passado')) {
                            diff -= 7; // Last occurrence
                        }

                        date = new Date(today);
                        date.setDate(today.getDate() + diff);
                        break;
                    }
                }

                // Adjust for timezone and format
                date.setMinutes(date.getMinutes() - date.getTimezoneOffset());
                return date;
            }

            function extractDescription(text, extractedData) {
                let description = text;

                // Remove amount patterns
                description = description.replace(/(r\$\s*)?\d+(?:[.,]\d{1,2})?(?:\s*reais?)?/gi, '');

                // Remove type indicators
                description = description.replace(/\b(recebi|ganhei|gastei|paguei|comprei|entrada|saída|saida)\b/gi, '');

                // Remove date references
                description = description.replace(/\b(hoje|ontem|amanhã|amanha|anteontem|semana|mês|mes|passado|próximo|proximo|que vem)\b/gi, '');

                // Remove category names if detected
                if (extractedData.category) {
                    const categoryWords = extractedData.category.name.toLowerCase().split(/\s+/);
                    for (const word of categoryWords) {
                        description = description.replace(new RegExp(word, 'gi'), '');
                    }
                }

                // Remove common filler words
                const fillerWords = [
                    'de', 'do', 'da', 'dos', 'das', 'em', 'no', 'na', 'nos', 'nas',
                    'com', 'por', 'para', 'um', 'uma', 'uns', 'umas', 'o', 'a', 'os', 'as',
                    'que', 'qual', 'quais', 'e', 'ou', 'mas', 'porém', 'entretanto',
                    'valor', 'total', 'no valor de', 'na quantia de'
                ];

                fillerWords.forEach(word => {
                    description = description.replace(new RegExp('\\b' + word + '\\b', 'gi'), '');
                });

                // Clean up
                description = description
                    .replace(/\s+/g, ' ')
                    .replace(/^\s+|\s+$/g, '')
                    .replace(/[.,;]$/, '')
                    .trim();

                // Capitalize first letter
                if (description.length > 0) {
                    description = description.charAt(0).toUpperCase() + description.slice(1);
                }

                // If description is too short, create one from context
                if (description.split(' ').length < 2) {
                    description = extractedData.type === 'income' ? 'Recebimento' : 'Despesa';
                    if (extractedData.category) {
                        description += ' - ' + extractedData.category.name;
                    }
                }

                return description;
            }

            function updateFormFields(data) {
                // Update type
                if (data.type) {
                    typeSelect.value = data.type;
                    typeSelect.dispatchEvent(new Event('change'));
                    animateField(typeSelect);
                }

                // Update amount
                if (data.amount) {
                    amountInput.value = data.amount;
                    animateField(amountInput);
                }

                // Update category
                if (data.category) {
                    categorySelect.value = data.category.id;
                    animateField(categorySelect);
                }

                // Update date
                if (data.date) {
                    dateInput.value = data.date.toISOString().slice(0,16);
                    animateField(dateInput);
                }

                // Update description
                if (data.description) {
                    descriptionInput.value = data.description;
                    animateField(descriptionInput);
                }
            }

            function showExtractionFeedback(data) {
                const feedback = [];

                if (data.type) {
                    feedback.push(`Tipo: ${data.type === 'income' ? 'Entrada' : 'Saída'}`);
                }

                if (data.amount) {
                    feedback.push(`Valor: R$ ${parseFloat(data.amount).toFixed(2).replace('.', ',')}`);
                }

                if (data.category) {
                    feedback.push(`Categoria: ${data.category.name}`);
                }

                if (data.date) {
                    const dateStr = data.date.toLocaleDateString('pt-BR');
                    feedback.push(`Data: ${dateStr}`);
                }

                if (feedback.length > 0) {
                    voiceFeedback.innerHTML = feedback.map(f =>
                        `<div class="text-green-600">✓ ${f}</div>`
                    ).join('');
                } else {
                    voiceFeedback.innerHTML = '<div class="text-yellow-600">⚠️ Não consegui identificar informações. Tente novamente.</div>';
                }
            }

            function animateField(element) {
                element.classList.add('ring-2', 'ring-green-200', 'scale-[1.02]');
                setTimeout(() => {
                    element.classList.remove('ring-2', 'ring-green-200', 'scale-[1.02]');
                }, 1000);
            }

            function addFeedback(message, type = 'info') {
                const colors = {
                    info: 'text-blue-600',
                    success: 'text-green-600',
                    warning: 'text-yellow-600',
                    error: 'text-red-600'
                };

                const div = document.createElement('div');
                div.className = colors[type];
                div.textContent = message;

                voiceFeedback.appendChild(div);

                // Keep only last 3 messages
                while (voiceFeedback.children.length > 3) {
                    voiceFeedback.removeChild(voiceFeedback.firstChild);
                }
            }

            // Form validation for voice input
            form = document.querySelector('form[data-validate]');
            form.addEventListener('submit', function(e) {
                if (!descriptionInput.value || !amountInput.value) {
                    e.preventDefault();
                    addFeedback('Preencha descrição e valor antes de salvar', 'error');
                    showVoicePanel();
                }
            });
        });
    </script>
@endsection
