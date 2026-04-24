<script setup lang="ts" generic="TData, TValue">
import type {
    ColumnDef,
    SortingState,
    ColumnFiltersState,
    VisibilityState,
} from '@tanstack/vue-table';
import {
    FlexRender,
    getCoreRowModel,
    getFilteredRowModel,
    getPaginationRowModel,
    getSortedRowModel,
    useVueTable,
} from '@tanstack/vue-table';
import { Search } from 'lucide-vue-next';
import { ref, computed } from 'vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';

// Props
interface DataTableProps<TData, TValue> {
    columns: ColumnDef<TData, TValue>[];
    data: TData[];
    isLoading?: boolean;
    searchable?: boolean;
    searchPlaceholder?: string;
    searchKey?: string;
    showPagination?: boolean;
    pageSize?: number;
    emptyMessage?: string;
    // For server-side pagination
    manualPagination?: boolean;
    pagination?: {
        from?: number;
        to?: number;
        total?: number;
        current_page?: number;
        last_page?: number;
        prev_page_url?: string | null;
        next_page_url?: string | null;
        links?: Array<{
            url: string | null;
            label: string;
            active: boolean;
        }>;
    };
}

const props = withDefaults(defineProps<DataTableProps<TData, TValue>>(), {
    isLoading: false,
    searchable: false,
    searchPlaceholder: 'Search...',
    showPagination: true,
    pageSize: 10,
    emptyMessage: 'No results.',
    manualPagination: false,
});

// Emits
const emit = defineEmits<{
    'update:search': [value: string];
    'page-change': [url: string];
    'next-page': [];
    'previous-page': [];
}>();

// Local state
const sorting = ref<SortingState>([]);
const columnFilters = ref<ColumnFiltersState>([]);
const columnVisibility = ref<VisibilityState>({});
const rowSelection = ref({});
const globalFilter = ref('');

// Table setup
const table = useVueTable({
    get data() {
        return props.data;
    },
    get columns() {
        return props.columns;
    },
    getCoreRowModel: getCoreRowModel(),
    getPaginationRowModel: props.manualPagination
        ? undefined
        : getPaginationRowModel(),
    getSortedRowModel: getSortedRowModel(),
    getFilteredRowModel: getFilteredRowModel(),
    onSortingChange: (updaterOrValue) => {
        sorting.value =
            typeof updaterOrValue === 'function'
                ? updaterOrValue(sorting.value)
                : updaterOrValue;
    },
    onColumnFiltersChange: (updaterOrValue) => {
        columnFilters.value =
            typeof updaterOrValue === 'function'
                ? updaterOrValue(columnFilters.value)
                : updaterOrValue;
    },
    onColumnVisibilityChange: (updaterOrValue) => {
        columnVisibility.value =
            typeof updaterOrValue === 'function'
                ? updaterOrValue(columnVisibility.value)
                : updaterOrValue;
    },
    onRowSelectionChange: (updaterOrValue) => {
        rowSelection.value =
            typeof updaterOrValue === 'function'
                ? updaterOrValue(rowSelection.value)
                : updaterOrValue;
    },
    onGlobalFilterChange: (updaterOrValue) => {
        globalFilter.value =
            typeof updaterOrValue === 'function'
                ? updaterOrValue(globalFilter.value)
                : updaterOrValue;
        emit('update:search', globalFilter.value);
    },
    state: {
        get sorting() {
            return sorting.value;
        },
        get columnFilters() {
            return columnFilters.value;
        },
        get columnVisibility() {
            return columnVisibility.value;
        },
        get rowSelection() {
            return rowSelection.value;
        },
        get globalFilter() {
            return globalFilter.value;
        },
    },
    initialState: {
        pagination: {
            pageSize: props.pageSize,
        },
    },
    manualPagination: props.manualPagination,
});

// Pagination handlers for server-side pagination
const handlePageChange = (url: string | null) => {
    if (url) {
        emit('page-change', url);
    }
};

const handleNextPage = () => {
    if (props.manualPagination) {
        emit('next-page');
    } else {
        table.nextPage();
    }
};

const handlePreviousPage = () => {
    if (props.manualPagination) {
        emit('previous-page');
    } else {
        table.previousPage();
    }
};

// Computed properties for pagination
const canPreviousPage = computed(() => {
    if (props.manualPagination) {
        return !!props.pagination?.prev_page_url;
    }
    return table.getCanPreviousPage();
});

const canNextPage = computed(() => {
    if (props.manualPagination) {
        return !!props.pagination?.next_page_url;
    }
    return table.getCanNextPage();
});
</script>

<template>
    <div class="space-y-4">
        <!-- Search Bar -->
        <div v-if="searchable" class="flex items-center justify-between">
            <div class="relative w-full max-w-sm">
                <Input
                    v-model="globalFilter"
                    :placeholder="searchPlaceholder"
                    class="pl-10"
                />
                <span
                    class="absolute inset-y-0 start-0 flex items-center justify-center px-2"
                >
                    <Search class="size-4 text-muted-foreground" />
                </span>
            </div>

            <div class="ml-4 flex items-center space-x-2">
                <slot name="search-extras" />
            </div>
        </div>

        <!-- Table -->
        <div class="rounded-md border">
            <Table>
                <TableHeader>
                    <TableRow
                        v-for="headerGroup in table.getHeaderGroups()"
                        :key="headerGroup.id"
                    >
                        <TableHead
                            v-for="header in headerGroup.headers"
                            :key="header.id"
                        >
                            <FlexRender
                                v-if="!header.isPlaceholder"
                                :render="header.column.columnDef.header"
                                :props="header.getContext()"
                            />
                        </TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <template
                        v-if="table.getRowModel().rows?.length && !isLoading"
                    >
                        <TableRow
                            v-for="row in table.getRowModel().rows"
                            :key="row.id"
                            :data-state="
                                row.getIsSelected() ? 'selected' : undefined
                            "
                        >
                            <TableCell
                                v-for="cell in row.getVisibleCells()"
                                :key="cell.id"
                            >
                                <FlexRender
                                    :render="cell.column.columnDef.cell"
                                    :props="cell.getContext()"
                                />
                            </TableCell>
                        </TableRow>
                    </template>
                    <template v-else-if="isLoading">
                        <TableRow>
                            <TableCell
                                :colspan="columns.length"
                                class="h-24 text-center"
                            >
                                Loading...
                            </TableCell>
                        </TableRow>
                    </template>
                    <template v-else>
                        <TableRow>
                            <TableCell
                                :colspan="columns.length"
                                class="h-24 text-center"
                            >
                                {{ emptyMessage }}
                            </TableCell>
                        </TableRow>
                    </template>
                </TableBody>
            </Table>
        </div>

        <!-- Pagination -->
        <div
            v-if="showPagination"
            class="flex items-center justify-between space-x-2 py-4"
        >
            <!-- Pagination Info -->
            <div class="flex-1 text-sm text-muted-foreground">
                <span v-if="manualPagination && pagination">
                    Showing {{ pagination.from }} to {{ pagination.to }} of
                    {{ pagination.total }} results
                </span>
                <span v-else>
                    Showing
                    {{
                        table.getState().pagination.pageIndex *
                            table.getState().pagination.pageSize +
                        1
                    }}
                    to
                    {{
                        Math.min(
                            (table.getState().pagination.pageIndex + 1) *
                                table.getState().pagination.pageSize,
                            table.getFilteredRowModel().rows.length,
                        )
                    }}
                    of {{ table.getFilteredRowModel().rows.length }} results
                </span>
            </div>

            <!-- Pagination Controls -->
            <div class="flex items-center space-x-2">
                <!-- Previous Button -->
                <Button
                    variant="outline"
                    size="sm"
                    :disabled="!canPreviousPage"
                    @click="handlePreviousPage"
                >
                    Previous
                </Button>

                <!-- Page Numbers (Server-side) -->
                <div
                    v-if="manualPagination && pagination?.links"
                    class="flex items-center space-x-1"
                >
                    <Button
                        v-for="link in pagination.links.slice(1, -1)"
                        :key="link.label"
                        :variant="link.active ? 'default' : 'outline'"
                        size="sm"
                        :disabled="!link.url || link.label === '...'"
                        @click="handlePageChange(link.url)"
                        class="min-w-[2rem]"
                    >
                        {{ link.label }}
                    </Button>
                </div>

                <!-- Page Numbers (Client-side) -->
                <div
                    v-else-if="!manualPagination"
                    class="flex items-center space-x-1"
                >
                    <span class="text-sm text-muted-foreground">
                        Page {{ table.getState().pagination.pageIndex + 1 }} of
                        {{ table.getPageCount() }}
                    </span>
                </div>

                <!-- Next Button -->
                <Button
                    variant="outline"
                    size="sm"
                    :disabled="!canNextPage"
                    @click="handleNextPage"
                >
                    Next
                </Button>
            </div>
        </div>
    </div>
</template>
