<div class="transaction-card card p-6 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 relative overflow-hidden {{ $transaction->status === 'paid' ? 'ring-2 ring-emerald-200 bg-gradient-to-br from-white to-emerald-50' : '' }}"
     data-type="{{ $transaction->type }}"
     data-category="{{ $transaction->category_id }}"
     data-description="{{ strtolower($transaction->description) }}"
     data-notes="{{ strtolower($transaction->notes ?? '') }}"
     data-status="{{ $transaction->status }}">
    @if($transaction->status === 'paid')
        <div class="absolute top-4 right-4">
            <div class="badge-paid flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold">
                <div class="w-2 h-2 bg-white rounded-full animate-pulse"></div>
                Pago
            </div>
        </div>
    @else
        <div class="absolute top-4 right-4">
            <div class="badge-pending flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold">
                <div class="w-2 h-2 bg-amber-500 rounded-full animate-pulse"></div>
                Pendente
            </div>
        </div>
    @endif

    <div class="flex items-start justify-between mb-4 {{ $transaction->status === 'paid' ? 'pr-16' : '' }}">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 {{ $transaction->type === 'income' ? 'bg-emerald-100' : 'bg-rose-100' }} rounded-xl flex items-center justify-center shadow-sm">
                <span class="text-xl {{ $transaction->type === 'income' ? 'text-emerald-600' : 'text-rose-600' }}">
                    {{ $transaction->type === 'income' ? '📈' : '📉' }}
                </span>
            </div>
            <div>
                <h3 class="font-semibold text-slate-800 text-lg leading-tight">{{ $transaction->description }}</h3>
                <div class="flex items-center gap-2 mt-1">
                    <span class="badge-modern-info">{{ $transaction->category->name }}</span>
                    @if($transaction->type === 'income')
                        <span class="badge-modern-success">Entrada</span>
                    @else
                        <span class="badge-modern-danger">Saída</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="mb-4">
        <div class="text-2xl font-bold {{ $transaction->type === 'income' ? 'text-emerald-600' : 'text-rose-600' }} mb-1">
            {{ $transaction->type === 'income' ? '+' : '-' }}R$ {{ number_format($transaction->amount, 2, ',', '.') }}
        </div>
        <div class="text-sm text-slate-500">
            {{ $transaction->transaction_date ? \Carbon\Carbon::parse($transaction->transaction_date)->format('d/m/Y') : $transaction->created_at->format('d/m H:i') }}
        </div>
    </div>

    <div class="flex items-center justify-between pt-4 border-t border-slate-100">
        <div class="flex items-center gap-1">
            <button onclick="window.location.href='{{ route('transactions.show', $transaction->id) }}'" class="btn-action group" data-tooltip="Visualizar">
                <span class="group-hover:scale-110 transition-transform">👁️</span>
            </button>
            <form action="{{ route('transactions.duplicate', $transaction->id) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="btn-action group" data-tooltip="Duplicar Transação">
                    <span class="group-hover:scale-110 transition-transform">⎘</span>
                </button>
            </form>
        </div>

        <div class="flex items-center gap-1">
            @if($transaction->status === 'pending')
                <form action="{{ route('transactions.markPaid', $transaction->id) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="btn-action-success group" data-tooltip="Marcar como Paga">
                        <span class="group-hover:scale-110 transition-transform">✅</span>
                    </button>
                </form>
            @else
                <form action="{{ route('transactions.markPending', $transaction->id) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="btn-action-warning group" data-tooltip="Marcar como Pendente">
                        <span class="group-hover:scale-110 transition-transform">⏳</span>
                    </button>
                </form>
            @endif
        </div>

        <div class="flex items-center gap-1">
            <button onclick="window.location.href='{{ route('transactions.edit', $transaction->id) }}'" class="btn-action group" data-tooltip="Editar">
                <span class="group-hover:scale-110 transition-transform">✏️</span>
            </button>
            <form action="{{ route('transactions.destroy', $transaction->id) }}" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-action-danger group" data-confirm="Tem certeza que deseja excluir esta transação?">
                    <span class="group-hover:scale-110 transition-transform">🗑️</span>
                </button>
            </form>
        </div>
    </div>
</div>
