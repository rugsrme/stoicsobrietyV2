<script setup lang="ts">
import type { BookBlock } from '@/types';

defineProps<{
    blocks: BookBlock[];
}>();

/** Split "*emphasis*" runs out of a block's text, without using v-html. */
function segments(text: string): { text: string; em: boolean }[] {
    return text
        .split(/(\*[^*]+\*)/)
        .filter(Boolean)
        .map((part) =>
            part.length > 2 && part.startsWith('*') && part.endsWith('*')
                ? { text: part.slice(1, -1), em: true }
                : { text: part, em: false },
        );
}
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
            <h4
                v-else-if="block.type === 'subheading'"
                class="mt-6 mb-2 font-semibold italic first:mt-0"
            >
                {{ block.text }}
            </h4>
            <p
                v-else-if="block.type === 'item'"
                class="text-muted-foreground mt-3 flex gap-3 leading-relaxed first:mt-0"
            >
                <span class="text-foreground shrink-0 font-semibold"
                    >{{ block.number }}.</span
                >
                <span>
                    <template v-for="(s, j) in segments(block.text)" :key="j">
                        <em v-if="s.em">{{ s.text }}</em>
                        <template v-else>{{ s.text }}</template>
                    </template>
                </span>
            </p>
            <p
                v-else
                class="text-muted-foreground mt-4 leading-relaxed first:mt-0"
            >
                <template v-for="(s, j) in segments(block.text)" :key="j">
                    <em v-if="s.em">{{ s.text }}</em>
                    <template v-else>{{ s.text }}</template>
                </template>
            </p>
        </template>
    </div>
</template>
