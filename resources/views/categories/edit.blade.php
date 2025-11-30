@extends('layout')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="mb-8">
            <a href="{{ route('categories.show', $category->id) }}" class="inline-flex items-center text-blue-600 hover:text-blue-700 font-medium mb-4 group">
                <span class="group-hover:-translate-x-1 transition-transform">←</span>
                <span class="ml-2">Voltar para Detalhes</span>
            </a>
            <h2 class="text-3xl font-bold text-slate-800 flex items-center gap-3">
                <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center">
                    <span class="text-xl">✏️</span>
                </div>
                Editar Categoria
            </h2>
            <p class="text-slate-600 mt-2">Atualize as informações desta categoria</p>
        </div>

        <div class="card p-8">
            <form action="{{ route('categories.update', $category->id) }}" method="POST" class="space-y-6" data-validate>
                @csrf
                @method('PUT')

                <!-- Nome -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        Nome da Categoria *
                    </label>
                    <input type="text"
                           name="name"
                           value="{{ old('name', $category->name) }}"
                           required
                           class="input-primary"
                           placeholder="Ex: Alimentação, Transporte, Salário..."
                           maxlength="50">
                    <p class="text-slate-500 text-xs mt-1">Máximo 50 caracteres</p>
                </div>

                <!-- Tipo -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        Tipo *
                    </label>
                    <select name="type" class="input-primary" id="category-type">
                        <option value="income" {{ old('type', $category->type) == 'income' ? 'selected' : '' }}>Entrada</option>
                        <option value="expense" {{ old('type', $category->type) == 'expense' ? 'selected' : '' }}>Saída</option>
                    </select>
                </div>

                <!-- Cor -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        Cor
                    </label>
                    <div class="flex gap-2 flex-wrap">
                        @php
                            $colors = [
                                'blue' => '#3b82f6',
                                'green' => '#10b981',
                                'red' => '#ef4444',
                                'yellow' => '#f59e0b',
                                'purple' => '#8b5cf6',
                                'pink' => '#ec4899',
                                'indigo' => '#6366f1',
                                'teal' => '#14b8a6'
                            ];
                            $currentColor = $category->color ?? '#6b7280';
                        @endphp
                        @foreach($colors as $name => $hex)
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="color" value="{{ $hex }}"
                                       {{ old('color', $currentColor) == $hex ? 'checked' : '' }}
                                       class="hidden color-radio">
                                <div class="w-8 h-8 rounded-full border-2 border-transparent color-option transition-all duration-200"
                                     style="background-color: {{ $hex }}"
                                     data-color="{{ $hex }}">
                                </div>
                            </label>
                        @endforeach

                        <!-- Cor personalizada -->
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="color" value=""
                                   {{ !in_array($currentColor, array_values($colors)) ? 'checked' : '' }}
                                   class="hidden color-radio" id="custom-color-radio">
                            <div class="w-8 h-8 rounded-full border-2 border-slate-300 flex items-center justify-center color-option"
                                 id="custom-color-display">
                                <span class="text-xs text-slate-600">+</span>
                            </div>
                        </label>
                        <input type="color"
                               name="custom_color"
                               value="{{ !in_array($currentColor, array_values($colors)) ? $currentColor : '#6b7280' }}"
                               class="w-12 h-8 rounded border border-slate-300 hidden"
                               id="custom-color-picker">
                    </div>
                </div>

                <!-- Ícone -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        Ícone (Opcional)
                    </label>
                    <div class="grid grid-cols-4 sm:grid-cols-8 gap-2">
                        @php
                            $icons = ['💰', '🍔', '🚗', '🏠', '📱', '👕', '🎮', '📚', '🏥', '✈️', '🎁', '💡', '⚡', '🎯', '🛒', '☕'];
                            $currentIcon = $category->icon ?? '💰';
                        @endphp
                        @foreach($icons as $icon)
                            <label class="flex items-center justify-center cursor-pointer">
                                <input type="radio" name="icon" value="{{ $icon }}"
                                       {{ old('icon', $currentIcon) == $icon ? 'checked' : '' }}
                                       class="hidden icon-radio">
                                <div class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center text-xl icon-option hover:bg-slate-200 transition-colors">
                                    {{ $icon }}
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Descrição -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        Descrição (Opcional)
                    </label>
                    <textarea name="description"
                              rows="3"
                              class="input-primary"
                              placeholder="Descreva o propósito desta categoria...">{{ old('description', $category->description) }}</textarea>
                </div>

                <!-- Submit Buttons -->
                <div class="flex gap-4 pt-4">
                    <button type="submit" class="btn-primary flex-1 py-4 flex items-center justify-center gap-2">
                        <span>💾</span>
                        <span>Atualizar Categoria</span>
                    </button>
                    <a href="{{ route('categories.show', $category->id) }}" class="btn-secondary flex items-center justify-center gap-2 px-6">
                        <span>↶</span>
                        <span>Cancelar</span>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <style>
        .color-option:has(.color-radio:checked) {
            border-color: #3b82f6;
            transform: scale(1.1);
        }

        .icon-option:has(.icon-radio:checked) {
            background-color: #3b82f6;
            color: white;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Efeito de seleção para cores
            const colorRadios = document.querySelectorAll('.color-radio');
            const customColorRadio = document.getElementById('custom-color-radio');
            const customColorPicker = document.getElementById('custom-color-picker');
            const customColorDisplay = document.getElementById('custom-color-display');

            colorRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    if (this === customColorRadio) {
                        customColorPicker.style.display = 'block';
                        customColorDisplay.style.backgroundColor = customColorPicker.value;
                        customColorDisplay.innerHTML = '';
                    } else {
                        customColorPicker.style.display = 'none';
                        const color = this.closest('label').querySelector('.color-option').getAttribute('data-color');
                        customColorDisplay.style.backgroundColor = '#f8fafc';
                        customColorDisplay.innerHTML = '<span class="text-xs text-slate-600">+</span>';
                    }
                });
            });

            customColorPicker.addEventListener('input', function() {
                customColorDisplay.style.backgroundColor = this.value;
                customColorDisplay.innerHTML = '';
                customColorRadio.checked = true;
                customColorRadio.dispatchEvent(new Event('change'));
            });

            // Trigger initial state
            if (customColorRadio.checked) {
                customColorRadio.dispatchEvent(new Event('change'));
            }

            // Efeito de seleção para ícones
            const iconRadios = document.querySelectorAll('.icon-radio');
            iconRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    document.querySelectorAll('.icon-option').forEach(el => {
                        el.style.backgroundColor = '';
                        el.style.color = '';
                    });
                    this.closest('.icon-option').style.backgroundColor = '#3b82f6';
                    this.closest('.icon-option').style.color = 'white';
                });

                // Set initial state for checked icon
                if (radio.checked) {
                    radio.closest('.icon-option').style.backgroundColor = '#3b82f6';
                    radio.closest('.icon-option').style.color = 'white';
                }
            });
        });
    </script>
@endsection
