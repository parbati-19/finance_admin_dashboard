<script setup lang="ts">
import { Head, router, setLayoutProps } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
import { toast } from 'vue-sonner';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Switch } from '@/components/ui/switch';
import type { BreadcrumbItem } from '@/types';

interface Role {
    id: number;
    name: string;
}

interface Permission {
    id: number;
    name: string;
}

const props = defineProps<{
    roles: Role[];
    permissions: Permission[];
    selectedRoleId: number | null;
    rolePermissions: number[];
}>();

const breadcrumbItems: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Role & Permissions', href: '/role-permissions' },
];

setLayoutProps({ breadcrumbs: breadcrumbItems });

const selectedRole = ref<string>(
    props.selectedRoleId ? String(props.selectedRoleId) : '',
);

const enabledPermissions = ref<number[]>([...props.rolePermissions]);
const isSaving = ref(false);
const highlightSelect = ref(false);

const roleSelected = computed(() => !!selectedRole.value);

watch(selectedRole, (val) => {
    if (val) {
        highlightSelect.value = false;

        router.get(
            '/role-permissions',
            { role_id: val },
            { preserveState: false },
        );
    }
});

watch(
    () => props.rolePermissions,
    (val) => {
        enabledPermissions.value = [...val];
    },
    { immediate: true },
);

// 🔥 Debounce logic (critical fix)
let debounceTimer: any = null;

const debouncedSync = (permissions: number[]) => {
    clearTimeout(debounceTimer);

    debounceTimer = setTimeout(() => {
        syncPermissions(permissions);
    }, 400);
};

// 🔥 API sync
const syncPermissions = (permissions: number[]) => {
    if (!selectedRole.value || isSaving.value) {
return;
}

    isSaving.value = true;

    router.put(
        '/role-permissions',
        {
            role_id: Number(selectedRole.value),
            permissions,
        },
        {
            preserveState: true,
            preserveScroll: true,

            onSuccess: () => {
                toast.success('Permissions updated successfully');
            },

            onError: (errors) => {
                const msgs = Object.values(errors).flat().join('\n');
                toast.error(msgs || 'Failed to update permissions');
            },

            onFinish: () => {
                isSaving.value = false;
            },
        },
    );
};

// 🔥 Toggle single permission
const togglePermission = (permissionId: number, checked: boolean) => {
    if (!roleSelected.value) {
        highlightSelect.value = true;

        return;
    }

    let next: number[];

    if (checked) {
        next = [...new Set([...enabledPermissions.value, permissionId])];
    } else {
        next = enabledPermissions.value.filter((id) => id !== permissionId);
    }

    enabledPermissions.value = next;

    debouncedSync(next); // ✅ FIXED
};

// 🔥 Toggle all permissions
const toggleAll = (enable: boolean) => {
    if (!roleSelected.value) {
        highlightSelect.value = true;

        return;
    }

    const next = enable ? props.permissions.map((p) => p.id) : [];

    enabledPermissions.value = next;

    debouncedSync(next); // ✅ FIXED
};

// 🔥 Computed state
const allEnabled = computed(
    () =>
        props.permissions.length > 0 &&
        enabledPermissions.value.length === props.permissions.length,
);
</script>

<template>
    <Head title="Role & Permissions" />

    <div class="flex h-full flex-1 flex-col gap-4 p-4">
        <div
            class="flex-1 rounded-xl border border-sidebar-border/70 p-5 dark:border-sidebar-border"
        >
            <!-- Header -->
            <div class="mb-6 flex items-center justify-between">
                <h1 class="text-xl font-bold">Role & Permissions</h1>
            </div>

            <!-- Role Selector -->
            <div class="mb-6 max-w-sm">
                <Label class="mb-2 block text-sm font-medium">
                    Select Role
                </Label>

                <Select v-model="selectedRole">
                    <SelectTrigger
                        class="w-full transition-all"
                        :class="
                            highlightSelect
                                ? 'border-destructive ring-2 ring-destructive'
                                : ''
                        "
                    >
                        <SelectValue placeholder="Choose a role..." />
                    </SelectTrigger>

                    <SelectContent>
                        <SelectItem
                            v-for="role in roles"
                            :key="role.id"
                            :value="String(role.id)"
                        >
                            {{ role.name }}
                        </SelectItem>
                    </SelectContent>
                </Select>

                <p v-if="highlightSelect" class="mt-1 text-xs text-destructive">
                    Please select a role first
                </p>
            </div>

            <!-- Actions -->
            <div class="mb-4 flex items-center justify-between">
                <p class="text-sm text-muted-foreground">
                    {{
                        roleSelected
                            ? 'Toggle permissions for this role'
                            : 'All permissions in the system'
                    }}
                </p>

                <div v-if="roleSelected" class="flex items-center gap-3">
                    <Button
                        variant="outline"
                        size="sm"
                        :disabled="isSaving"
                        @click="toggleAll(!allEnabled)"
                    >
                        {{ allEnabled ? 'Disable All' : 'Enable All' }}
                    </Button>
                </div>
            </div>

            <!-- Empty State -->
            <div
                v-if="permissions.length === 0"
                class="py-8 text-center text-sm text-muted-foreground"
            >
                No permissions found. Create permissions first.
            </div>

            <!-- Permissions Grid -->
            <div
                v-else
                class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4"
            >
                <div
                    v-for="permission in permissions"
                    :key="permission.id"
                    class="flex items-center justify-between rounded-lg border border-border p-3 transition-colors"
                    :class="[
                        enabledPermissions.includes(permission.id)
                            ? 'bg-accent/50'
                            : '',
                        !roleSelected ? 'opacity-60' : '',
                    ]"
                >
                    <Label
                        :for="`perm-${permission.id}`"
                        class="text-sm font-medium"
                        :class="
                            roleSelected
                                ? 'cursor-pointer'
                                : 'cursor-not-allowed'
                        "
                    >
                        {{ permission.name }}
                    </Label>

                    <Switch
                        :modelValue="enabledPermissions.includes(permission.id)"
                        :disabled="isSaving || !roleSelected"
                        @update:modelValue="
                            (val: boolean) =>
                                togglePermission(permission.id, val)
                        "
                    />
                </div>
            </div>
        </div>
    </div>
</template>
