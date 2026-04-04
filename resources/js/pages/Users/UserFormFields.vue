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

const props = withDefaults(
    defineProps<{
        form: any;
        isEdit?: boolean;
        isCreate?: boolean;
        roles?: string[];
    }>(),
    {
        isEdit: false,
        isCreate: false,
        roles: () => [],
    },
);
const { form, isEdit, isCreate, roles } = props;
</script>
<template>
    <div class="mb-3">
        <Label for="userName" class="form-label mb-3">Name</Label>
        <Input v-model="form.name" id="userName" required />
    </div>

    <div class="mb-3">
        <Label for="userEmail" class="form-label mb-3">Email</Label>
        <Input v-model="form.email" id="userEmail" type="email" required />
    </div>

    <div class="mb-3">
        <Label for="userPassword" class="form-label mb-3">Password</Label>
        <Input
            v-model="form.password"
            id="userPassword"
            type="password"
            :placeholder="isEdit ? 'Leave blank to keep unchanged' : ''"
            :required="isCreate"
        />
    </div>

    <div class="mb-3">
        <Label for="userPasswordConfirm" class="form-label mb-3"
            >Confirm Password</Label
        >
        <Input
            v-model="form.password_confirmation"
            id="userPasswordConfirm"
            type="password"
            :required="isCreate"
        />
    </div>

    <div class="mb-3">
        <Label for="userRole" class="form-label mb-3">Role</Label>
        <Select v-model="form.role">
            <SelectTrigger id="userRole">
                <SelectValue placeholder="Select a role" />
            </SelectTrigger>
            <SelectContent>
                <SelectItem v-for="role in roles" :key="role" :value="role">
                    {{ role }}
                </SelectItem>
            </SelectContent>
        </Select>
    </div>

    <div class="mb-3">
        <Label for="userStatus" class="form-label mb-3">Status</Label>
        <select
            v-model="form.status"
            id="userStatus"
            class="w-full rounded-md border px-3 py-2"
        >
            <option :value="1">Active</option>
            <option :value="0">Inactive</option>
        </select>
    </div>
</template>
