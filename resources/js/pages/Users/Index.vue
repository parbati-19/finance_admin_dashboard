<script setup lang="ts">
import {
    Head,
    router,
    setLayoutProps,
    useForm,
    usePage,
} from '@inertiajs/vue3';
import type { ColumnDef } from '@tanstack/vue-table';
import { ref, h, watch } from 'vue';
import { toast } from 'vue-sonner';
import DataTable from '@/components/DataTable.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
    DialogFooter,
} from '@/components/ui/dialog';
import type { BreadcrumbItem, User } from '@/types';
import UserForm from './UserForm.vue';
import DeleteConfirmDialog from '@/components/DeleteConfirmDialog.vue';

const page = usePage();
const users = ref<User[]>(
    (page.props as any).users?.data ?? (page.props as any).users ?? [],
);
const roles = ref<string[]>((page.props as any).roles ?? []);
const isLoading = ref(false);
const search = ref('');
const selectedStatus = ref('all');

// Delete dialog state
const deleteDialogOpen = ref(false);
const userToDelete = ref<number | null>(null);
const isPermanentDelete = ref(false);

// Create form for deletion
const deleteForm = useForm({});

// Dialog state for create/edit user form
const userDialogOpen = ref(false);
const dialogMode = ref<'create' | 'edit'>('create');
const dialogUser = ref<any | null>(null);

const breadcrumbItems: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Users', href: '/users' },
];

// Pass breadcrumbs to the global layout via Inertia
setLayoutProps({ breadcrumbs: breadcrumbItems });

// Define your columns
const columns: ColumnDef<User>[] = [
    {
        accessorKey: 'id',
        header: '#',
        cell: ({ row }) =>
            h('div', { class: 'font-medium' }, row.getValue('id')),
    },
    {
        accessorKey: 'name',
        header: 'Name',
        cell: ({ row }) =>
            h('div', { class: 'font-medium' }, row.getValue('name')),
    },
    {
        accessorKey: 'email',
        header: 'Email',
    },
    {
        accessorKey: 'role',
        header: 'Role',
        cell: ({ row }) => {
            const role = row.getValue('role') as string;

            return h(
                'div',
                {
                    class: 'capitalize px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300 w-fit',
                },
                role,
            );
        },
    },
    {
        accessorKey: 'status',
        header: 'Status',
        cell: ({ row }) => {
            const status = row.getValue('status') as number;

            return h(
                'div',
                {
                    class: `px-2 py-1 rounded-full text-xs font-medium w-fit ${
                        status === 1
                            ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300'
                            : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'
                    }`,
                },
                status === 1 ? 'Active' : 'Inactive',
            );
        },
    },
    {
        id: 'actions',
        header: 'Actions',
        cell: ({ row }) => {
            const user = row.original;
            const isDeleted = selectedStatus.value === 'deleted';

            if (isDeleted) {
                return h(
                    'div',
                    { class: 'flex justify-start items-center gap-2' },
                    [
                        h(
                            Button,
                            {
                                variant: 'outline',
                                size: 'sm',
                                onClick: () => restoreUser(user.id),
                            },
                            () => 'Restore',
                        ),
                        h(
                            Button,
                            {
                                variant: 'destructive',
                                size: 'sm',
                                onClick: () => permanentDeleteUser(user.id),
                            },
                            () => 'Delete Permanently',
                        ),
                    ],
                );
            }

            return h(
                'div',
                { class: 'flex justify-start items-center gap-2' },
                [
                    h(
                        Button,
                        {
                            variant: 'outline',
                            size: 'sm',
                            onClick: () => editUser(user.id),
                        },
                        () => 'Edit',
                    ),
                    h(
                        Button,
                        {
                            variant: 'outline',
                            size: 'sm',
                            onClick: () => confirmDeleteUser(user.id),
                        },
                        () => 'Delete',
                    ),
                ],
            );
        },
    },
];

// Keep users in sync with Inertia page props (server-provided)
watch(
    () => (page.props as any).users,
    (val) => {
        users.value = val?.data ?? val ?? [];
    },
);

// Show server flash messages
watch(
    () => (page.props as any).flash?.success,
    (val) => {
        if (val) {
            toast.success(val);
        }
    },
);

const setStatusFilter = (status: string) => {
    selectedStatus.value = status;
    router.visit('/users', {
        method: 'get',
        data: status !== 'all' ? { status } : {},
        preserveState: true,
        replace: true,
    });
};

const openCreateDialog = () => {
    dialogMode.value = 'create';
    dialogUser.value = null;
    userDialogOpen.value = true;
};

const editUser = (id: number) => {
    // find user from current list; fallback to null
    const found = (users.value as any[]).find((u) => u.id === id) ?? null;
    dialogMode.value = 'edit';
    dialogUser.value = found;
    userDialogOpen.value = true;
};

const confirmDeleteUser = (id: number) => {
    userToDelete.value = id;
    isPermanentDelete.value = false;
    deleteDialogOpen.value = true;
};

const handleDeleteConfirm = async () => {
    if (!userToDelete.value) {
        return;
    }

    if (isPermanentDelete.value) {
        await deleteForm.delete(`/users/${userToDelete.value}/force-delete`, {
            onSuccess: () => {
                deleteDialogOpen.value = false;
                userToDelete.value = null;
                isPermanentDelete.value = false;
            },
            onError: (errors) => {
                const msgs = Object.values(errors).flat().join('\n');
                toast.error(msgs || 'Error permanently deleting user');
            },
        });
    } else {
        await deleteForm.delete(`/users/${userToDelete.value}`, {
            onSuccess: () => {
                deleteDialogOpen.value = false;
                userToDelete.value = null;
                isPermanentDelete.value = false;
            },
            onError: (errors) => {
                const msgs = Object.values(errors).flat().join('\n');
                toast.error(msgs || 'Error deleting user');
            },
        });
    }
};

const handleUserSaved = (message?: string) => {
    userDialogOpen.value = false;

    if (message) {
        toast.success(message);
    }

    router.reload();
};

const handleDeleteCancel = () => {
    deleteDialogOpen.value = false;
    userToDelete.value = null;
    isPermanentDelete.value = false;
};

const restoreUser = async (id: number) => {
    router.patch(
        `/users/${id}/restore`,
        {},
        {
            onSuccess: () => {
                toast.success('User restored successfully');
                router.reload();
            },
            onError: () => {
                toast.error('Error restoring user');
            },
        },
    );
};

const permanentDeleteUser = (id: number) => {
    userToDelete.value = id;
    isPermanentDelete.value = true;
    deleteDialogOpen.value = true;
};

// Watch search input and request server-side filtering
watch(search, (val) => {
    router.visit('/users', {
        method: 'get',
        data: { name: val, email: val },
        preserveState: true,
        replace: true,
    });
});

// DataTable pagination handlers
const handleTablePageChange = (url: string) => {
    if (!url) return;

    router.visit(url, { preserveState: true, replace: true });
};

const handleTableNext = () => {
    const next = (page.props as any).users?.next_page_url;

    if (next) router.visit(next, { preserveState: true, replace: true });
};

const handleTablePrevious = () => {
    const prev = (page.props as any).users?.prev_page_url;

    if (prev) router.visit(prev, { preserveState: true, replace: true });
};
</script>

<template>
    <Head title="Users" />
    <div class="flex h-full flex-1 flex-col gap-4 p-4 md:flex-row">
        <!-- Sidebar -->
        <div
            class="h-fit w-full rounded-xl border border-gray-200 bg-white md:w-64 dark:border-neutral-800 dark:bg-neutral-900"
        >
            <div class="border-b border-gray-200 p-4 dark:border-neutral-700">
                <h2 class="text-lg font-semibold">Users</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Manage user accounts
                </p>
            </div>
            <div class="p-4">
                <Button
                    variant="outline"
                    class="w-full justify-start"
                    @click="openCreateDialog"
                >
                    <span class="mr-2">+</span>
                    New User
                </Button>
            </div>
            <div class="space-y-1 px-4 pb-4">
                <Button
                    variant="ghost"
                    class="w-full justify-start"
                    :class="selectedStatus === 'all' ? 'bg-accent' : ''"
                    @click="setStatusFilter('all')"
                >
                    All Users
                </Button>
                <Button
                    variant="ghost"
                    class="w-full justify-start"
                    :class="selectedStatus === 'active' ? 'bg-accent' : ''"
                    @click="setStatusFilter('active')"
                >
                    Active
                </Button>
                <Button
                    variant="ghost"
                    class="w-full justify-start"
                    :class="selectedStatus === 'inactive' ? 'bg-accent' : ''"
                    @click="setStatusFilter('inactive')"
                >
                    Inactive
                </Button>
                <Button
                    variant="ghost"
                    class="w-full justify-start"
                    :class="selectedStatus === 'deleted' ? 'bg-accent' : ''"
                    @click="setStatusFilter('deleted')"
                >
                    Deleted
                </Button>
            </div>
        </div>

        <!-- Main Content -->
        <div
            class="flex-1 rounded-xl border border-sidebar-border/70 p-5 dark:border-sidebar-border"
        >
            <div class="mb-4 flex items-center justify-between py-2">
                <h1 class="text-xl font-bold">Users Management</h1>
            </div>

            <!-- Data Table -->
            <DataTable
                :columns="columns"
                :data="users"
                :is-loading="isLoading"
                searchable
                search-placeholder="Search users..."
                :show-pagination="true"
                :page-size="10"
                empty-message="No users found."
                :manual-pagination="true"
                :pagination="(page.props as any).users"
                @update:search="(val) => (search = val)"
                @page-change="(url) => handleTablePageChange(url)"
                @next-page="() => handleTableNext()"
                @previous-page="() => handleTablePrevious()"
            />
        </div>
    </div>

    <!-- Delete Confirmation Dialog -->
    <DeleteConfirmDialog
        v-model:open="deleteDialogOpen"
        :title="isPermanentDelete ? 'Permanently Delete User' : 'Delete User'"
        :description="
            isPermanentDelete
                ? 'Are you sure you want to permanently delete this user? This action cannot be undone and the user will be completely removed from the system.'
                : 'Are you sure you want to delete this user? This action can be undone by restoring the user.'
        "
        :is-loading="deleteForm.processing"
        @confirm="handleDeleteConfirm"
        @cancel="handleDeleteCancel"
    />

    <!-- User Form Dialog -->
    <Dialog :open="userDialogOpen" @update:open="(v) => (userDialogOpen = v)">
        <DialogContent>
            <DialogHeader>
                <DialogTitle>{{
                    dialogMode === 'create' ? 'Create User' : 'Edit User'
                }}</DialogTitle>
            </DialogHeader>

            <div class="mt-2">
                <UserForm
                    :user="dialogUser"
                    :roles="roles"
                    @saved="handleUserSaved"
                />
            </div>

            <DialogFooter />
        </DialogContent>
    </Dialog>
</template>
