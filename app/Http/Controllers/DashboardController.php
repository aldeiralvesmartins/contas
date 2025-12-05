<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Transaction;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Filtro por mês
        $month = $request->input('month', Carbon::now()->format('Y-m'));
        $selectedDate = Carbon::createFromFormat('Y-m', $month);
        $startOfMonth = $selectedDate->copy()->startOfMonth();
        $endOfMonth = $selectedDate->copy()->endOfMonth();

        $today = Carbon::today();

        /*
        |--------------------------------------------------------------------------
        | DADOS PRINCIPAIS - OTIMIZADOS COM FILTRO DE MÊS
        |--------------------------------------------------------------------------
        */

        // Total geral de transações (com filtro por mês)
        $totalTransactions = [
            'income' => Transaction::where('type', 'income')
                    ->whereBetween('transaction_date', [$startOfMonth, $endOfMonth])
                    ->sum('amount') ?? 0,
            'expense' => Transaction::where('type', 'expense')
                    ->whereBetween('transaction_date', [$startOfMonth, $endOfMonth])
                    ->sum('amount') ?? 0,
        ];

        // Transações por status (com filtro por mês)
        $transactionsByStatus = [
            'paid' => [
                'income' => Transaction::where('type', 'income')
                    ->where('status', 'paid')
                    ->whereBetween('transaction_date', [$startOfMonth, $endOfMonth])
                    ->sum('amount'),
                'expense' => Transaction::where('type', 'expense')
                    ->where('status', 'paid')
                    ->whereBetween('transaction_date', [$startOfMonth, $endOfMonth])
                    ->sum('amount'),
            ],
            'pending' => [
                'income' => Transaction::where('type', 'income')
                    ->where('status', 'pending')
                    ->whereBetween('transaction_date', [$startOfMonth, $endOfMonth])
                    ->sum('amount'),
                'expense' => Transaction::where('type', 'expense')
                    ->where('status', 'pending')
                    ->whereBetween('transaction_date', [$startOfMonth, $endOfMonth])
                    ->sum('amount'),
            ]
        ];

        // Contas vencidas (considera todas, não apenas do mês filtrado)
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
        | ACCOUNTS - COM FILTRO DE MÊS PARA CONTAS
        |--------------------------------------------------------------------------
        */

        // Contas pagas no mês filtrado - USANDO DATE
        $paidAccounts = Account::with('category')
            ->where('status', 'paid')
            ->whereBetween('paid_at', [$startOfMonth->format('Y-m-d'), $endOfMonth->format('Y-m-d')])
            ->get();

        $accountsPaid = [
            'income' => $paidAccounts->filter(fn($a) => $a->category->type === 'income')->sum('amount'),
            'expense' => $paidAccounts->filter(fn($a) => $a->category->type === 'expense')->sum('amount'),
        ];

        // Contas pendentes (vencidas no mês filtrado)
        $pendingAccounts = Account::with('category')
            ->where('status', 'pending')
            ->whereBetween('due_date', [$startOfMonth->format('Y-m-d'), $endOfMonth->format('Y-m-d')])
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

        // Contas vencidas (todas)
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
        | LISTAS PARA EXIBIÇÃO (COM FILTRO DE MÊS)
        |--------------------------------------------------------------------------
        */
        $displayData = [
            'upcoming_bills' => Account::with('category')
                ->where('status', 'pending')
                ->whereBetween('due_date', [$today->format('Y-m-d'), $today->copy()->addDays(7)->format('Y-m-d')])
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
                ->whereBetween('transaction_date', [$startOfMonth, $endOfMonth])
                ->latest('transaction_date')
                ->take(6)
                ->get(),

            'recent_accounts' => Account::with('category')
                ->whereBetween('due_date', [$startOfMonth->format('Y-m-d'), $endOfMonth->format('Y-m-d')])
                ->latest('due_date')
                ->take(4)
                ->get(),
        ];

        /*
        |--------------------------------------------------------------------------
        | ESTATÍSTICAS ADICIONAIS
        |--------------------------------------------------------------------------
        */
        $stats = [
            'category_stats' => $this->getCategoryStats($startOfMonth, $endOfMonth),
            'monthly_comparison' => $this->getMonthlyComparison($selectedDate),
            'monthly_options' => $this->getMonthlyOptions(),
        ];

        return view('dashboard', array_merge(
            $totals,
            $pendingTotals,
            [
                'overdue_totals' => $overdueTotals,
                'selected_month' => $month,
                'selected_month_name' => $selectedDate->translatedFormat('F Y'),
            ],
            $displayData,
            $stats,
            [
                'transactions_by_status' => $transactionsByStatus,
                'accounts_pending' => $accountsPending,
                'accounts_overdue' => $accountsOverdue,
            ]
        ));
    }

    private function getCategoryStats($startDate, $endDate)
    {
        return Category::withCount([
            'transactions' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('transaction_date', [$startDate, $endDate]);
            },
            'accounts' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('due_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
            }
        ])
            ->withSum([
                'transactions as transactions_total' => function($query) use ($startDate, $endDate) {
                    $query->whereBetween('transaction_date', [$startDate, $endDate]);
                }
            ], 'amount')
            ->withSum([
                'accounts as accounts_total' => function($query) use ($startDate, $endDate) {
                    $query->whereBetween('due_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
                }
            ], 'amount')
            ->get();
    }

    private function getMonthlyComparison($selectedDate)
    {
        $currentMonth = $selectedDate;
        $lastMonth = $selectedDate->copy()->subMonth();

        $current = [
            'income' => Transaction::where('type', 'income')
                ->whereMonth('transaction_date', $currentMonth->month)
                ->whereYear('transaction_date', $currentMonth->year)
                ->sum('amount'),
            'expense' => Transaction::where('type', 'expense')
                ->whereMonth('transaction_date', $currentMonth->month)
                ->whereYear('transaction_date', $currentMonth->year)
                ->sum('amount'),
        ];

        $last = [
            'income' => Transaction::where('type', 'income')
                ->whereMonth('transaction_date', $lastMonth->month)
                ->whereYear('transaction_date', $lastMonth->year)
                ->sum('amount'),
            'expense' => Transaction::where('type', 'expense')
                ->whereMonth('transaction_date', $lastMonth->month)
                ->whereYear('transaction_date', $lastMonth->year)
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

    private function getMonthlyOptions()
    {
        $options = [];
        $startDate = Carbon::now()->subMonths(12);
        $endDate = Carbon::now()->addMonths(6);

        $current = $startDate->copy();

        while ($current <= $endDate) {
            $options[] = [
                'value' => $current->format('Y-m'),
                'label' => $current->translatedFormat('F Y'),
                'is_current' => $current->format('Y-m') === Carbon::now()->format('Y-m'),
                'is_selected' => $current->format('Y-m') === request()->input('month', Carbon::now()->format('Y-m')),
            ];
            $current->addMonth();
        }

        return $options;
    }
}
