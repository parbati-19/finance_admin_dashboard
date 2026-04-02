<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

type Props = {
    className?: HTMLAttributes['class'];
};

defineProps<Props>();
</script>

<template>
    <img
        src="/img/app-logo.png"
        alt="Finance Logo"
        :class="className"
        v-bind="$attrs"
    />
</template>
