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
    <!-- The Sober Now We Live mark (ring, apex and beams from the profile badge). -->
    <img src="/brand/mark.svg" alt="" :class="className" v-bind="$attrs" />
</template>
