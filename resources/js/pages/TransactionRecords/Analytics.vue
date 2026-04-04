<script setup lang="ts">
import { Head, router, setLayoutProps } from '@inertiajs/vue3';
import {
    ArrowDownRight,
    ArrowUpRight,
    DollarSign,
    TrendingDown,
    TrendingUp,
    Wallet,
} from 'lucide-vue-next';
import { computed } from 'vue';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import type { BreadcrumbItem } from '@/types';

interface Summary {
    totalTransactions: number;
    totalIncome: string;
    totalExpense: string;
    netBalance: string;
}

interface CategoryItem {
    category: string;
    type: number;
    type_label: string;
    total: string;
    count: number;
    percentage?: number;
}

interface RecentTransaction {
    id: number;
    amount: string;
    type: number;
    type_label: string;
    category: string;
    date: string;
    notes: string | null;
}

interface DailyTotal {
    date: string;
    income: string;
    expense: string;
}

const props = defineProps<{
    summary: Summary;
    categoryBreakdown: CategoryItem[];
    recentTransactions: RecentTransaction[];
    dailyTotals: DailyTotal[];
    topExpenseCategories: CategoryItem[];
    topIncomeCategories: CategoryItem[];
    period: string;
    periodLabel: string;
}>();

const breadcrumbItems: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Transaction Analytics', href: '/transaction-analytics' },
];

setLayoutProps({ breadcrumbs: breadcrumbItems });

const netIsPositive = computed(() => parseFloat(props.summary.netBalance) >= 0);

const setPeriod = (period: string) => {
    router.get(
        '/transaction-analytics',
        { period },
        { preserveState: true, replace: true },
    );
};

const maxDailyValue = computed(() => {
    let max = 0;

    for (const d of props.dailyTotals) {
        max = Math.max(max, parseFloat(d.income), parseFloat(d.expense));
    }

    return max || 1;
});

const barHeight = (value: string) => {
    const v = parseFloat(value);

    if (v === 0) {
        return '0%';
    }

    return `${Math.max((v / maxDailyValue.value) * 100, 4)}%`;
};
</script>

<template>
    <Head title="Transaction Analytics" />
    <div class="flex h-full flex-1 flex-col gap-4 p-4">
        <div
            class="flex-1 rounded-xl border border-sidebar-border/70 p-5 dark:border-sidebar-border"
        >
            <!-- Header -->
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-bold">Transaction Analytics</h1>
                    <p class="text-sm text-muted-foreground">
                        {{ periodLabel }}
                    </p>
                </div>
                <div class="flex gap-2">
                    <Button
                        :variant="period === 'week' ? 'default' : 'outline'"
                        size="sm"
                        @click="setPeriod('week')"
                    >
                        Week
                    </Button>
                    <Button
                        :variant="period === 'month' ? 'default' : 'outline'"
                        size="sm"
                        @click="setPeriod('month')"
                    >
                        Month
                    </Button>
                    <Button
                        :variant="period === 'year' ? 'default' : 'outline'"
                        size="sm"
                        @click="setPeriod('year')"
                    >
                        Year
                    </Button>
                </div>
            </div>

            <!-- Summary Cards -->
            <div
                class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4"
            >
                <Card>
                    <CardHeader
                        class="flex flex-row items-center justify-between space-y-0 pb-2"
                    >
                        <CardTitle class="text-sm font-medium">
                            Total Transactions
                        </CardTitle>
                        <DollarSign class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">
                            {{ summary.totalTransactions }}
                        </div>
                        <p class="text-xs text-muted-foreground">
                            records in this period
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader
                        class="flex flex-row items-center justify-between space-y-0 pb-2"
                    >
                        <CardTitle class="text-sm font-medium">
                            Total Income
                        </CardTitle>
                        <TrendingUp class="h-4 w-4 text-green-500" />
                    </CardHeader>
                    <CardContent>
                        <div
                            class="text-2xl font-bold text-green-600 dark:text-green-400"
                        >
                            +{{ summary.totalIncome }}
                        </div>
                        <p class="text-xs text-muted-foreground">
                            earned this period
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader
                        class="flex flex-row items-center justify-between space-y-0 pb-2"
                    >
                        <CardTitle class="text-sm font-medium">
                            Total Expenses
                        </CardTitle>
                        <TrendingDown class="h-4 w-4 text-red-500" />
                    </CardHeader>
                    <CardContent>
                        <div
                            class="text-2xl font-bold text-red-600 dark:text-red-400"
                        >
                            -{{ summary.totalExpense }}
                        </div>
                        <p class="text-xs text-muted-foreground">
                            spent this period
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader
                        class="flex flex-row items-center justify-between space-y-0 pb-2"
                    >
                        <CardTitle class="text-sm font-medium">
                            Net Balance
                        </CardTitle>
                        <Wallet class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div
                            class="text-2xl font-bold"
                            :class="
                                netIsPositive
                                    ? 'text-green-600 dark:text-green-400'
                                    : 'text-red-600 dark:text-red-400'
                            "
                        >
                            {{ netIsPositive ? '+' : ''
                            }}{{ summary.netBalance }}
                        </div>
                        <p class="text-xs text-muted-foreground">
                            income - expenses
                        </p>
                    </CardContent>
                </Card>
            </div>

            <!-- Daily Activity & Recent Transactions -->
            <div class="mb-6 grid grid-cols-1 gap-4 lg:grid-cols-3">
                <!-- Daily Activity Chart -->
                <Card class="lg:col-span-2">
                    <CardHeader>
                        <CardTitle class="text-base">Daily Activity</CardTitle>
                        <CardDescription>
                            Income vs Expense by day
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div
                            v-if="dailyTotals.length === 0"
                            class="flex h-48 items-center justify-center text-sm text-muted-foreground"
                        >
                            No transactions in this period
                        </div>
                        <div
                            v-else
                            class="flex h-48 items-end gap-1 overflow-x-auto"
                        >
                            <div
                                v-for="day in dailyTotals"
                                :key="day.date"
                                class="flex min-w-[28px] flex-1 flex-col items-center gap-0.5"
                            >
                                <div
                                    class="flex w-full items-end justify-center gap-0.5"
                                    style="height: 140px"
                                >
                                    <div
                                        class="w-2.5 rounded-t bg-green-500 transition-all"
                                        :style="{
                                            height: barHeight(day.income),
                                        }"
                                        :title="`Income: ${day.income}`"
                                    />
                                    <div
                                        class="w-2.5 rounded-t bg-red-500 transition-all"
                                        :style="{
                                            height: barHeight(day.expense),
                                        }"
                                        :title="`Expense: ${day.expense}`"
                                    />
                                </div>
                                <span class="text-[10px] text-muted-foreground">
                                    {{ day.date.slice(8) }}
                                </span>
                            </div>
                        </div>
                        <div
                            class="mt-2 flex items-center gap-4 text-xs text-muted-foreground"
                        >
                            <div class="flex items-center gap-1">
                                <div
                                    class="h-2.5 w-2.5 rounded-sm bg-green-500"
                                />
                                Income
                            </div>
                            <div class="flex items-center gap-1">
                                <div
                                    class="h-2.5 w-2.5 rounded-sm bg-red-500"
                                />
                                Expense
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Recent Transactions -->
                <Card>
                    <CardHeader>
                        <CardTitle class="text-base"
                            >Recent Transactions</CardTitle
                        >
                        <CardDescription> Last 5 records </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div
                            v-if="recentTransactions.length === 0"
                            class="py-4 text-center text-sm text-muted-foreground"
                        >
                            No transactions yet
                        </div>
                        <div v-else class="space-y-3">
                            <div
                                v-for="txn in recentTransactions"
                                :key="txn.id"
                                class="flex items-center justify-between"
                            >
                                <div class="flex items-center gap-2">
                                    <div
                                        class="flex h-8 w-8 items-center justify-center rounded-full"
                                        :class="
                                            txn.type === 1
                                                ? 'bg-green-100 dark:bg-green-900'
                                                : 'bg-red-100 dark:bg-red-900'
                                        "
                                    >
                                        <ArrowUpRight
                                            v-if="txn.type === 1"
                                            class="h-4 w-4 text-green-600 dark:text-green-400"
                                        />
                                        <ArrowDownRight
                                            v-else
                                            class="h-4 w-4 text-red-600 dark:text-red-400"
                                        />
                                    </div>
                                    <div>
                                        <p
                                            class="text-sm font-medium capitalize"
                                        >
                                            {{ txn.category }}
                                        </p>
                                        <p
                                            class="text-xs text-muted-foreground"
                                        >
                                            {{ txn.date }}
                                        </p>
                                    </div>
                                </div>
                                <span
                                    class="text-sm font-semibold"
                                    :class="
                                        txn.type === 1
                                            ? 'text-green-600 dark:text-green-400'
                                            : 'text-red-600 dark:text-red-400'
                                    "
                                >
                                    {{ txn.type === 1 ? '+' : '-'
                                    }}{{ txn.amount }}
                                </span>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Category Breakdown -->
            <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                <!-- Top Income Categories -->
                <Card>
                    <CardHeader>
                        <CardTitle class="text-base"
                            >Top Income Categories</CardTitle
                        >
                        <CardDescription>
                            Where your money comes from
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div
                            v-if="topIncomeCategories.length === 0"
                            class="py-4 text-center text-sm text-muted-foreground"
                        >
                            No income in this period
                        </div>
                        <div v-else class="space-y-3">
                            <div
                                v-for="cat in topIncomeCategories"
                                :key="cat.category"
                            >
                                <div
                                    class="mb-1 flex items-center justify-between text-sm"
                                >
                                    <span class="font-medium capitalize">
                                        {{ cat.category }}
                                    </span>
                                    <span class="text-muted-foreground">
                                        {{ cat.total }}
                                        <span class="ml-1 text-xs">
                                            ({{ cat.percentage }}%)
                                        </span>
                                    </span>
                                </div>
                                <div
                                    class="h-2 w-full overflow-hidden rounded-full bg-muted"
                                >
                                    <div
                                        class="h-full rounded-full bg-green-500 transition-all"
                                        :style="{ width: `${cat.percentage}%` }"
                                    />
                                </div>
                                <p class="mt-0.5 text-xs text-muted-foreground">
                                    {{ cat.count }} transaction{{
                                        cat.count !== 1 ? 's' : ''
                                    }}
                                </p>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Top Expense Categories -->
                <Card>
                    <CardHeader>
                        <CardTitle class="text-base"
                            >Top Expense Categories</CardTitle
                        >
                        <CardDescription>
                            Where your money goes
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div
                            v-if="topExpenseCategories.length === 0"
                            class="py-4 text-center text-sm text-muted-foreground"
                        >
                            No expenses in this period
                        </div>
                        <div v-else class="space-y-3">
                            <div
                                v-for="cat in topExpenseCategories"
                                :key="cat.category"
                            >
                                <div
                                    class="mb-1 flex items-center justify-between text-sm"
                                >
                                    <span class="font-medium capitalize">
                                        {{ cat.category }}
                                    </span>
                                    <span class="text-muted-foreground">
                                        {{ cat.total }}
                                        <span class="ml-1 text-xs">
                                            ({{ cat.percentage }}%)
                                        </span>
                                    </span>
                                </div>
                                <div
                                    class="h-2 w-full overflow-hidden rounded-full bg-muted"
                                >
                                    <div
                                        class="h-full rounded-full bg-red-500 transition-all"
                                        :style="{ width: `${cat.percentage}%` }"
                                    />
                                </div>
                                <p class="mt-0.5 text-xs text-muted-foreground">
                                    {{ cat.count }} transaction{{
                                        cat.count !== 1 ? 's' : ''
                                    }}
                                </p>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- All Category Breakdown Table -->
            <Card class="mt-4">
                <CardHeader>
                    <CardTitle class="text-base">Category Breakdown</CardTitle>
                    <CardDescription>
                        All categories with totals
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div
                        v-if="categoryBreakdown.length === 0"
                        class="py-4 text-center text-sm text-muted-foreground"
                    >
                        No transactions in this period
                    </div>
                    <div v-else class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr
                                    class="border-b text-left text-muted-foreground"
                                >
                                    <th class="pb-2 font-medium">Category</th>
                                    <th class="pb-2 font-medium">Type</th>
                                    <th class="pb-2 text-right font-medium">
                                        Count
                                    </th>
                                    <th class="pb-2 text-right font-medium">
                                        Total
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="item in categoryBreakdown"
                                    :key="`${item.category}-${item.type}`"
                                    class="border-b last:border-0"
                                >
                                    <td class="py-2 font-medium capitalize">
                                        {{ item.category }}
                                    </td>
                                    <td class="py-2">
                                        <span
                                            class="rounded-full px-2 py-0.5 text-xs font-medium"
                                            :class="
                                                item.type === 1
                                                    ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300'
                                                    : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'
                                            "
                                        >
                                            {{ item.type_label }}
                                        </span>
                                    </td>
                                    <td class="py-2 text-right">
                                        {{ item.count }}
                                    </td>
                                    <td
                                        class="py-2 text-right font-semibold"
                                        :class="
                                            item.type === 1
                                                ? 'text-green-600 dark:text-green-400'
                                                : 'text-red-600 dark:text-red-400'
                                        "
                                    >
                                        {{ item.type === 1 ? '+' : '-'
                                        }}{{ item.total }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
