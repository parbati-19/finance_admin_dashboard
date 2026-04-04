<script setup lang="ts">
import { Checkbox } from '@/components/ui/checkbox';
import { Label } from '@/components/ui/label';
import { defineProps } from 'vue';

const props = defineProps<{ form: any; user?: any; roles?: any[]; permissions?: any[] }>();
</script>

<template>
  <div class="space-y-4">
    <div>
      <Label class="mb-2">Assign Roles</Label>
      <div class="grid grid-cols-3 gap-2">
        <div v-for="r in props.roles || []" :key="r.id" class="flex items-center gap-2">
          <input type="checkbox" :value="r.name" v-model="props.form.roles" class="h-4 w-4" />
          <span class="text-sm">{{ r.name }}</span>
        </div>
        <div v-if="(props.roles || []).length === 0" class="text-sm text-muted-foreground">No roles available</div>
      </div>
    </div>

    <div>
      <Label class="mb-2">Assign Permissions</Label>
      <div class="grid grid-cols-3 gap-2">
        <div v-for="p in props.permissions || []" :key="p.id" class="flex items-center gap-2">
          <input type="checkbox" :value="p.name" v-model="props.form.permissions" class="h-4 w-4" />
          <span class="text-sm">{{ p.name }}</span>
        </div>
        <div v-if="(props.permissions || []).length === 0" class="text-sm text-muted-foreground">No permissions available</div>
      </div>
    </div>
  </div>
</template>
