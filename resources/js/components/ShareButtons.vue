<script setup lang="ts">
import { Check, Link2, Share2 } from '@lucide/vue';
import { computed, onMounted, ref } from 'vue';

const props = defineProps<{
    title: string;
}>();

const url = ref('');
const facebookUrl = computed(
    () =>
        `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url.value)}`,
);

// Phones offer their own share sheet, which reaches Instagram, Messenger, etc.
const canShareNatively = ref(false);
const copied = ref(false);

onMounted(() => {
    url.value = window.location.origin + window.location.pathname;
    canShareNatively.value = typeof navigator.share === 'function';
});

function shareNatively() {
    navigator.share({ title: props.title, url: url.value }).catch(() => {
        // Dismissed by the reader.
    });
}

async function copyLink() {
    await navigator.clipboard.writeText(url.value);
    copied.value = true;
    setTimeout(() => (copied.value = false), 2000);
}

const button =
    'inline-flex items-center gap-2 rounded border border-[var(--site-line)] bg-[var(--site-card)] px-4 py-2 text-sm transition-colors hover:text-[var(--site-ink)]';
</script>

<template>
    <div class="flex flex-wrap items-center gap-3 text-[var(--site-ink-soft)]">
        <span class="text-sm text-[var(--site-ink-faint)]">Share</span>
        <button
            v-if="canShareNatively"
            type="button"
            :class="button"
            @click="shareNatively"
        >
            <Share2 class="size-4" /> Share…
        </button>
        <a
            :href="facebookUrl"
            target="_blank"
            rel="noopener noreferrer"
            :class="button"
        >
            Facebook
        </a>
        <button type="button" :class="button" @click="copyLink">
            <component :is="copied ? Check : Link2" class="size-4" />
            {{ copied ? 'Link copied' : 'Copy link' }}
        </button>
    </div>
</template>
