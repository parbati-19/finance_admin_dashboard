<script setup lang="ts">
import {
    Head,
    setLayoutProps,
    usePage,
    router,
    useForm,
} from '@inertiajs/vue3';
import type { ColumnDef } from '@tanstack/vue-table';
import { ref, h, watch } from 'vue';
import { toast } from 'vue-sonner';
import DataTable from '@/components/DataTable.vue';
import DeleteConfirmDialog from '@/components/DeleteConfirmDialog.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import type { BreadcrumbItem } from '@/types';
import TransactionForm from './TransactionForm.vue';

interface Transaction {
    id: number;
    amount: string;
    type: number;
    type_label: string;
    category: string;
    date: string;
    notes: string | null;
    created_at: string;
}

const page = usePage();
const transactions = ref<Transaction[]>(
    (page.props as any).transactions?.data ??
        (page.props as any).transactions ??
        [],
);
const types = ref((page.props as any).types ?? []);
const filters = ref((page.props as any).filters ?? {});
const isLoading = ref(false);
const search = ref(filters.value.search ?? '');
const selectedType = ref(filters.value.type ?? 'all');

// Dialog state for create/edit
const dialogOpen = ref(false);
const dialogMode = ref<'create' | 'edit'>('create');
const dialogTransaction = ref<any | null>(null);

// Delete dialog state
const deleteDialogOpen = ref(false);
const transactionToDelete = ref<number | null>(null);
const deleteForm = useForm({});

const breadcrumbItems: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Transactions', href: '/transaction-records' },
];

setLayoutProps({ breadcrumbs: breadcrumbItems });

const columns: ColumnDef<Transaction>[] = [
    {
        accessorKey: 'id',
        header: '#',
        cell: ({ row }) =>
            h('div', { class: 'font-medium' }, row.getValue('id')),
    },
    {
        accessorKey: 'date',
        header: 'Date',
    },
    {
        accessorKey: 'type_label',
        header: 'Type',
        cell: ({ row }) => {
            const type = row.original.type;
            const label = row.getValue('type_label') as string;

            return h(
                'div',
                {
                    class: `px-2 py-1 rounded-full text-xs font-medium w-fit ${
                        type === 1
                            ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300'
                            : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'
                    }`,
                },
                label,
            );
        },
    },
    {
        accessorKey: 'category',
        header: 'Category',
        cell: ({ row }) =>
            h(
                'div',
                {
                    class: 'capitalize px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300 w-fit',
                },
                row.getValue('category'),
            ),
    },
    {
        accessorKey: 'amount',
        header: 'Amount',
        cell: ({ row }) => {
            const type = row.original.type;
            const amount = row.getValue('amount') as string;

            return h(
                'div',
                {
                    class: `font-semibold ${type === 1 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'}`,
                },
                `${type === 1 ? '+' : '-'}${amount}`,
            );
        },
    },
    {
        accessorKey: 'notes',
        header: 'Notes',
        cell: ({ row }) => {
            const notes = row.getValue('notes') as string | null;

            return h(
                'div',
                {
                    class: 'max-w-[200px] truncate text-muted-foreground text-sm',
                },
                notes || '—',
            );
        },
    },
    {
        id: 'actions',
        header: 'Actions',
        cell: ({ row }) => {
            const t = row.original;

            return h('div', { class: 'flex gap-2' }, [
                h(
                    Button,
                    {
                        variant: 'outline',
                        size: 'sm',
                        onClick: () => editTransaction(t),
                    },
                    () => 'Edit',
                ),
                h(
                    Button,
                    {
                        variant: 'destructive',
                        size: 'sm',
                        onClick: () => confirmDelete(t.id),
                    },
                    () => 'Delete',
                ),
            ]);
        },
    },
];

// Keep data in sync with Inertia page props
watch(
    () => (page.props as any).transactions,
    (val) => {
        transactions.value = val?.data ?? val ?? [];
    },
);

// Flash messages
watch(
    () => (page.props as any).flash?.success,
    (val) => {
        if (val) {
            toast.success(val);
        }
    },
);

const setTypeFilter = (type: string) => {
    selectedType.value = type;
    router.get(
        '/transaction-records',
        {
            type: type === 'all' ? undefined : type,
            search: search.value || undefined,
        },
        { preserveState: true, replace: true },
    );
};

const openCreateDialog = () => {
    dialogMode.value = 'create';
    dialogTransaction.value = null;
    dialogOpen.value = true;
};

const editTransaction = (t: Transaction) => {
    dialogMode.value = 'edit';
    dialogTransaction.value = { ...t };
    dialogOpen.value = true;
};

const confirmDelete = (id: number) => {
    transactionToDelete.value = id;
    deleteDialogOpen.value = true;
};

const handleDeleteConfirm = async () => {
    if (!transactionToDelete.value) {
        return;
    }

    await deleteForm.delete(
        `/transaction-records/${transactionToDelete.value}`,
        {
            onSuccess: () => {
                deleteDialogOpen.value = false;
                transactionToDelete.value = null;
                toast.success('Transaction deleted successfully');
                router.reload();
            },
            onError: (errors) => {
                const msgs = Object.values(errors).flat().join('\n');
                toast.error(msgs || 'Error deleting transaction');
            },
        },
    );
};

const handleSaved = (message?: string) => {
    dialogOpen.value = false;
    router.reload();
};

const handleDeleteCancel = () => {
    deleteDialogOpen.value = false;
    transactionToDelete.value = null;
};

// Search
let searchTimeout: ReturnType<typeof setTimeout>;
watch(search, (val) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get(
            '/transaction-records',
            {
                search: val || undefined,
                type:
                    selectedType.value === 'all'
                        ? undefined
                        : selectedType.value,
            },
            { preserveState: true, replace: true },
        );
    }, 300);
});

// Pagination
const handleTablePageChange = (url: string) => {
    if (!url) {
        return;
    }

    router.visit(url, { preserveState: true, replace: true });
};

const handleTableNext = () => {
    const next = (page.props as any).transactions?.next_page_url;

    if (next) {
        router.visit(next, { preserveState: true, replace: true });
    }
};

const handleTablePrevious = () => {
    const prev = (page.props as any).transactions?.prev_page_url;

    if (prev) {
        router.visit(prev, { preserveState: true, replace: true });
    }
};
</script>

<template>
    <Head title="Transaction Records" />
    <div class="flex h-full flex-1 flex-col gap-4 p-4 md:flex-row">
        <!-- Sidebar -->
        <div
            class="h-fit w-full rounded-xl border border-gray-200 bg-white md:w-64 dark:border-neutral-800 dark:bg-neutral-900"
        >
            <div class="border-b border-gray-200 p-4 dark:border-neutral-700">
                <h2 class="text-lg font-semibold">Transactions</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Manage your records
                </p>
            </div>
            <div class="p-4">
                <Button
                    variant="outline"
                    class="w-full justify-start"
                    @click="openCreateDialog"
                >
                    <span class="mr-2">+</span>
                    New Transaction
                </Button>
            </div>
            <div class="space-y-1 px-4 pb-4">
                <Button
                    variant="ghost"
                    class="w-full justify-start"
                    :class="selectedType === 'all' ? 'bg-accent' : ''"
                    @click="setTypeFilter('all')"
                >
                    All
                </Button>
                <Button
                    variant="ghost"
                    class="w-full justify-start"
                    :class="selectedType === '1' ? 'bg-accent' : ''"
                    @click="setTypeFilter('1')"
                >
                    Income
                </Button>
                <Button
                    variant="ghost"
                    class="w-full justify-start"
                    :class="selectedType === '2' ? 'bg-accent' : ''"
                    @click="setTypeFilter('2')"
                >
                    Expense
                </Button>
            </div>
        </div>

        <!-- Main Content -->
        <div
            class="flex-1 rounded-xl border border-sidebar-border/70 p-5 dark:border-sidebar-border"
        >
            <div class="mb-4 flex items-center justify-between py-2">
                <h1 class="text-xl font-bold">Transaction Records</h1>
            </div>

            <DataTable
                :columns="columns"
                :data="transactions"
                :is-loading="isLoading"
                searchable
                search-placeholder="Search by category or notes..."
                :show-pagination="true"
                :page-size="10"
                empty-message="No transactions found."
                :manual-pagination="true"
                :pagination="(page.props as any).transactions"
                @update:search="(val) => (search = val)"
                @page-change="(url) => handleTablePageChange(url)"
                @next-page="() => handleTableNext()"
                @previous-page="() => handleTablePrevious()"
            />
        </div>
    </div>

    <!-- Create/Edit Dialog -->
    <Dialog v-model:open="dialogOpen">
        <DialogContent class="sm:max-w-lg">
            <DialogHeader>
                <DialogTitle>
                    {{
                        dialogMode === 'create'
                            ? 'New Transaction'
                            : 'Edit Transaction'
                    }}
                </DialogTitle>
            </DialogHeader>
            <TransactionForm
                :transaction="dialogTransaction"
                :types="types"
                @saved="handleSaved"
            />
        </DialogContent>
    </Dialog>

    <!-- Delete Confirmation Dialog -->
    <DeleteConfirmDialog
        v-model:open="deleteDialogOpen"
        title="Delete Transaction"
        description="Are you sure you want to delete this transaction? This action can be undone."
        :is-loading="deleteForm.processing"
        @confirm="handleDeleteConfirm"
        @cancel="handleDeleteCancel"
    />
</template>
