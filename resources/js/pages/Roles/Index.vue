<script setup lang="ts">
import {
    Head,
    setLayoutProps,
    usePage,
    router,
    useForm,
} from '@inertiajs/vue3';
// Accept arbitrary incoming props (errors, auth, flash, etc.) to avoid Vue's "extraneous attributes" warning
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
import { destroy as destroyRole } from '@/routes/roles';
import type { BreadcrumbItem } from '@/types';
import RolesForm from './RolesForm.vue';

const page = usePage();
const roles = ref(
    (page.props as any).roles?.data ?? (page.props as any).roles ?? [],
);
const isLoading = ref(false);
const filters = (page.props as any).filters ?? {};
const search = ref(filters.name ?? '');

// Dialog state for create/edit role
const roleDialogOpen = ref(false);
const roleDialogMode = ref<'create' | 'edit'>('create');
const dialogRole = ref<any | null>(null);

// Delete dialog state
const deleteDialogOpen = ref(false);
const roleToDelete = ref<number | null>(null);
const deleteForm = useForm({});

const breadcrumbItems: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Roles', href: '/roles' },
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
            const r = row.original;

            return h('div', { class: 'flex gap-2' }, [
                h(
                    Button,
                    {
                        variant: 'outline',
                        size: 'sm',
                        onClick: () => editRole(r.id),
                    },
                    () => 'Edit',
                ),
                h(
                    Button,
                    {
                        variant: 'destructive',
                        size: 'sm',
                        onClick: () => confirmDeleteRole(r.id),
                    },
                    () => 'Delete',
                ),
            ]);
        },
    },
];

watch(
    () => (page.props as any).roles,
    (val) => {
        roles.value = val?.data ?? val ?? [];
    },
);

let searchTimeout: ReturnType<typeof setTimeout>;
watch(search, (val) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get(
            '/roles',
            {
                name: val || undefined,
            },
            { preserveState: true, replace: true },
        );
    }, 300);
});

const openCreateRoleDialog = () => {
    roleDialogMode.value = 'create';
    dialogRole.value = null;
    roleDialogOpen.value = true;
};

const editRole = (id: number) => {
    const found = (roles.value as any[]).find((r) => r.id === id) ?? null;
    roleDialogMode.value = 'edit';
    dialogRole.value = found;
    roleDialogOpen.value = true;
};

const confirmDeleteRole = (id: number) => {
    roleToDelete.value = id;
    deleteDialogOpen.value = true;
};

const handleDeleteConfirm = async () => {
    if (!roleToDelete.value) {
        return;
    }

    await deleteForm.delete(destroyRole.url(roleToDelete.value), {
        onSuccess: () => {
            deleteDialogOpen.value = false;
            roleToDelete.value = null;
            toast.success('Role deleted successfully');
            router.reload();
        },
        onError: (errors) => {
            const msgs = Object.values(errors).flat().join('\n');
            toast.error(msgs || 'Error deleting role');
        },
    });
};

const handleDeleteCancel = () => {
    deleteDialogOpen.value = false;
    roleToDelete.value = null;
};

const handleRoleSaved = (message?: string) => {
    roleDialogOpen.value = false;

    if (message) {
        toast.success(message);
    }

    router.reload();
};
</script>

<template>
    <Head title="Roles" />
    <div class="flex h-full flex-1 flex-col gap-4 p-4 md:flex-row">
        <div
            class="flex-1 rounded-xl border border-sidebar-border/70 p-5 dark:border-sidebar-border"
        >
            <div class="mb-4 flex items-center justify-between py-2">
                <h1 class="text-xl font-bold">Roles</h1>
                <Button variant="default" @click="openCreateRoleDialog">
                    Create Role
                </Button>
            </div>

            <DataTable
                :columns="columns"
                :data="roles"
                :is-loading="isLoading"
                searchable
                search-placeholder="Search roles..."
                :show-pagination="true"
                :page-size="10"
                empty-message="No roles found."
                @update:search="(val) => (search = val)"
            />
        </div>
    </div>

    <!-- Delete Confirmation Dialog -->
    <DeleteConfirmDialog
        v-model:open="deleteDialogOpen"
        title="Delete Role"
        description="Are you sure you want to delete this role? This action cannot be undone."
        :is-loading="deleteForm.processing"
        @confirm="handleDeleteConfirm"
        @cancel="handleDeleteCancel"
    />

    <!-- Role Form Dialog -->
    <Dialog :open="roleDialogOpen" @update:open="(v) => (roleDialogOpen = v)">
        <DialogContent>
            <DialogHeader>
                <DialogTitle>{{
                    roleDialogMode === 'create' ? 'Create Role' : 'Edit Role'
                }}</DialogTitle>
                <DialogDescription
                    >Fill in the details to create a new
                    plan.</DialogDescription
                >
            </DialogHeader>

            <div class="mt-2">
                <RolesForm :role="dialogRole" @saved="handleRoleSaved" />
            </div>
            <DialogFooter />
        </DialogContent>
    </Dialog>
</template>
