<script setup lang="ts">
import type { HTMLAttributes } from "vue"
import { computed } from "vue"
import { SwitchRoot, SwitchThumb } from "reka-ui"
import { cn } from "@/lib/utils"

const props = defineProps<{
  modelValue: boolean
  disabled?: boolean
  class?: HTMLAttributes["class"]
}>()

const emit = defineEmits<{
  (e: "update:modelValue", value: boolean): void
}>()

const checked = computed({
  get: () => props.modelValue,
  set: (val: boolean) => emit("update:modelValue", val),
})
</script>

<template>
  <SwitchRoot
    v-model="checked"
    :disabled="props.disabled"
    data-slot="switch"
    :class="cn(
      'peer data-[state=checked]:bg-primary data-[state=unchecked]:bg-input focus-visible:border-ring focus-visible:ring-ring/50 dark:data-[state=unchecked]:bg-input/80 inline-flex h-[1.75rem] w-14 shrink-0 items-center rounded-full border border-transparent shadow-xs transition-all outline-none focus-visible:ring-[4px] disabled:cursor-not-allowed disabled:opacity-50',
      props.class,
    )"
  >
    <SwitchThumb
      data-slot="switch-thumb"
      :class="cn(
        'bg-background dark:data-[state=unchecked]:bg-foreground dark:data-[state=checked]:bg-primary-foreground pointer-events-none block size-6 rounded-full transition-transform duration-200 ease-in-out data-[state=unchecked]:translate-x-0 data-[state=checked]:translate-x-7'
      )"
    />
  </SwitchRoot>
</template>
