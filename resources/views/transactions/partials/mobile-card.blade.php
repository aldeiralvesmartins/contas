<div class="transaction-item bg-white rounded-2xl p-4 shadow-sm border border-slate-200 active:scale-[0.99] transition-transform duration-150"
     data-type="{{ $transaction->type }}"
     data-category="{{ $transaction->category_id }}"
     data-description="{{ strtolower($transaction->description) }}"
     data-notes="{{ strtolower($transaction->notes ?? '') }}"
     data-status="{{ $transaction->status }}"
     onclick="window.location.href='{{ route('transactions.show', $transaction->id) }}'">
    <div class="flex items-start justify-between mb-3">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 {{ $transaction->type === 'income' ? 'bg-emerald-100' : 'bg-rose-100' }} rounded-xl flex items-center justify-center flex-shrink-0">
                <span class="text-lg {{ $transaction->type === 'income' ? 'text-emerald-600' : 'text-rose-600' }}">
                    {{ $transaction->type === 'income' ? '⬆️' : '⬇️' }}
                </span>
            </div>
            <div class="min-w-0 flex-1">
                <h3 class="font-semibold text-slate-800 text-sm truncate">{{ $transaction->description }}</h3>
                <div class="flex items-center gap-1.5 mt-1">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $transaction->type === 'income' ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                        {{ $transaction->type === 'income' ? 'Entrada' : 'Saída' }}
                    </span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-slate-50 text-slate-700">
                        {{ $transaction->category->name }}
                    </span>
                </div>
            </div>
        </div>
        <div class="flex-shrink-0 ml-2">
            @if($transaction->status === 'paid')
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-emerald-500 text-white">✅</span>
            @else
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-amber-500 text-white">⏳</span>
            @endif
        </div>
    </div>
    <div class="flex items-center justify-between">
        <div>
            <div class="text-lg font-bold {{ $transaction->type === 'income' ? 'text-emerald-600' : 'text-rose-600' }}">
                {{ $transaction->type === 'income' ? '+' : '-' }}R$ {{ number_format($transaction->amount, 2, ',', '.') }}
            </div>
            <div class="text-xs text-slate-500 mt-0.5">
                {{ $transaction->transaction_date ? \Carbon\Carbon::parse($transaction->transaction_date)->format('d/m/Y') : $transaction->created_at->format('d/m H:i') }}
            </div>
        </div>
        <div class="flex items-center gap-1" onclick="event.stopPropagation()"></div>
    </div>
</div>
