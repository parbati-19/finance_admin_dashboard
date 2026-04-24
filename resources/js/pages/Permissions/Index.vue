<script setup lang="ts">
import {
    Head,
    router,
    setLayoutProps,
    usePage,
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
    DialogFooter,
} from '@/components/ui/dialog';
import { destroy as destroyPermission } from '@/routes/permissions';
import type { BreadcrumbItem } from '@/types';
import PermissionForm from './PermissionForm.vue';
import { Input } from '@/components/ui/input';

const page = usePage();
const permissions = ref(
    (page.props as any).permissions?.data ??
        (page.props as any).permissions ??
        [],
);
const isLoading = ref(false);
const filters = (page.props as any).filters ?? {};
const search = ref(filters.name ?? '');

// Dialog state for create/edit permission
const permissionDialogOpen = ref(false);
const permissionDialogMode = ref<'create' | 'edit'>('create');
const dialogPermission = ref<any | null>(null);

// Delete dialog state
const deleteDialogOpen = ref(false);
const permissionToDelete = ref<number | null>(null);
const deleteForm = useForm({});

const breadcrumbItems: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Permissions', href: '/permissions' },
];

setLayoutProps({ breadcrumbs: breadcrumbItems });

const columns: ColumnDef[] = [
    {
        accessorKey: 'id',
        header: '#',
        cell: ({ row }) =>
            h('div', { class: 'font-medium' }, row.getValue('id')),
    },
    { accessorKey: 'name', header: 'Name' },
    { accessorKey: 'guard_name', header: 'Guard' },
    {
        id: 'actions',
        header: 'Actions',
        cell: ({ row }) => {
            const p = row.original;

            return h('div', { class: 'flex gap-2' }, [
                h(
                    Button,
                    {
                        variant: 'outline',
                        size: 'sm',
                        onClick: () => editPermission(p.id),
                    },
                    () => 'Edit',
                ),
                h(
                    Button,
                    {
                        variant: 'destructive',
                        size: 'sm',
                        onClick: () => confirmDeletePermission(p.id),
                    },
                    () => 'Delete',
                ),
            ]);
        },
    },
];

watch(
    () => (page.props as any).permissions,
    (val) => {
        permissions.value = val?.data ?? val ?? [];
    },
);

let searchTimeout: ReturnType<typeof setTimeout>;
watch(search, (val) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get(
            '/permissions',
            {
                name: val || undefined,
            },
            { preserveState: true, replace: true },
        );
    }, 300);
});

const handlePermissionSaved = (message?: string) => {
    permissionDialogOpen.value = false;

    if (message) {
        toast.success(message);
    }

    router.reload();
};

const confirmDeletePermission = (id: number) => {
    permissionToDelete.value = id;
    deleteDialogOpen.value = true;
};

const handleDeleteConfirm = async () => {
    if (!permissionToDelete.value) {
        return;
    }

    await deleteForm.delete(destroyPermission.url(permissionToDelete.value), {
        onSuccess: () => {
            deleteDialogOpen.value = false;
            permissionToDelete.value = null;
            toast.success('Permission deleted successfully');
            router.reload();
        },
        onError: (errors) => {
            const msgs = Object.values(errors).flat().join('\n');
            toast.error(msgs || 'Error deleting permission');
        },
    });
};

const handleDeleteCancel = () => {
    deleteDialogOpen.value = false;
    permissionToDelete.value = null;
};

const openCreatePermissionDialog = () => {
    permissionDialogMode.value = 'create';
    dialogPermission.value = null;
    permissionDialogOpen.value = true;
};

const editPermission = (id: number) => {
    const found = (permissions.value as any[]).find((p) => p.id === id) ?? null;
    permissionDialogMode.value = 'edit';
    dialogPermission.value = found;
    permissionDialogOpen.value = true;
};
</script>

<template>
    <Head title="Permissions" />
    <div class="flex h-full flex-1 flex-col gap-4 p-4 md:flex-row">
        <div
            class="flex-1 rounded-xl border border-sidebar-border/70 p-5 dark:border-sidebar-border"
        >
            <div class="mb-4 flex items-center justify-between py-2">
                <h1 class="text-xl font-bold">Permissions</h1>
                <button
                    class="inline-flex items-center rounded-md bg-primary px-3 py-2 text-sm font-medium text-white hover:bg-primary/90"
                    @click="openCreatePermissionDialog"
                >
                    Create Permission
                </button>
            </div>

            <DataTable
                :columns="columns"
                :data="permissions"
                :is-loading="isLoading"
                searchable
                search-placeholder="Search permissions..."
                :show-pagination="true"
                :page-size="10"
                empty-message="No permissions found."
                @update:search="(val) => (search = val)"
            />
        </div>
    </div>

    <!-- Delete Confirmation Dialog -->
    <DeleteConfirmDialog
        v-model:open="deleteDialogOpen"
        title="Delete Permission"
        description="Are you sure you want to delete this permission? This action cannot be undone."
        :is-loading="deleteForm.processing"
        @confirm="handleDeleteConfirm"
        @cancel="handleDeleteCancel"
    />

    <!-- Permission Form Dialog -->
    <Dialog
        :open="permissionDialogOpen"
        @update:open="(v) => (permissionDialogOpen = v)"
    >
        <DialogContent>
            <DialogHeader>
                <DialogTitle>{{
                    permissionDialogMode === 'create'
                        ? 'Create Permission'
                        : 'Edit Permission'
                }}</DialogTitle>
                <DialogDescription
                    >Fill in the details to create a new
                    permission.</DialogDescription
                >
            </DialogHeader>

            <div class="mt-2">
                <PermissionForm
                    :permission="dialogPermission"
                    @saved="handlePermissionSaved"
                />
            </div>

            <DialogFooter />
        </DialogContent>
    </Dialog>
</template>
