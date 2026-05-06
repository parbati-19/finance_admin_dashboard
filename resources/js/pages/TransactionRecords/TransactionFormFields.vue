<script setup lang="ts">
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';

const props = defineProps<{
    form: any;
    types: { value: number; label: string }[];
}>();

const { form, types } = props;
</script>

<template>
    <div class="space-y-4">
        <div>
            <Label for="txnType" class="mb-2 block">Type</Label>
            <Select v-model="form.type">
                <SelectTrigger class="w-full">
                    <SelectValue placeholder="Select type" />
                </SelectTrigger>
                <SelectContent>
                    <SelectItem
                        v-for="t in types"
                        :key="t.value"
                        :value="String(t.value)"
                    >
                        {{ t.label }}
                    </SelectItem>
                </SelectContent>
            </Select>
            <p v-if="form.errors?.type" class="mt-1 text-xs text-destructive">
                {{ form.errors.type }}
            </p>
        </div>

        <div>
            <Label for="txnAmount" class="mb-2 block">Amount</Label>
            <Input
                id="txnAmount"
                v-model="form.amount"
                type="number"
                step="0.01"
                min="0.01"
                required
                placeholder="0.00"
            />
            <p v-if="form.errors?.amount" class="mt-1 text-xs text-destructive">
                {{ form.errors.amount }}
            </p>
        </div>

        <div>
            <Label for="txnCategory" class="mb-2 block">Category</Label>
            <Input
                id="txnCategory"
                v-model="form.category"
                required
                placeholder="e.g. Food, Salary, Rent..."
            />
            <p
                v-if="form.errors?.category"
                class="mt-1 text-xs text-destructive"
            >
                {{ form.errors.category }}
            </p>
        </div>

        <div>
            <Label for="txnDate" class="mb-2 block">Date</Label>
            <Input id="txnDate" v-model="form.date" type="date" required />
            <p v-if="form.errors?.date" class="mt-1 text-xs text-destructive">
                {{ form.errors.date }}
            </p>
        </div>

        <div>
            <Label for="txnNotes" class="mb-2 block">Notes</Label>
            <Textarea
                id="txnNotes"
                v-model="form.notes"
                rows="3"
                class="flex w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50"
                placeholder="Optional notes..."
            ></Textarea>
            <p v-if="form.errors?.notes" class="mt-1 text-xs text-destructive">
                {{ form.errors.notes }}
            </p>
        </div>
    </div>
</template>
