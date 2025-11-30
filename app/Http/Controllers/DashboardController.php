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
        // TRANSACTIONS - Entradas e Saídas
        $total_income_transactions = Transaction::where('type', 'income')->sum('amount') ?? 0;
        $total_expense_transactions = Transaction::where('type', 'expense')->sum('amount') ?? 0;

        // ACCOUNTS - Usando a relação com category para determinar o tipo
        // Contas pagas (já realizadas)
        $paid_accounts = Account::with('category')
            ->where('status', 'paid')
            ->get();

        $total_income_accounts = $paid_accounts->filter(function($account) {
            return $account->category->type === 'income';
        })->sum('amount');

        $total_expense_accounts = $paid_accounts->filter(function($account) {
            return $account->category->type === 'expense';
        })->sum('amount');

        // TOTAIS GERAIS (transactions + accounts pagas)
        $total_income = $total_income_transactions + $total_income_accounts;
        $total_expense = $total_expense_transactions + $total_expense_accounts;
        $balance = $total_income - $total_expense;

        // CONTAS PENDENTES (separadas por tipo via categoria)
        $pending_accounts = Account::with('category')
            ->where('status', 'pending')
            ->get();

        $pending_income_accounts = $pending_accounts->filter(function($account) {
            return $account->category->type === 'income';
        });

        $pending_expense_accounts = $pending_accounts->filter(function($account) {
            return $account->category->type === 'expense';
        });

        $pending_income_bills = $pending_income_accounts->count();
        $pending_expense_bills = $pending_expense_accounts->count();
        $pending_bills = $pending_income_bills + $pending_expense_bills;

        // VALORES PENDENTES
        $pending_income_amount = $pending_income_accounts->sum('amount');
        $pending_expense_amount = $pending_expense_accounts->sum('amount');

        // CONTAS VENCIDAS
        $overdue_accounts = Account::with('category')
            ->where('status', 'pending')
            ->whereDate('due_date', '<', Carbon::today())
            ->get();

        $late_income_bills = $overdue_accounts->filter(function($account) {
            return $account->category->type === 'income';
        })->count();

        $late_expense_bills = $overdue_accounts->filter(function($account) {
            return $account->category->type === 'expense';
        })->count();

        $late_bills = $late_income_bills + $late_expense_bills;

        // Contas pendentes recentes (próximos 7 dias)
        $upcoming_bills = Account::with('category')
            ->where('status', 'pending')
            ->whereBetween('due_date', [Carbon::today(), Carbon::today()->addDays(7)])
            ->orderBy('due_date')
            ->take(6)
            ->get();

        // Contas vencidas para display
        $overdue_bills = Account::with('category')
            ->where('status', 'pending')
            ->whereDate('due_date', '<', Carbon::today())
            ->orderBy('due_date')
            ->take(5)
            ->get();

        // Últimas transações
        $recent_transactions = Transaction::with('category')
            ->latest()
            ->take(6)
            ->get();

        // Últimas contas
        $recent_accounts = Account::with('category')
            ->latest()
            ->take(4)
            ->get();

        // Estatísticas por categoria
        $category_stats = Category::withCount(['transactions', 'accounts'])
            ->withSum(['transactions as transactions_total'], 'amount')
            ->withSum(['accounts as accounts_total'], 'amount')
            ->get();

        // Transações do mês atual
        $current_month_income = Transaction::where('type', 'income')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('amount');

        $current_month_expense = Transaction::where('type', 'expense')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('amount');

        // Tendência vs mês anterior
        $last_month_income = Transaction::where('type', 'income')
            ->whereMonth('created_at', Carbon::now()->subMonth()->month)
            ->whereYear('created_at', Carbon::now()->subMonth()->year)
            ->sum('amount');

        $last_month_expense = Transaction::where('type', 'expense')
            ->whereMonth('created_at', Carbon::now()->subMonth()->month)
            ->whereYear('created_at', Carbon::now()->subMonth()->year)
            ->sum('amount');

        $income_trend = $last_month_income > 0 ?
            (($current_month_income - $last_month_income) / $last_month_income) * 100 : 0;

        $expense_trend = $last_month_expense > 0 ?
            (($current_month_expense - $last_month_expense) / $last_month_expense) * 100 : 0;

        return view('dashboard', compact(
            'total_income',
            'total_expense',
            'balance',
            'pending_bills',
            'pending_income_bills',
            'pending_expense_bills',
            'late_bills',
            'pending_income_amount',
            'pending_expense_amount',
            'upcoming_bills',
            'overdue_bills',
            'recent_transactions',
            'recent_accounts',
            'category_stats',
            'current_month_income',
            'current_month_expense',
            'income_trend',
            'expense_trend'
        ));
    }
}
