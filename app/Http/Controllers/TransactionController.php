<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Category;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        // Filtro por mês (padrão mês atual em formato Y-m)
        $month = $request->input('month', Carbon::now()->format('Y-m'));
        $selectedDate = Carbon::createFromFormat('Y-m', $month);
        $startOfMonth = $selectedDate->copy()->startOfMonth();
        $endOfMonth = $selectedDate->copy()->endOfMonth();

        $transactions = Transaction::with('category')
            ->whereBetween('transaction_date', [$startOfMonth, $endOfMonth])
            ->latest('transaction_date')
            ->paginate(12)
            ->appends(['month' => $month]);

        // Totais consolidados do mês (independente da paginação)
        $total_income = Transaction::whereBetween('transaction_date', [$startOfMonth, $endOfMonth])
            ->where('type', 'income')
            ->sum('amount');
        $total_expense = Transaction::whereBetween('transaction_date', [$startOfMonth, $endOfMonth])
            ->where('type', 'expense')
            ->sum('amount');
        $balance = $total_income - $total_expense;

        $categories = Category::all();
        $monthly_options = $this->getMonthlyOptions($month);

        return view('transactions.index', compact(
            'transactions',
            'categories',
            'monthly_options',
            'total_income',
            'total_expense',
            'balance',
            'month'
        ));
    }

    public function exportPdf(Request $request)
    {
        $month = $request->input('month', Carbon::now()->format('Y-m'));
        $selectedDate = Carbon::createFromFormat('Y-m', $month);
        $startOfMonth = $selectedDate->copy()->startOfMonth();
        $endOfMonth = $selectedDate->copy()->endOfMonth();

        $transactions = Transaction::with('category')
            ->whereBetween('transaction_date', [$startOfMonth, $endOfMonth])
            ->orderBy('transaction_date')
            ->get();

        $total_income = Transaction::whereBetween('transaction_date', [$startOfMonth, $endOfMonth])
            ->where('type', 'income')
            ->sum('amount');
        $total_expense = Transaction::whereBetween('transaction_date', [$startOfMonth, $endOfMonth])
            ->where('type', 'expense')
            ->sum('amount');
        $balance = $total_income - $total_expense;

        return view('transactions.export', [
            'transactions' => $transactions,
            'total_income' => $total_income,
            'total_expense' => $total_expense,
            'balance' => $balance,
            'selected_month' => $month,
            'selected_month_name' => $selectedDate->translatedFormat('F Y'),
        ]);
    }

    public function create()
    {
        $categories = Category::all();
        return view('transactions.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'description' => 'required|string|max:200',
            'amount' => 'required|numeric|min:0.01',
            'type' => 'required|in:income,expense',
            'category_id' => 'required|exists:categories,id',
            'transaction_date' => 'required|date',
            'notes' => 'nullable|string|max:500',
        ]);

        // Converter a data do formato datetime-local para timestamp
        $transactionDate = \Carbon\Carbon::parse($validated['transaction_date']);

        // Criar a transação
        Transaction::create([
            'description' => $validated['description'],
            'amount' => $validated['amount'],
            'type' => $validated['type'],
            'category_id' => $validated['category_id'],
            'transaction_date' => $transactionDate,
            'notes' => $validated['notes'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('transactions.index')
            ->with('success', 'Transação criada com sucesso!');
    }

    public function show(Transaction $transaction)
    {
        $transaction->load('category');
        return view('transactions.show', compact('transaction'));
    }

    public function edit(Transaction $transaction)
    {
        $categories = Category::all();
        return view('transactions.edit', compact('transaction', 'categories'));
    }

    public function update(Request $request, Transaction $transaction)
    {
        $validated = $request->validate([
            'description' => 'required|string|max:200',
            'amount' => 'required|numeric|min:0.01',
            'type' => 'required|in:income,expense',
            'category_id' => 'required|exists:categories,id',
            'transaction_date' => 'required|date',
            'notes' => 'nullable|string|max:500',
        ]);

        // Converter a data do formato datetime-local para timestamp
        $transactionDate = \Carbon\Carbon::parse($validated['transaction_date']);

        // Atualizar a transação com a nova data
        $transaction->update([
            'description' => $validated['description'],
            'amount' => $validated['amount'],
            'type' => $validated['type'],
            'category_id' => $validated['category_id'],
            'transaction_date' => $transactionDate,
            'notes' => $validated['notes'],
            // Opcional: se quiser alterar a data de criação também
            'created_at' => $transactionDate,
            'updated_at' => now(),
        ]);

        return redirect()->route('transactions.show', $transaction)
            ->with('success', 'Transação atualizada com sucesso!');
    }

    public function destroy(Transaction $transaction)
    {
        $transaction->delete();

        return redirect()->route('transactions.index')
            ->with('success', 'Transação excluída com sucesso!');
    }

    /**
     * Duplicate an existing transaction
     */
    public function duplicate(Transaction $transaction)
    {
        try {
            // Criar nova transação com base na existente
            $newTransaction = $transaction->replicate();

            // Modificar a descrição para indicar que é uma cópia
            $newTransaction->description = $this->generateDuplicateDescription($transaction->description);

            // Usar data atual para a nova transação
            $newTransaction->created_at = now();
            $newTransaction->updated_at = now();

            // Salvar a nova transação
            $newTransaction->save();

            return redirect()
                ->route('transactions.edit', $newTransaction)
                ->with('success', 'Transação duplicada com sucesso! Você pode editar os dados da nova transação.');

        } catch (\Exception $e) {
            return redirect()
                ->route('transactions.index')
                ->with('error', 'Erro ao duplicar transação: ' . $e->getMessage());
        }
    }

    /**
     * Generate a description for the duplicated transaction
     */
    private function generateDuplicateDescription($originalDescription)
    {
        // Remover sufixos de cópia anteriores
        $cleanDescription = preg_replace('/\s+\(Cópia\s+\d+\)$/', '', $originalDescription);
        $cleanDescription = preg_replace('/\s+\(Cópia\)$/', '', $cleanDescription);

        // Verificar se já existe uma cópia com esta descrição
        $existingCopies = Transaction::where('description', 'like', $cleanDescription . '%')->count();

        if ($existingCopies > 0) {
            return $cleanDescription . ' (Cópia ' . ($existingCopies + 1) . ')';
        }

        return $cleanDescription . ' (Cópia)';
    }

    // TransactionController.php
    public function markPaid($id)
    {
        $transaction = Transaction::findOrFail($id);
        $transaction->update(['status' => 'paid']);

        return redirect()->back()->with('success', 'Transação marcada como paga!');
    }

    public function markPending($id)
    {
        $transaction = Transaction::findOrFail($id);
        $transaction->update(['status' => 'pending']);

        return redirect()->back()->with('success', 'Transação marcada como pendente!');
    }

    private function getMonthlyOptions(string $selectedMonth)
    {
        $options = [];
        $startDate = Carbon::now()->subMonths(12);
        $endDate = Carbon::now()->addMonths(6);

        $current = $startDate->copy();
        while ($current <= $endDate) {
            $value = $current->format('Y-m');
            $options[] = [
                'value' => $value,
                'label' => $current->translatedFormat('F Y'),
                'is_current' => $value === Carbon::now()->format('Y-m'),
                'is_selected' => $value === $selectedMonth,
            ];
            $current->addMonth();
        }

        return $options;
    }
}
