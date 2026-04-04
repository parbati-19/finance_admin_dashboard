<?php

namespace App\Http\Controllers;

use App\Models\Enums\TransactionType;
use App\Models\TransactionRecord;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $now = Carbon::now();

        $currentMonthStart = $now->copy()->startOfMonth();
        $currentMonthEnd = $now->copy()->endOfMonth();
        $lastMonthStart = $now->copy()->subMonth()->startOfMonth();
        $lastMonthEnd = $now->copy()->subMonth()->endOfMonth();
        $currentWeekStart = $now->copy()->startOfWeek();
        $currentWeekEnd = $now->copy()->endOfWeek();

        // Current month totals
        $currentIncome = TransactionRecord::where('user_id', $userId)
            ->whereBetween('date', [$currentMonthStart, $currentMonthEnd])
            ->where('type', TransactionType::Income)
            ->sum('amount');

        $currentExpense = TransactionRecord::where('user_id', $userId)
            ->whereBetween('date', [$currentMonthStart, $currentMonthEnd])
            ->where('type', TransactionType::Expense)
            ->sum('amount');

        $currentTotal = TransactionRecord::where('user_id', $userId)
            ->whereBetween('date', [$currentMonthStart, $currentMonthEnd])
            ->count();

        // Last month totals (for comparison)
        $lastIncome = TransactionRecord::where('user_id', $userId)
            ->whereBetween('date', [$lastMonthStart, $lastMonthEnd])
            ->where('type', TransactionType::Income)
            ->sum('amount');

        $lastExpense = TransactionRecord::where('user_id', $userId)
            ->whereBetween('date', [$lastMonthStart, $lastMonthEnd])
            ->where('type', TransactionType::Expense)
            ->sum('amount');

        $lastTotal = TransactionRecord::where('user_id', $userId)
            ->whereBetween('date', [$lastMonthStart, $lastMonthEnd])
            ->count();

        // This week totals
        $weekIncome = TransactionRecord::where('user_id', $userId)
            ->whereBetween('date', [$currentWeekStart, $currentWeekEnd])
            ->where('type', TransactionType::Income)
            ->sum('amount');

        $weekExpense = TransactionRecord::where('user_id', $userId)
            ->whereBetween('date', [$currentWeekStart, $currentWeekEnd])
            ->where('type', TransactionType::Expense)
            ->sum('amount');

        $netBalance = $currentIncome - $currentExpense;

        // Percentage changes
        $incomeChange = $lastIncome > 0
            ? round((($currentIncome - $lastIncome) / $lastIncome) * 100, 1)
            : ($currentIncome > 0 ? 100 : 0);

        $expenseChange = $lastExpense > 0
            ? round((($currentExpense - $lastExpense) / $lastExpense) * 100, 1)
            : ($currentExpense > 0 ? 100 : 0);

        $totalChange = $lastTotal > 0
            ? round((($currentTotal - $lastTotal) / $lastTotal) * 100, 1)
            : ($currentTotal > 0 ? 100 : 0);

        // New customers this month
        $newCustomers = User::whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])->count();
        $lastMonthCustomers = User::whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->count();
        $customerChange = $lastMonthCustomers > 0
            ? round((($newCustomers - $lastMonthCustomers) / $lastMonthCustomers) * 100, 1)
            : ($newCustomers > 0 ? 100 : 0);

        // Daily breakdown for current month (chart data)
        $dailyData = TransactionRecord::where('user_id', $userId)
            ->whereBetween('date', [$currentMonthStart, $currentMonthEnd])
            ->select('date', 'type', DB::raw('SUM(amount) as total'))
            ->groupBy('date', 'type')
            ->orderBy('date')
            ->get()
            ->groupBy(fn($row) => $row->date->format('Y-m-d'))
            ->map(fn($group, $date) => [
                'date' => Carbon::parse($date)->format('M d'),
                'income' => round((float) $group->where('type', TransactionType::Income)->sum('total'), 2),
                'expense' => round((float) $group->where('type', TransactionType::Expense)->sum('total'), 2),
            ])
            ->values();

        // Top expense categories this month
        $topExpenses = TransactionRecord::where('user_id', $userId)
            ->whereBetween('date', [$currentMonthStart, $currentMonthEnd])
            ->where('type', TransactionType::Expense)
            ->select('category', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('category')
            ->orderByDesc('total')
            ->limit(5)
            ->get()
            ->map(fn($row) => [
                'category' => $row->category,
                'total' => number_format((float) $row->total, 2, '.', ''),
                'count' => $row->count,
                'percentage' => $currentExpense > 0
                    ? round(($row->total / $currentExpense) * 100, 1)
                    : 0,
            ]);

        // Recent transactions (last 10)
        $recentTransactions = TransactionRecord::where('user_id', $userId)
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->limit(10)
            ->get()
            ->map(fn($r) => [
                'id' => $r->id,
                'amount' => $r->amount,
                'type' => $r->type->value,
                'type_label' => $r->type->label(),
                'category' => $r->category,
                'date' => $r->date->format('Y-m-d'),
                'notes' => $r->notes,
            ]);

        return Inertia::render('Dashboard', [
            'summary' => [
                'totalTransactions' => $currentTotal,
                'totalIncome' => number_format((float) $currentIncome, 2, '.', ''),
                'totalExpense' => number_format((float) $currentExpense, 2, '.', ''),
                'netBalance' => number_format((float) $netBalance, 2, '.', ''),
                'weekIncome' => number_format((float) $weekIncome, 2, '.', ''),
                'weekExpense' => number_format((float) $weekExpense, 2, '.', ''),
                'newCustomers' => $newCustomers,
                'customerChange' => $customerChange,
                'incomeChange' => $incomeChange,
                'expenseChange' => $expenseChange,
                'totalChange' => $totalChange,
            ],
            'chartData' => $dailyData,
            'topExpenses' => $topExpenses,
            'recentTransactions' => $recentTransactions,
            'currentMonth' => $now->format('F Y'),
        ]);
    }
}
