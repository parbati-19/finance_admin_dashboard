<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import { Button } from '@/components/ui/button';
import RoleFormFields from './RolesFormFields.vue';

const props = defineProps<{ role: any }>();

const form = useForm({
    name: props.role?.name ?? '',
    guard_name: props.role?.guard_name ?? '',
});

const isEdit = Boolean(props.role);
const isCreate = !isEdit;

// Ensure `route` helper is available in this component (Ziggy may expose it on window)
const route =
    typeof window !== 'undefined' && (window as any).route
        ? (window as any).route.bind(window)
        : (name: string, params?: any) => {
              // fallback: build a simple RESTful URL for resource routes when Ziggy isn't present
              console.warn(
                  'route() helper is not available on window. Falling back to simple URL builder.',
              );
              const parts = name.split('.');
              const resource = parts[0];
              const action = parts[1] ?? 'index';

              if (action === 'update') {
                  const id =
                      typeof params === 'object' && params?.id
                          ? params.id
                          : params;

                  return id ? `/${resource}/${id}` : `/${resource}`;
              }

              if (action === 'store' || action === 'index') {
                  return `/${resource}`;
              }

              return `/${resource}`;
          };

const emit = defineEmits<{
    saved: [message?: string];
}>();
const submit = () => {
    if (props.role) {
        form.put(route('roles.update', props.role.id), {
            onSuccess: () => {
                emit('saved', 'Role updated successfully');
            },
            onError: (errors: Record<string, any>) => {
                const msgs = Object.values(errors).flat().join('\n');
                toast.error(msgs || 'Validation failed');
            },
        });
    } else {
        form.post(route('roles.store'), {
            onSuccess: () => {
                emit('saved', 'Role created successfully');
            },
            onError: (errors: Record<string, any>) => {
                const msgs = Object.values(errors).flat().join('\n');
                toast.error(msgs || 'Validation failed');
            },
        });
    }
};
</script>

<template>
    <Head title="Role Form" />
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl">
        <div class="relative min-h-[100vh] flex-1 rounded-xl md:min-h-min">
            <div
                class="flex flex-col space-y-4 rounded-xl border border-gray-100 p-2 shadow-xs dark:border-neutral-800"
            >
                <div class="w-full max-w-3xl">
                    <form @submit.prevent="submit">
                        <RoleFormFields
                            :form="form"
                            :is-edit="isEdit"
                            :is-create="isCreate"
                        />
                        <div class="mt-4 mr-6 mb-4 flex justify-end gap-2">
                            <Button type="submit">Save</Button>
                            <Button
                                as="a"
                                :href="route('roles.index')"
                                variant="secondary"
                                >Cancel</Button
                            >
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
