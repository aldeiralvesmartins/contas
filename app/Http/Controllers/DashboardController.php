<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Transaction;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        /*
        |--------------------------------------------------------------------------
        | DADOS PRINCIPAIS - OTIMIZADOS
        |--------------------------------------------------------------------------
        */

        // Total geral de transações (independente do status)
        $totalTransactions = [
            'income' => Transaction::where('type', 'income')->sum('amount') ?? 0,
            'expense' => Transaction::where('type', 'expense')->sum('amount') ?? 0,
        ];

        // Transações por status
        $transactionsByStatus = [
            'paid' => [
                'income' => Transaction::where('type', 'income')->where('status', 'paid')->sum('amount'),
                'expense' => Transaction::where('type', 'expense')->where('status', 'paid')->sum('amount'),
            ],
            'pending' => [
                'income' => Transaction::where('type', 'income')->where('status', 'pending')->sum('amount'),
                'expense' => Transaction::where('type', 'expense')->where('status', 'pending')->sum('amount'),
            ]
        ];

        // Contas vencidas
        $overdueTransactions = Transaction::where('status', 'pending')
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', $today)
            ->get();

        $lateTransactions = [
            'income' => $overdueTransactions->where('type', 'income')->count(),
            'expense' => $overdueTransactions->where('type', 'expense')->count(),
            'total' => $overdueTransactions->count()
        ];

        /*
        |--------------------------------------------------------------------------
        | ACCOUNTS - OTIMIZADO
        |--------------------------------------------------------------------------
        */

        // Contas pagas
        $paidAccounts = Account::with('category')
            ->where('status', 'paid')
            ->get();

        $accountsPaid = [
            'income' => $paidAccounts->filter(fn($a) => $a->category->type === 'income')->sum('amount'),
            'expense' => $paidAccounts->filter(fn($a) => $a->category->type === 'expense')->sum('amount'),
        ];

        // Contas pendentes
        $pendingAccounts = Account::with('category')
            ->where('status', 'pending')
            ->get();

        $accountsPending = [
            'count' => [
                'income' => $pendingAccounts->filter(fn($a) => $a->category->type === 'income')->count(),
                'expense' => $pendingAccounts->filter(fn($a) => $a->category->type === 'expense')->count(),
            ],
            'amount' => [
                'income' => $pendingAccounts->filter(fn($a) => $a->category->type === 'income')->sum('amount'),
                'expense' => $pendingAccounts->filter(fn($a) => $a->category->type === 'expense')->sum('amount'),
            ]
        ];

        // Contas vencidas
        $overdueAccounts = Account::with('category')
            ->where('status', 'pending')
            ->whereDate('due_date', '<', $today)
            ->get();

        $accountsOverdue = [
            'income' => $overdueAccounts->filter(fn($a) => $a->category->type === 'income')->count(),
            'expense' => $overdueAccounts->filter(fn($a) => $a->category->type === 'expense')->count(),
        ];

        /*
        |--------------------------------------------------------------------------
        | TOTAIS CONSOLIDADOS
        |--------------------------------------------------------------------------
        */
        $totals = [
            'income' => $totalTransactions['income'] + $accountsPaid['income'],
            'expense' => $totalTransactions['expense'] + $accountsPaid['expense'],
        ];

        $totals['balance'] = $totals['income'] - $totals['expense'];

        // Pendências consolidadas
        $pendingTotals = [
            'count' => $accountsPending['count']['income'] + $accountsPending['count']['expense'],
            'amount' => [
                'income' => $accountsPending['amount']['income'] + $transactionsByStatus['pending']['income'],
                'expense' => $accountsPending['amount']['expense'] + $transactionsByStatus['pending']['expense'],
            ]
        ];

        // Vencidos consolidados
        $overdueTotals = [
            'count' => $accountsOverdue['income'] + $accountsOverdue['expense'] + $lateTransactions['total'],
            'accounts' => $accountsOverdue['income'] + $accountsOverdue['expense'],
            'transactions' => $lateTransactions['total']
        ];

        /*
        |--------------------------------------------------------------------------
        | LISTAS PARA EXIBIÇÃO
        |--------------------------------------------------------------------------
        */
        $displayData = [
            'upcoming_bills' => Account::with('category')
                ->where('status', 'pending')
                ->whereBetween('due_date', [$today, $today->copy()->addDays(7)])
                ->orderBy('due_date')
                ->take(6)
                ->get(),

            'overdue_bills' => Account::with('category')
                ->where('status', 'pending')
                ->whereDate('due_date', '<', $today)
                ->orderBy('due_date')
                ->take(5)
                ->get(),

            'recent_transactions' => Transaction::with('category')
                ->latest()
                ->take(6)
                ->get(),

            'recent_accounts' => Account::with('category')
                ->latest()
                ->take(4)
                ->get(),
        ];

        /*
        |--------------------------------------------------------------------------
        | ESTATÍSTICAS ADICIONAIS
        |--------------------------------------------------------------------------
        */
        $stats = [
            'category_stats' => Category::withCount(['transactions', 'accounts'])
                ->withSum(['transactions as transactions_total'], 'amount')
                ->withSum(['accounts as accounts_total'], 'amount')
                ->get(),

            'monthly_comparison' => $this->getMonthlyComparison(),
        ];

        return view('dashboard', array_merge(
            $totals,
            $pendingTotals,
            ['overdue_totals' => $overdueTotals],
            $displayData,
            $stats,
            [
                'transactions_by_status' => $transactionsByStatus,
                'accounts_pending' => $accountsPending,
                'accounts_overdue' => $accountsOverdue,
            ]
        ));
    }

    private function getMonthlyComparison()
    {
        $currentMonth = Carbon::now();
        $lastMonth = Carbon::now()->subMonth();

        $current = [
            'income' => Transaction::where('type', 'income')
                ->whereMonth('created_at', $currentMonth->month)
                ->whereYear('created_at', $currentMonth->year)
                ->sum('amount'),
            'expense' => Transaction::where('type', 'expense')
                ->whereMonth('created_at', $currentMonth->month)
                ->whereYear('created_at', $currentMonth->year)
                ->sum('amount'),
        ];

        $last = [
            'income' => Transaction::where('type', 'income')
                ->whereMonth('created_at', $lastMonth->month)
                ->whereYear('created_at', $lastMonth->year)
                ->sum('amount'),
            'expense' => Transaction::where('type', 'expense')
                ->whereMonth('created_at', $lastMonth->month)
                ->whereYear('created_at', $lastMonth->year)
                ->sum('amount'),
        ];

        return [
            'current' => $current,
            'last' => $last,
            'trend' => [
                'income' => $last['income'] > 0 ? (($current['income'] - $last['income']) / $last['income']) * 100 : 0,
                'expense' => $last['expense'] > 0 ? (($current['expense'] - $last['expense']) / $last['expense']) * 100 : 0,
            ]
        ];
    }
}
