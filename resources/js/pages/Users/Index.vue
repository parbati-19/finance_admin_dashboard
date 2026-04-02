<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import type {
    ColumnDef,
  ColumnFiltersState,
  ExpandedState,
  SortingState,
  VisibilityState,
} from '@tanstack/vue-table'
import type{ User } from '@/types';
import {
  FlexRender,
  getCoreRowModel,
  getExpandedRowModel,
  getFilteredRowModel,
  getPaginationRowModel,
  getSortedRowModel,
  useVueTable,
} from '@tanstack/vue-table'
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table';
import { ref } from 'process';
import { createReusableTemplate } from '@vueuse/core';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Users', href: '/users' },
        ],
    },
});

const props = defineProps<{
    users: {
        data: User[];
    };
}>();

const [DefineTemplate, ReuseTemplate] = createReusableTemplate<{
  payment: {
    id: string
  }
  onExpand: () => void
}>()
const columns: ColumnDef<User>[] = [
  {
    id: 'select',
    header: ({ table }) => h(Checkbox, {
      'modelValue': table.getIsAllPageRowsSelected() || (table.getIsSomePageRowsSelected() && 'indeterminate'),
      'onUpdate:modelValue': value => table.toggleAllPageRowsSelected(!!value),
      'ariaLabel': 'Select all',
    }),
    cell: ({ row }) => h(Checkbox, {
      'modelValue': row.getIsSelected(),
      'onUpdate:modelValue': value => row.toggleSelected(!!value),
      'ariaLabel': 'Select row',
    }),
    enableSorting: false,
    enableHiding: false,
  },
  {
    accessorKey: 'status',
    header: 'Status',
    cell: ({ row }) => h('div', { class: 'capitalize' }, row.getValue('status')),
  },
  {
    accessorKey: 'email',
    header: ({ column }) => {
      return h(Button, {
        variant: 'ghost',
        onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
      }, () => ['Email', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })])
    },
    cell: ({ row }) => h('div', { class: 'lowercase' }, row.getValue('email')),
  },
  {
    accessorKey: 'amount',
    header: () => h('div', { class: 'text-right' }, 'Amount'),
    cell: ({ row }) => {
      const amount = Number.parseFloat(row.getValue('amount'))
      // Format the amount as a dollar amount
      const formatted = new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
      }).format(amount)
      return h('div', { class: 'text-right font-medium' }, formatted)
    },
  },
  {
    id: 'actions',
    enableHiding: false,
    cell: ({ row }) => {
      const payment = row.original
      return h(ReuseTemplate, {
        payment,
        onExpand: row.toggleExpanded,
      })
    },
  },
]
const sorting = ref<SortingState>([])
const columnFilters = ref<ColumnFiltersState>([])
const columnVisibility = ref<VisibilityState>({})
const rowSelection = ref({})
const expanded = ref<ExpandedState>({})
const table = useVueTable({
  data,
  columns,
  getCoreRowModel: getCoreRowModel(),
  getPaginationRowModel: getPaginationRowModel(),
  getSortedRowModel: getSortedRowModel(),
  getFilteredRowModel: getFilteredRowModel(),
  getExpandedRowModel: getExpandedRowModel(),
  onSortingChange: updaterOrValue => valueUpdater(updaterOrValue, sorting),
  onColumnFiltersChange: updaterOrValue => valueUpdater(updaterOrValue, columnFilters),
  onColumnVisibilityChange: updaterOrValue => valueUpdater(updaterOrValue, columnVisibility),
  onRowSelectionChange: updaterOrValue => valueUpdater(updaterOrValue, rowSelection),
  onExpandedChange: updaterOrValue => valueUpdater(updaterOrValue, expanded),
  state: {
    get sorting() { return sorting.value },
    get columnFilters() { return columnFilters.value },
    get columnVisibility() { return columnVisibility.value },
    get rowSelection() { return rowSelection.value },
    get expanded() { return expanded.value },
  },
})
function copy(id: string) {
  navigator.clipboard.writeText(id)
}
</script>

<template>
    <Head title="Users Management" />
    <div
        class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
    >
        <div class="flex flex-col gap-4 py-2 px-4 md:gap-4 md:py-4">
            <h1 class="text-2xl font-semibold text-gray-900">
                Users Management
            </h1>
            <p class="text-sm text-gray-600">
                Manage your application users here.
            </p>

            <div class="overflow-hidden rounded-lg border">
                <Table class="w-full border-collapse bg-white text-left text-sm text-gray-500">
                    <TableHead class="bg-gray-50">
                        <TableRow>
                            <TableHeader class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Name
                            </TableHeader>
                            <TableHeader class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Email
                            </TableHeader>
                            <TableHeader class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Role
                            </TableHeader>
                        </TableRow>
                    </TableHead>
                    <TableBody class="bg-white divide-y divide-gray-200">
                        <TableRow v-for="user in props.users.data" :key="user.id">
                            <TableCell class="px-6 py-4 whitespace-nowrap">
                                {{ user.name }}
                            </TableCell>
                            <TableCell class="px-6 py-4 whitespace-nowrap">
                                {{ user.email }}
                            </TableCell>
                            <TableCell class="px-6 py-4 whitespace-nowrap">
                                {{ user.role }}
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
        </div>
    </div>
</template>
