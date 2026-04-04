<script setup lang="ts">
import { router, usePage } from '@inertiajs/vue3';
import type { AcceptableValue } from 'reka-ui';
import AppearanceTabs from '@/components/AppearanceTabs.vue';
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { SidebarTrigger } from '@/components/ui/sidebar';
import type { BreadcrumbItem, SharedData } from '@/types';

withDefaults(
    defineProps<{
        breadcrumbs?: BreadcrumbItem[];
    }>(),
    {
        breadcrumbs: () => [],
    },
);

const page = usePage<SharedData>();

const switchRole = (role: AcceptableValue) => {
    if (typeof role === 'string') {
        router.put(
            '/role-switch',
            { role },
            {
                preserveState: false,
            },
        );
    }
};
</script>

<template>
    <header
        class="flex h-16 shrink-0 items-center gap-2 border-b border-sidebar-border/70 px-6 transition-[width,height] ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:h-12 md:px-4"
    >
        <div class="flex items-center gap-2">
            <SidebarTrigger class="-ml-1" />
            <template v-if="breadcrumbs && breadcrumbs.length > 0">
                <Breadcrumbs :breadcrumbs="breadcrumbs" />
            </template>
        </div>
        <div class="ml-auto flex items-center gap-3">
            <AppearanceTabs />
            <Select
                :model-value="page.props.auth.role ?? ''"
                @update:model-value="switchRole"
            >
                <SelectTrigger class="h-8 w-[130px]">
                    <SelectValue placeholder="Select role" />
                </SelectTrigger>
                <SelectContent>
                    <SelectItem
                        v-for="role in page.props.auth.roles"
                        :key="role"
                        :value="role"
                    >
                        {{ role }}
                    </SelectItem>
                </SelectContent>
            </Select>
        </div>
    </header>
</template>
