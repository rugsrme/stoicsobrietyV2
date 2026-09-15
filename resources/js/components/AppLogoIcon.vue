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
    <svg
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 100 100"
        :class="className"
        v-bind="$attrs"
    >
        <path
            fill="currentColor"
            fill-rule="evenodd"
            clip-rule="evenodd"
            d="M50 4a46 46 0 1 1 0 92 46 46 0 0 1 0-92Zm0 8a38 38 0 1 0 0 76 38 38 0 0 0 0-76Z"
        />
        <path
            fill="currentColor"
            d="M43 22h14a4 4 0 0 1 4 4v10a4 4 0 0 1-4 4h-1.2l14.7 26a4 4 0 0 1-3.5 6H32a4 4 0 0 1-3.5-6l14.7-26H42a4 4 0 0 1-4-4V26a4 4 0 0 1 4-4h1Z"
        />
    </svg>
</template>
