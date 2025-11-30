@extends('layout')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="text-3xl font-bold text-slate-800 flex items-center gap-3">
                    <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center">
                        <span class="text-xl">🏷️</span>
                    </div>
                    Categorias
                </h2>
                <p class="text-slate-600 mt-2">Organize suas transações por categorias</p>
            </div>
            <a href="{{ route('categories.create') }}" class="btn-primary flex items-center gap-2">
                <span>+</span>
                <span>Nova Categoria</span>
            </a>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="card p-6 text-center">
                <div class="text-2xl font-bold text-slate-800">{{ $categories->total() }}</div>
                <p class="text-slate-600 text-sm">Total</p>
            </div>
            <div class="card p-6 text-center">
                <div class="text-2xl font-bold text-emerald-600">{{ $categories->where('type', 'income')->count() }}</div>
                <p class="text-slate-600 text-sm">Entrada</p>
            </div>
            <div class="card p-6 text-center">
                <div class="text-2xl font-bold text-rose-600">{{ $categories->where('type', 'expense')->count() }}</div>
                <p class="text-slate-600 text-sm">Saída</p>
            </div>
            <div class="card p-6 text-center">
                @php
                    $totalTransactions = $categories->sum('transactions_count');
                @endphp
                <div class="text-2xl font-bold text-blue-600">{{ $totalTransactions }}</div>
                <p class="text-slate-600 text-sm">Transações</p>
            </div>
        </div>

        <!-- Filtros -->
        <div class="card p-6">
            <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-end">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Buscar Categorias</label>
                    <input type="text" data-filter=".category-row" placeholder="Digite para filtrar..." class="input-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Tipo</label>
                    <select class="input-primary" id="type-filter">
                        <option value="">Todos</option>
                        <option value="income">Entrada</option>
                        <option value="expense">Saída</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Tabela -->
        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="table-modern">
                    <thead>
                    <tr>
                        <th class="py-4 px-6">Nome</th>
                        <th class="py-4 px-6">Tipo</th>
                        <th class="py-4 px-6">Cor</th>
                        <th class="py-4 px-6">Transações</th>
                        <th class="py-4 px-6">Contas</th>
                        <th class="py-4 px-6 text-center">Ações</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($categories as $category)
                        <tr class="category-row" data-type="{{ $category->type }}">
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    @if($category->icon)
                                        <div class="w-8 h-8 bg-slate-100 rounded-lg flex items-center justify-center">
                                            <span class="text-slate-600">{{ $category->icon }}</span>
                                        </div>
                                    @endif
                                    <div>
                                        <p class="font-medium text-slate-800">{{ $category->name }}</p>
                                        @if($category->description)
                                            <p class="text-sm text-slate-500 mt-1">{{ Str::limit($category->description, 50) }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                @if($category->type === 'income')
                                    <span class="badge-success">Entrada</span>
                                @else
                                    <span class="badge-danger">Saída</span>
                                @endif
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full border-2 border-slate-200"
                                         style="background-color: {{ $category->color ?? '#6b7280' }}"></div>
                                    @if($category->color)
                                        <span class="text-xs text-slate-500">{{ $category->color }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <div class="text-center">
                                    <span class="font-semibold text-slate-800">{{ $category->transactions_count ?? 0 }}</span>
                                    <p class="text-xs text-slate-500">transações</p>
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <div class="text-center">
                                    <span class="font-semibold text-slate-800">{{ $category->accounts_count ?? 0 }}</span>
                                    <p class="text-xs text-slate-500">contas</p>
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('categories.show', $category->id) }}"
                                       class="p-2 text-slate-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                       data-tooltip="Visualizar">
                                        👁️
                                    </a>

                                    <a href="{{ route('categories.edit', $category->id) }}"
                                       class="p-2 text-slate-600 hover:text-green-600 hover:bg-green-50 rounded-lg transition-colors"
                                       data-tooltip="Editar">
                                        ✏️
                                    </a>

                                    @if($category->transactions_count == 0 && $category->accounts_count == 0)
                                        <form action="{{ route('categories.destroy', $category->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="p-2 text-slate-600 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                                    data-confirm="Tem certeza que deseja excluir esta categoria?">
                                                🗑️
                                            </button>
                                        </form>
                                    @else
                                        <span class="p-2 text-slate-400 cursor-not-allowed" data-tooltip="Categoria em uso - não pode ser excluída">
                                    🗑️
                                </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Empty State -->
            @if($categories->isEmpty())
                <div class="text-center py-12">
                    <div class="w-24 h-24 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-3xl">🏷️</span>
                    </div>
                    <h3 class="text-lg font-semibold text-slate-700 mb-2">Nenhuma categoria encontrada</h3>
                    <p class="text-slate-500 mb-4">Crie categorias para organizar suas transações</p>
                    <a href="{{ route('categories.create') }}" class="btn-primary inline-flex items-center gap-2">
                        <span>+</span>
                        <span>Nova Categoria</span>
                    </a>
                </div>
            @endif
        </div>

        <!-- Paginação -->
        @if($categories->hasPages())
            <div class="card p-6">
                {{ $categories->links() }}
            </div>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Filtro por tipo
            const typeFilter = document.getElementById('type-filter');

            function applyFilters() {
                const typeValue = typeFilter.value;

                document.querySelectorAll('.category-row').forEach(row => {
                    const rowType = row.getAttribute('data-type');
                    const typeMatch = !typeValue || rowType === typeValue;

                    row.style.display = typeMatch ? '' : 'none';
                });
            }

            typeFilter.addEventListener('change', applyFilters);
        });
    </script>
@endsection
