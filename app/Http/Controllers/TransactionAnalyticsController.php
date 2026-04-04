<?php

namespace App\Http\Controllers;

use App\Models\Enums\TransactionType;
use App\Models\TransactionRecord;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class TransactionAnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();
        $period = $request->input('period', 'month');

        $now = Carbon::now();

        $dateRange = match ($period) {
            'week' => [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()],
            'year' => [$now->copy()->startOfYear(), $now->copy()->endOfYear()],
            default => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
        };

        [$start, $end] = $dateRange;

        $baseQuery = TransactionRecord::where('user_id', $userId)
            ->whereBetween('date', [$start, $end]);

        // Summary totals
        $totalTransactions = (clone $baseQuery)->count();
        $totalIncome = (clone $baseQuery)->where('type', TransactionType::Income)->sum('amount');
        $totalExpense = (clone $baseQuery)->where('type', TransactionType::Expense)->sum('amount');
        $netBalance = $totalIncome - $totalExpense;

        // Category breakdown
        $categoryBreakdown = (clone $baseQuery)
            ->select('category', 'type', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('category', 'type')
            ->orderByDesc('total')
            ->get()
            ->map(fn($row) => [
                'category' => $row->category,
                'type' => $row->type->value,
                'type_label' => $row->type->label(),
                'total' => number_format((float) $row->total, 2, '.', ''),
                'count' => $row->count,
            ]);

        // Recent transactions (last 5)
        $recentTransactions = TransactionRecord::where('user_id', $userId)
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->limit(5)
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

        // Daily totals for the period (for chart-like display)
        $dailyTotals = (clone $baseQuery)
            ->select('date', 'type', DB::raw('SUM(amount) as total'))
            ->groupBy('date', 'type')
            ->orderBy('date')
            ->get()
            ->groupBy(fn($row) => $row->date->format('Y-m-d'))
            ->map(fn($group, $date) => [
                'date' => $date,
                'income' => number_format((float) $group->where('type', TransactionType::Income)->sum('total'), 2, '.', ''),
                'expense' => number_format((float) $group->where('type', TransactionType::Expense)->sum('total'), 2, '.', ''),
            ])
            ->values();

        // Top categories by expense
        $topExpenseCategories = (clone $baseQuery)
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
                'percentage' => $totalExpense > 0
                    ? round(($row->total / $totalExpense) * 100, 1)
                    : 0,
            ]);

        // Top categories by income
        $topIncomeCategories = (clone $baseQuery)
            ->where('type', TransactionType::Income)
            ->select('category', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('category')
            ->orderByDesc('total')
            ->limit(5)
            ->get()
            ->map(fn($row) => [
                'category' => $row->category,
                'total' => number_format((float) $row->total, 2, '.', ''),
                'count' => $row->count,
                'percentage' => $totalIncome > 0
                    ? round(($row->total / $totalIncome) * 100, 1)
                    : 0,
            ]);

        return Inertia::render('TransactionRecords/Analytics', [
            'summary' => [
                'totalTransactions' => $totalTransactions,
                'totalIncome' => number_format((float) $totalIncome, 2, '.', ''),
                'totalExpense' => number_format((float) $totalExpense, 2, '.', ''),
                'netBalance' => number_format((float) $netBalance, 2, '.', ''),
            ],
            'categoryBreakdown' => $categoryBreakdown,
            'recentTransactions' => $recentTransactions,
            'dailyTotals' => $dailyTotals,
            'topExpenseCategories' => $topExpenseCategories,
            'topIncomeCategories' => $topIncomeCategories,
            'period' => $period,
            'periodLabel' => match ($period) {
                'week' => 'This Week (' . $start->format('M d') . ' - ' . $end->format('M d, Y') . ')',
                'year' => 'This Year (' . $start->format('Y') . ')',
                default => 'This Month (' . $start->format('M Y') . ')',
            },
        ]);
    }
}
