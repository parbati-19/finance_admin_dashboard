<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import { Button } from '@/components/ui/button';
import PermissionFormFields from './PermissionFormFields.vue';

const props = defineProps<{ permission?: any }>();

const form = useForm({
    name: props.permission?.name ?? '',
    guard_name: props.permission?.guard_name ?? '',
});

const isEdit = Boolean(props.permission);
const isCreate = !isEdit;

// Ensure `route` helper is available in this component (Ziggy may expose it on window)
const route =
    typeof window !== 'undefined' && (window as any).route
        ? (window as any).route.bind(window)
        : (name: string, params?: any) => {
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

const emit = defineEmits(['saved']);

const submit = () => {
    if (props.permission) {
        form.put(route('permissions.update', props.permission.id), {
            onSuccess: () => {
                emit('saved', 'Permission updated');
            },
            onError: (errors: Record<string, any>) => {
                const msgs = Object.values(errors).flat().join('\n');
                toast.error(msgs || 'Validation failed');
            },
        });
    } else {
        form.post(route('permissions.store'), {
            onSuccess: () => {
                emit('saved', 'Permission created');
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
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl ">
        <div class="relative min-h-[100vh] flex-1 rounded-xl md:min-h-min">
            <div
                class="flex flex-col space-y-4 rounded-xl border border-gray-100 p-2 shadow-xs dark:border-neutral-800"
            >
                <div class="w-full max-w-3xl">
                    <form @submit.prevent="submit">
                        <PermissionFormFields
                            :form="form"
                            :is-edit="isEdit"
                            :is-create="isCreate"
                        />
                        <div class="mt-4 mr-6 mb-4 flex justify-end gap-2">
                            <Button type="submit">Save</Button>
                            <Button
                                as="a"
                                :href="route('permissions.index')"
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
