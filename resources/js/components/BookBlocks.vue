<script setup lang="ts">
import type { BookBlock } from '@/types';

defineProps<{
    blocks: BookBlock[];
}>();
</script>

<template>
    <div class="max-w-2xl">
        <template v-for="(block, i) in blocks" :key="i">
            <h3
                v-if="block.type === 'heading'"
                class="mt-8 mb-3 text-lg font-semibold first:mt-0"
            >
                {{ block.text }}
            </h3>
            <p v-else class="mt-4 leading-relaxed text-muted-foreground first:mt-0">
                {{ block.text }}
            </p>
        </template>
    </div>
</template>
