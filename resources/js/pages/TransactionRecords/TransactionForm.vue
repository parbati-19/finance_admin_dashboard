<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import { Button } from '@/components/ui/button';
import TransactionFormFields from './TransactionFormFields.vue';

const props = defineProps<{
    transaction: any;
    types: { value: number; label: string }[];
}>();

const form = useForm({
    amount: props.transaction?.amount ?? '',
    type: props.transaction?.type ? String(props.transaction.type) : '',
    category: props.transaction?.category ?? '',
    date: props.transaction?.date ?? new Date().toISOString().slice(0, 10),
    notes: props.transaction?.notes ?? '',
});

const isEdit = Boolean(props.transaction);

const emit = defineEmits<{
    saved: [message?: string];
}>();

const submit = () => {
    if (isEdit) {
        form.put(`/transaction-records/${props.transaction.id}`, {
            onSuccess: () => {
                emit('saved', 'Transaction updated successfully');
            },
            onError: (errors: Record<string, any>) => {
                const msgs = Object.values(errors).flat().join('\n');
                toast.error(msgs || 'Validation failed');
            },
        });
    } else {
        form.post('/transaction-records', {
            onSuccess: () => {
                emit('saved', 'Transaction created successfully');
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
    <div class="flex flex-col gap-4">
        <form @submit.prevent="submit">
            <TransactionFormFields :form="form" :types="types" />
            <div class="mt-4 flex justify-end gap-2">
                <Button type="submit" :disabled="form.processing">
                    {{ form.processing ? 'Saving...' : 'Save' }}
                </Button>
            </div>
        </form>
    </div>
</template>
