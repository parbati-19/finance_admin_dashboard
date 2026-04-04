<script setup lang="ts">
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

const props = withDefaults(
    defineProps<{
        form: any;
        isEdit?: boolean;
        isCreate?: boolean;
    }>(),
    {
        isEdit: false,
        isCreate: false,
    },
);
const { form, isEdit, isCreate } = props;
</script>

<template>
    <div class="p-2">
        <div class="rounded p-4">
            <div class="space-y-4">
                <div>
                    <Label class="mb-3">Name</Label>
                    <Input v-model="form.name" />
                </div>
                <div>
                    <Label class="mb-3">Guard (optional)</Label>
                    <Input v-model="form.guard_name" />
                </div>
            </div>
        </div>
    </div>
</template>
