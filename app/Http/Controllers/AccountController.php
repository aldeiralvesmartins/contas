<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Category;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index()
    {
        $accounts = Account::with('category')
            ->latest()
            ->paginate(10);

        $categories = Category::all();

        return view('accounts.index', compact('accounts', 'categories'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('accounts.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:100',
            'amount' => 'required|numeric|min:0.01',
            'due_date' => 'required|date',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string|max:500',
        ]);

        Account::create($validated);

        return redirect()->route('accounts.index')
            ->with('success', 'Conta criada com sucesso!');
    }

    public function show(Account $account)
    {
        $account->load('category');
        return view('accounts.show', compact('account'));
    }

    public function edit(Account $account)
    {
        $categories = Category::all();
        return view('accounts.edit', compact('account', 'categories'));
    }

    public function update(Request $request, Account $account)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:100',
            'amount' => 'required|numeric|min:0.01',
            'due_date' => 'required|date',
            'category_id' => 'required|exists:categories,id',
            'status' => 'required|in:pending,paid',
            'description' => 'nullable|string|max:500',
        ]);

        $account->update($validated);

        return redirect()->route('accounts.show', $account)
            ->with('success', 'Conta atualizada com sucesso!');
    }

    public function destroy(Account $account)
    {
        $account->delete();

        return redirect()->route('accounts.index')
            ->with('success', 'Conta excluída com sucesso!');
    }

    public function pay(Account $account)
    {
        $account->update(['status' => 'paid']);

        return redirect()->route('accounts.index')
            ->with('success', 'Conta marcada como paga!');
    }

    /**
     * Duplicate an existing account
     */
    public function duplicate(Account $account)
    {
        try {
            // Criar nova conta com base na existente
            $newAccount = $account->replicate();

            // Modificar o título para indicar que é uma cópia
            $newAccount->title = $this->generateDuplicateTitle($account->title);

            // Resetar status para pendente (se aplicável)
            $newAccount->status = 'pending';

            // Ajustar data de vencimento para o próximo mês
            $newAccount->due_date = $this->getNextDueDate($account->due_date);

            // Salvar a nova conta
            $newAccount->save();

            return redirect()
                ->route('accounts.edit', $newAccount)
                ->with('success', 'Conta duplicada com sucesso! Você pode editar os dados da nova conta.');

        } catch (\Exception $e) {
            return redirect()
                ->route('accounts.index')
                ->with('error', 'Erro ao duplicar conta: ' . $e->getMessage());
        }
    }

    /**
     * Generate a title for the duplicated account
     */
    private function generateDuplicateTitle($originalTitle)
    {
        // Remover sufixos de cópia anteriores
        $cleanTitle = preg_replace('/\s+\(Cópia\s+\d+\)$/', '', $originalTitle);
        $cleanTitle = preg_replace('/\s+\(Cópia\)$/', '', $cleanTitle);

        // Verificar se já existe uma cópia com este título
        $existingCopies = Account::where('title', 'like', $cleanTitle . '%')->count();

        if ($existingCopies > 0) {
            return $cleanTitle . ' (Cópia ' . ($existingCopies + 1) . ')';
        }

        return $cleanTitle . ' (Cópia)';
    }

    /**
     * Calculate next due date for duplicated account
     */
    private function getNextDueDate($originalDueDate)
    {
        $dueDate = \Carbon\Carbon::parse($originalDueDate);

        // Se a data original já passou, usar o próximo mês
        if ($dueDate->isPast()) {
            return now()->addMonth()->format('Y-m-d');
        }

        // Se for no futuro, manter a data mas verificar se não é muito distante
        if ($dueDate->diffInMonths(now()) > 3) {
            return now()->addMonth()->format('Y-m-d');
        }

        return $dueDate->format('Y-m-d');
    }
}
