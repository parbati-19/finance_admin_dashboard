<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import { Button } from '@/components/ui/button';
import UserFormFields from './UserFormFields.vue';

const props = defineProps<{ user: any; details?: any; roles?: string[] }>();

const form = useForm({
    name: props.user?.name ?? '',
    email: props.user?.email ?? '',
    status: props.user?.status ?? 1,
    role: props.user?.role ?? '',
    password: '',
    password_confirmation: '',
    email_verified_at: props.user?.email_verified_at ?? '',
});

const isEdit = Boolean(props.user);
const isCreate = !isEdit;

const emit = defineEmits(['saved']);

const submit = () => {
    const options = {
        onSuccess: () => {
            const msg = isEdit
                ? 'User updated successfully'
                : 'User created successfully';
            toast.success(msg);
            emit('saved', msg);
        },
        onError: (errors: Record<string, any>) => {
            const msgs = Object.values(errors).flat().join('\n');
            toast.error(msgs || 'Validation failed');
        },
    };

    if (isEdit) {
        form.put(`/users/${props.user.id}`, options);
    } else {
        form.post('/users', options);
    }
};
</script>

<template>
    <Head title="User Form" />
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
        <div
            class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 px-5 md:min-h-min dark:border-sidebar-border"
        >
            <div class="mb-4 flex items-center justify-between py-5">
                <h1 class="text-xl font-bold">
                    {{ props.user ? 'Edit' : 'Add' }} Details
                </h1>
                <div>
                    <Button as="a" href="/users">Back to User</Button>
                </div>
            </div>

            <div
                class="flex flex-col space-y-4 rounded-xl border border-gray-200 bg-white p-5 px-5 shadow-xs dark:border-neutral-700 dark:bg-neutral-800"
            >
                <div class="w-full max-w-3xl">
                    <form @submit.prevent="submit">
                        <UserFormFields
                            :form="form"
                            :is-edit="isEdit"
                            :is-create="isCreate"
                            :roles="props.roles ?? []"
                        />
                        <div class="mt-4 flex justify-end gap-2">
                            <Button type="submit">Save</Button>
                            <Button as="a" href="/users" variant="secondary"
                                >Cancel</Button
                            >
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
