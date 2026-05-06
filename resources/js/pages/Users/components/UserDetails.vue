<script setup lang="ts">
import { defineProps } from 'vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';

const props = defineProps<{ form: any }>();
const form = props.form;
</script>

<template>
    <div class="space-y-5 rounded-xl  bg-white p-5 md:p-8 dark:bg-neutral-800">
        <div class="mb-4 xl:mb-8">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-neutral-200">User Details</h3>
            <p class="text-sm text-gray-500 dark:text-neutral-500">Provide additional user information.</p>
        </div>

        <div class="grid gap-y-1.5 sm:grid-cols-12 sm:gap-x-5 sm:gap-y-0">
            <div class="sm:col-span-4 xl:col-span-3 2xl:col-span-2">
                <Label class="mb-3">User Type</Label>
            </div>
            <div class="sm:col-span-8 xl:col-span-6 2xl:col-span-4">
                <div>
                    <Select v-model="form.user_type">
                        <SelectTrigger class="w-full">
                            <SelectValue placeholder="Select type" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem :value="0">Personal</SelectItem>
                            <SelectItem :value="1">Business</SelectItem>
                        </SelectContent>
                    </Select>
                </div>
                <div v-if="form.errors?.user_type" class="mt-2 text-sm text-red-600 dark:text-red-400">{{ props.form.errors.user_type }}</div>
            </div>
        </div>

        <div class="grid gap-y-1.5 sm:grid-cols-12 sm:gap-x-5 sm:gap-y-0">
            <div class="sm:col-span-4 xl:col-span-3 2xl:col-span-2">
                <Label for="company_name" class="mb-3">Company Name</Label>
            </div>
            <div class="sm:col-span-8 xl:col-span-6 2xl:col-span-4">
                <Input id="company_name" v-model="form.company_name" />
                <div v-if="form.errors?.company_name" class="mt-2 text-sm text-red-600 dark:text-red-400">
                    {{ props.form.errors.company_name }}
                </div>
            </div>
        </div>

        <div class="grid gap-y-1.5 sm:grid-cols-12 sm:gap-x-5 sm:gap-y-0">
            <div class="sm:col-span-4 xl:col-span-3 2xl:col-span-2">
                <Label for="company_pan">Company PAN</Label>
            </div>
            <div class="sm:col-span-8 xl:col-span-6 2xl:col-span-4">
                <Input id="company_pan" v-model="form.company_pan" />
                <div v-if="form.errors?.company_pan" class="mt-2 text-sm text-red-600 dark:text-red-400">
                    {{ props.form.errors.company_pan }}
                </div>
            </div>
        </div>

        <div class="grid gap-y-1.5 sm:grid-cols-12 sm:gap-x-5 sm:gap-y-0">
            <div class="sm:col-span-4 xl:col-span-3 2xl:col-span-2">
                <Label for="contact_person">Contact Person</Label>
            </div>
            <div class="sm:col-span-8 xl:col-span-6 2xl:col-span-4">
                <Input id="contact_person" v-model="form.contact_person" />
                <div v-if="form.errors?.contact_person" class="mt-2 text-sm text-red-600 dark:text-red-400">
                    {{ props.form.errors.contact_person }}
                </div>
            </div>
        </div>

        <div class="grid gap-y-1.5 sm:grid-cols-12 sm:gap-x-5 sm:gap-y-0">
            <div class="sm:col-span-4 xl:col-span-3 2xl:col-span-2">
                <Label for="company_address">Company Address</Label>
            </div>
            <div class="sm:col-span-8 xl:col-span-6 2xl:col-span-4">
                <Textarea id="company_address" v-model="form.company_address" rows="3" />
                <div v-if="form.errors?.company_address" class="mt-2 text-sm text-red-600 dark:text-red-400">
                    {{ props.form.errors.company_address }}
                </div>
            </div>
        </div>

        <div class="grid gap-y-1.5 sm:grid-cols-12 sm:gap-x-5 sm:gap-y-0">
            <div class="sm:col-span-4 xl:col-span-3 2xl:col-span-2">
                <Label>Phones</Label>
            </div>
            <div class="sm:col-span-8 xl:col-span-6 2xl:col-span-4">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <Input v-model="form.company_phone" placeholder="Company phone" />
                        <div v-if="form.errors?.company_phone" class="mt-2 text-sm text-red-600 dark:text-red-400">
                            {{ props.form.errors.company_phone }}
                        </div>
                    </div>
                    <div>
                        <Input v-model="form.personal_phone" placeholder="Personal phone" />
                        <div v-if="form.errors?.personal_phone" class="mt-2 text-sm text-red-600 dark:text-red-400">
                            {{ props.form.errors.personal_phone }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid gap-y-1.5 sm:grid-cols-12 sm:gap-x-5 sm:gap-y-0">
            <div class="sm:col-span-4 xl:col-span-3 2xl:col-span-2">
                <Label for="company_email">Company Email</Label>
            </div>
            <div class="sm:col-span-8 xl:col-span-6 2xl:col-span-4">
                <Input id="company_email" v-model="form.company_email" type="email" />
                <div v-if="form.errors?.company_email" class="mt-2 text-sm text-red-600 dark:text-red-400">
                    {{ props.form.errors.company_email }}
                </div>
            </div>
        </div>

        <div class="grid gap-y-1.5 sm:grid-cols-12 sm:gap-x-5 sm:gap-y-0">
            <div class="sm:col-span-4 xl:col-span-3 2xl:col-span-2"></div>
            <div class="sm:col-span-8 xl:col-span-6 2xl:col-span-4">
                <div class="flex items-center gap-2">
                    <input id="isVerified" type="checkbox" v-model="form.is_verified" class="h-4 w-4" />
                    <Label for="isVerified" class="mb-0">Verified</Label>
                </div>
                <div v-if="form.errors?.is_verified" class="mt-2 text-sm text-red-600 dark:text-red-400">
                    {{ props.form.errors.is_verified }}
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* local styles */
</style>
