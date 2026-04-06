<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TransactionRecordResource;
use App\Models\Enums\TransactionType;
use App\Models\TransactionRecord;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(): JsonResponse
    {
        $userId = Auth::id();
        $now = Carbon::now();

        $currentMonthStart = $now->copy()->startOfMonth();
        $currentMonthEnd = $now->copy()->endOfMonth();
        $lastMonthStart = $now->copy()->subMonth()->startOfMonth();
        $lastMonthEnd = $now->copy()->subMonth()->endOfMonth();
        $currentWeekStart = $now->copy()->startOfWeek();
        $currentWeekEnd = $now->copy()->endOfWeek();

        $currentIncome = TransactionRecord::where('user_id', $userId)
            ->whereBetween('date', [$currentMonthStart, $currentMonthEnd])
            ->where('type', TransactionType::Income)->sum('amount');

        $currentExpense = TransactionRecord::where('user_id', $userId)
            ->whereBetween('date', [$currentMonthStart, $currentMonthEnd])
            ->where('type', TransactionType::Expense)->sum('amount');

        $currentTotal = TransactionRecord::where('user_id', $userId)
            ->whereBetween('date', [$currentMonthStart, $currentMonthEnd])->count();

        $lastIncome = TransactionRecord::where('user_id', $userId)
            ->whereBetween('date', [$lastMonthStart, $lastMonthEnd])
            ->where('type', TransactionType::Income)->sum('amount');

        $lastExpense = TransactionRecord::where('user_id', $userId)
            ->whereBetween('date', [$lastMonthStart, $lastMonthEnd])
            ->where('type', TransactionType::Expense)->sum('amount');

        $lastTotal = TransactionRecord::where('user_id', $userId)
            ->whereBetween('date', [$lastMonthStart, $lastMonthEnd])->count();

        $weekIncome = TransactionRecord::where('user_id', $userId)
            ->whereBetween('date', [$currentWeekStart, $currentWeekEnd])
            ->where('type', TransactionType::Income)->sum('amount');

        $weekExpense = TransactionRecord::where('user_id', $userId)
            ->whereBetween('date', [$currentWeekStart, $currentWeekEnd])
            ->where('type', TransactionType::Expense)->sum('amount');

        $netBalance = $currentIncome - $currentExpense;

        $incomeChange = $lastIncome > 0
            ? round((($currentIncome - $lastIncome) / $lastIncome) * 100, 1)
            : ($currentIncome > 0 ? 100 : 0);

        $expenseChange = $lastExpense > 0
            ? round((($currentExpense - $lastExpense) / $lastExpense) * 100, 1)
            : ($currentExpense > 0 ? 100 : 0);

        $totalChange = $lastTotal > 0
            ? round((($currentTotal - $lastTotal) / $lastTotal) * 100, 1)
            : ($currentTotal > 0 ? 100 : 0);

        $newCustomers = User::whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])->count();
        $lastMonthCustomers = User::whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->count();
        $customerChange = $lastMonthCustomers > 0
            ? round((($newCustomers - $lastMonthCustomers) / $lastMonthCustomers) * 100, 1)
            : ($newCustomers > 0 ? 100 : 0);

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
                    ? round(($row->total / $currentExpense) * 100, 1) : 0,
            ]);

        $recentTransactions = TransactionRecordResource::collection(
            TransactionRecord::where('user_id', $userId)
                ->orderByDesc('date')
                ->orderByDesc('id')
                ->limit(10)
                ->get()
        );

        return response()->json([
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
            'dailyData' => $dailyData,
            'topExpenses' => $topExpenses,
            'recentTransactions' => $recentTransactions,
        ]);
    }
}
