<script setup lang="ts">
import { Head, setLayoutProps } from '@inertiajs/vue3';
import {
    ArrowDownRight,
    ArrowUpRight,
    TrendingDown,
    TrendingUp,
} from 'lucide-vue-next';
import { computed } from 'vue';
import type { BreadcrumbItem } from '@/types';

interface Summary {
    totalTransactions: number;
    totalIncome: string;
    totalExpense: string;
    netBalance: string;
    weekIncome: string;
    weekExpense: string;
    newCustomers: number;
    customerChange: number;
    incomeChange: number;
    expenseChange: number;
    totalChange: number;
}

interface ChartDataPoint {
    date: string;
    income: number;
    expense: number;
}

interface TopExpense {
    category: string;
    total: string;
    count: number;
    percentage: number;
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

interface StatCard {
    title: string;
    value: string;
    change: string;
    trend: 'up' | 'down';
    description: string;
    footer: string;
}

const props = defineProps<{
    summary: Summary;
    chartData: ChartDataPoint[];
    topExpenses: TopExpense[];
    recentTransactions: RecentTransaction[];
    currentMonth: string;
}>();

const breadcrumbItems: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
];

setLayoutProps({ breadcrumbs: breadcrumbItems });

const { currentMonth } = props;

const formatChange = (val: number) => {
    const sign = val >= 0 ? '+' : '';

    return `${sign}${val}%`;
};

const stats = computed<StatCard[]>(() => {
    const netBalance = parseFloat(props.summary.netBalance);

    return [
        {
            title: 'New Customers',
            value: `${props.summary.newCustomers}`,
            change: formatChange(props.summary.customerChange),
            trend: props.summary.customerChange >= 0 ? 'up' : 'down',
            description:
                props.summary.customerChange >= 0
                    ? 'Growing this month'
                    : 'Down this month',
            footer: `${currentMonth} registrations`,
        },
        {
            title: 'Total Expenses',
            value: `$${props.summary.totalExpense}`,
            change: formatChange(props.summary.expenseChange),
            trend: props.summary.expenseChange <= 0 ? 'up' : 'down',
            description:
                props.summary.expenseChange <= 0
                    ? 'Spending decreased'
                    : 'Spending increased',
            footer: `Weekly expenses: $${props.summary.weekExpense}`,
        },
        {
            title: 'Net Balance',
            value: `$${props.summary.netBalance}`,
            change: formatChange(
                props.summary.incomeChange - props.summary.expenseChange,
            ),
            trend: netBalance >= 0 ? 'up' : 'down',
            description:
                netBalance >= 0 ? 'Positive cash flow' : 'Negative cash flow',
            footer: `${props.summary.totalTransactions} transactions this month`,
        },
        {
            title: 'Transactions',
            value: `${props.summary.totalTransactions}`,
            change: formatChange(props.summary.totalChange),
            trend: props.summary.totalChange >= 0 ? 'up' : 'down',
            description:
                props.summary.totalChange >= 0
                    ? 'Activity is increasing'
                    : 'Activity is decreasing',
            footer: `${currentMonth} overview`,
        },
    ];
});

const chartHeight = 220;
const chartPadding = 20;

const maxChartValue = computed(() => {
    let max = 0;

    for (const d of props.chartData) {
        max = Math.max(max, d.income, d.expense);
    }

    return max || 1;
});

const chartWidth = computed(() => {
    return Math.max(props.chartData.length * 40, 600);
});

const buildPath = (key: 'income' | 'expense') => {
    const len = props.chartData.length;

    if (len === 0) {
        return '';
    }

    const stepX = (chartWidth.value - chartPadding * 2) / Math.max(len - 1, 1);
    const points = props.chartData.map((d, i) => {
        const x = chartPadding + i * stepX;
        const y =
            chartHeight -
            chartPadding -
            (d[key] / maxChartValue.value) * (chartHeight - chartPadding * 2);

        return { x, y };
    });

    if (len === 1) {
        return `M ${points[0].x} ${points[0].y} L ${points[0].x} ${points[0].y}`;
    }

    let path = `M ${points[0].x} ${points[0].y}`;

    for (let i = 0; i < points.length - 1; i++) {
        const cp1x = points[i].x + stepX * 0.4;
        const cp1y = points[i].y;
        const cp2x = points[i + 1].x - stepX * 0.4;
        const cp2y = points[i + 1].y;

        path += ` C ${cp1x} ${cp1y}, ${cp2x} ${cp2y}, ${points[i + 1].x} ${points[i + 1].y}`;
    }

    return path;
};

const buildAreaPath = (key: 'income' | 'expense') => {
    const line = buildPath(key);

    if (!line) {
        return '';
    }

    const len = props.chartData.length;
    const stepX = (chartWidth.value - chartPadding * 2) / Math.max(len - 1, 1);
    const lastX = chartPadding + (len - 1) * stepX;
    const bottom = chartHeight - chartPadding;

    return `${line} L ${lastX} ${bottom} L ${chartPadding} ${bottom} Z`;
};
</script>

<template>
    <Head title="Dashboard" />

    <div
        class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
    >
        <div class="flex flex-col gap-4 py-4 md:gap-6 md:py-6">
            <!-- Stats Grid -->
            <div
                class="grid grid-cols-1 gap-4 px-4 lg:px-6 xl:grid-cols-2 2xl:grid-cols-4"
            >
                <div
                    v-for="stat in stats"
                    :key="stat.title"
                    class="flex flex-col gap-6 rounded-xl border bg-card py-6 shadow-sm"
                >
                    <div class="grid grid-cols-[1fr_auto] gap-1.5 px-6">
                        <p class="text-sm text-muted-foreground">
                            {{ stat.title }}
                        </p>
                        <h3
                            class="text-2xl font-semibold tabular-nums sm:text-3xl"
                        >
                            {{ stat.value }}
                        </h3>
                        <div
                            class="col-start-2 row-span-2 row-start-1 self-start justify-self-end"
                        >
                            <div
                                class="inline-flex items-center gap-1 rounded-full border px-2 py-0.5 text-xs font-medium"
                                :class="
                                    stat.trend === 'up'
                                        ? 'text-green-600'
                                        : 'text-red-600'
                                "
                            >
                                <TrendingUp
                                    v-if="stat.trend === 'up'"
                                    class="h-4 w-4"
                                />
                                <TrendingDown v-else class="h-4 w-4" />
                                {{ stat.change }}
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col gap-1.5 px-6 text-sm">
                        <div class="flex items-center gap-2 font-medium">
                            {{ stat.description }}
                            <TrendingUp
                                v-if="stat.trend === 'up'"
                                class="h-4 w-4"
                            />
                            <TrendingDown v-else class="h-4 w-4" />
                        </div>
                        <div class="text-muted-foreground">
                            {{ stat.footer }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chart Card -->
            <div class="px-4 lg:px-6">
                <div
                    class="flex flex-col gap-6 rounded-xl border bg-card py-6 shadow-sm"
                >
                    <div class="flex items-start justify-between px-6">
                        <div>
                            <h3 class="leading-none font-semibold">
                                Daily Overview
                            </h3>
                            <p class="mt-1.5 text-sm text-muted-foreground">
                                Income vs Expenses &middot; {{ currentMonth }}
                            </p>
                        </div>
                        <div
                            class="flex items-center gap-4 text-xs text-muted-foreground"
                        >
                            <div class="flex items-center gap-1.5">
                                <div
                                    class="h-2.5 w-2.5 rounded-sm bg-green-500"
                                />
                                Income
                            </div>
                            <div class="flex items-center gap-1.5">
                                <div
                                    class="h-2.5 w-2.5 rounded-sm bg-red-500"
                                />
                                Expense
                            </div>
                        </div>
                    </div>
                    <div class="px-6">
                        <div
                            v-if="chartData.length === 0"
                            class="flex h-[250px] items-center justify-center rounded-lg border border-dashed"
                        >
                            <p class="text-sm text-muted-foreground">
                                No transactions this month
                            </p>
                        </div>
                        <div v-else class="overflow-x-auto">
                            <svg
                                :viewBox="`0 0 ${chartWidth} ${chartHeight}`"
                                :width="chartWidth"
                                :height="chartHeight"
                                class="min-w-full"
                                preserveAspectRatio="none"
                            >
                                <defs>
                                    <linearGradient
                                        id="incomeGrad"
                                        x1="0"
                                        y1="0"
                                        x2="0"
                                        y2="1"
                                    >
                                        <stop
                                            offset="0%"
                                            stop-color="rgb(34 197 94)"
                                            stop-opacity="0.3"
                                        />
                                        <stop
                                            offset="100%"
                                            stop-color="rgb(34 197 94)"
                                            stop-opacity="0.02"
                                        />
                                    </linearGradient>
                                    <linearGradient
                                        id="expenseGrad"
                                        x1="0"
                                        y1="0"
                                        x2="0"
                                        y2="1"
                                    >
                                        <stop
                                            offset="0%"
                                            stop-color="rgb(239 68 68)"
                                            stop-opacity="0.3"
                                        />
                                        <stop
                                            offset="100%"
                                            stop-color="rgb(239 68 68)"
                                            stop-opacity="0.02"
                                        />
                                    </linearGradient>
                                </defs>

                                <!-- Grid lines -->
                                <g>
                                    <line
                                        v-for="i in 4"
                                        :key="`grid-${i}`"
                                        :x1="chartPadding"
                                        :x2="chartWidth - chartPadding"
                                        :y1="
                                            chartPadding +
                                            ((chartHeight - chartPadding * 2) /
                                                4) *
                                                (i - 1)
                                        "
                                        :y2="
                                            chartPadding +
                                            ((chartHeight - chartPadding * 2) /
                                                4) *
                                                (i - 1)
                                        "
                                        stroke="currentColor"
                                        stroke-opacity="0.08"
                                        stroke-dasharray="4 4"
                                    />
                                </g>

                                <!-- Income area -->
                                <path
                                    :d="buildAreaPath('income')"
                                    fill="url(#incomeGrad)"
                                />
                                <!-- Income line -->
                                <path
                                    :d="buildPath('income')"
                                    fill="none"
                                    stroke="rgb(34 197 94)"
                                    stroke-width="2.5"
                                    stroke-linecap="round"
                                />

                                <!-- Expense area -->
                                <path
                                    :d="buildAreaPath('expense')"
                                    fill="url(#expenseGrad)"
                                />
                                <!-- Expense line -->
                                <path
                                    :d="buildPath('expense')"
                                    fill="none"
                                    stroke="rgb(239 68 68)"
                                    stroke-width="2.5"
                                    stroke-linecap="round"
                                />

                                <!-- Date labels -->
                                <g>
                                    <text
                                        v-for="(day, i) in chartData"
                                        :key="`label-${day.date}`"
                                        :x="
                                            chartPadding +
                                            i *
                                                ((chartWidth -
                                                    chartPadding * 2) /
                                                    Math.max(
                                                        chartData.length - 1,
                                                        1,
                                                    ))
                                        "
                                        :y="chartHeight - 4"
                                        text-anchor="middle"
                                        class="fill-muted-foreground"
                                        style="font-size: 10px"
                                    >
                                        {{ day.date.split(' ')[1] }}
                                    </text>
                                </g>

                                <!-- Dots: income -->
                                <g>
                                    <circle
                                        v-for="(day, i) in chartData"
                                        :key="`inc-${day.date}`"
                                        :cx="
                                            chartPadding +
                                            i *
                                                ((chartWidth -
                                                    chartPadding * 2) /
                                                    Math.max(
                                                        chartData.length - 1,
                                                        1,
                                                    ))
                                        "
                                        :cy="
                                            chartHeight -
                                            chartPadding -
                                            (day.income / maxChartValue) *
                                                (chartHeight - chartPadding * 2)
                                        "
                                        r="3"
                                        fill="rgb(34 197 94)"
                                        class="opacity-0 transition-opacity hover:opacity-100"
                                    >
                                        <title>
                                            {{ day.date }}: ${{ day.income }}
                                        </title>
                                    </circle>
                                </g>

                                <!-- Dots: expense -->
                                <g>
                                    <circle
                                        v-for="(day, i) in chartData"
                                        :key="`exp-${day.date}`"
                                        :cx="
                                            chartPadding +
                                            i *
                                                ((chartWidth -
                                                    chartPadding * 2) /
                                                    Math.max(
                                                        chartData.length - 1,
                                                        1,
                                                    ))
                                        "
                                        :cy="
                                            chartHeight -
                                            chartPadding -
                                            (day.expense / maxChartValue) *
                                                (chartHeight - chartPadding * 2)
                                        "
                                        r="3"
                                        fill="rgb(239 68 68)"
                                        class="opacity-0 transition-opacity hover:opacity-100"
                                    >
                                        <title>
                                            {{ day.date }}: ${{ day.expense }}
                                        </title>
                                    </circle>
                                </g>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Transactions Table -->
            <div class="flex flex-col gap-6">
                <div class="flex items-center justify-between px-4 lg:px-6">
                    <h3 class="font-semibold">Recent Transactions</h3>
                    <p class="text-sm text-muted-foreground">
                        Last 10 transactions
                    </p>
                </div>

                <div class="mx-4 overflow-hidden rounded-lg border lg:mx-6">
                    <div class="relative w-full overflow-auto">
                        <table class="w-full caption-bottom text-sm">
                            <thead class="sticky top-0 z-10 border-b bg-muted">
                                <tr
                                    class="border-b transition-colors hover:bg-muted/50"
                                >
                                    <th
                                        class="h-10 px-4 text-left align-middle font-medium"
                                    >
                                        Date
                                    </th>
                                    <th
                                        class="h-10 px-4 text-left align-middle font-medium"
                                    >
                                        Category
                                    </th>
                                    <th
                                        class="h-10 px-4 text-left align-middle font-medium"
                                    >
                                        Type
                                    </th>
                                    <th
                                        class="h-10 px-4 text-left align-middle font-medium"
                                    >
                                        Notes
                                    </th>
                                    <th
                                        class="h-10 px-4 text-right align-middle font-medium"
                                    >
                                        Amount
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-if="recentTransactions.length === 0"
                                    class="border-b transition-colors"
                                >
                                    <td
                                        colspan="5"
                                        class="p-8 text-center text-sm text-muted-foreground"
                                    >
                                        No transactions yet. Start by adding
                                        one!
                                    </td>
                                </tr>
                                <tr
                                    v-for="txn in recentTransactions"
                                    v-else
                                    :key="txn.id"
                                    class="border-b transition-colors hover:bg-muted/50"
                                >
                                    <td
                                        class="p-4 align-middle text-muted-foreground"
                                    >
                                        {{ txn.date }}
                                    </td>
                                    <td
                                        class="p-4 align-middle font-medium capitalize"
                                    >
                                        {{ txn.category }}
                                    </td>
                                    <td class="p-4 align-middle">
                                        <span
                                            class="inline-flex items-center gap-1 rounded-full border px-2 py-0.5 text-xs font-medium"
                                            :class="
                                                txn.type === 1
                                                    ? 'text-green-600'
                                                    : 'text-red-600'
                                            "
                                        >
                                            <ArrowUpRight
                                                v-if="txn.type === 1"
                                                class="h-3 w-3"
                                            />
                                            <ArrowDownRight
                                                v-else
                                                class="h-3 w-3"
                                            />
                                            {{ txn.type_label }}
                                        </span>
                                    </td>
                                    <td
                                        class="p-4 align-middle text-muted-foreground"
                                    >
                                        {{ txn.notes ?? '—' }}
                                    </td>
                                    <td
                                        class="p-4 text-right align-middle font-semibold tabular-nums"
                                        :class="
                                            txn.type === 1
                                                ? 'text-green-600'
                                                : 'text-red-600'
                                        "
                                    >
                                        {{ txn.type === 1 ? '+' : '-' }}${{
                                            txn.amount
                                        }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
