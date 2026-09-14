<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import PublicLayout from '@/layouts/PublicLayout.vue';
</script>

<template>
    <Head title="Affiliate Disclosure" />

    <PublicLayout>
        <article class="mx-auto max-w-3xl px-6 pt-10 pb-24 lg:px-10 lg:pt-16">
            <h1
                class="font-serif-display text-4xl font-medium tracking-tight sm:text-5xl"
            >
                Affiliate Disclosure
            </h1>

            <div
                class="mt-12 space-y-6 leading-relaxed text-current/80"
            >
                <p>
                    Some links on this website are affiliate links. This
                    means that if you follow one of these links and make a
                    qualifying purchase, I may receive a small commission at
                    no additional cost to you.
                </p>

                <p>As an Amazon Associate I earn from qualifying purchases.</p>

                <p>
                    My recommendations and reviews are based on my own
                    opinions and experiences. Affiliate relationships do not
                    determine what I recommend, what I say about a book or
                    resource, or whether my review is positive or negative.
                </p>

                <p>
                    The purpose of this website is to share books, ideas, and
                    resources that I believe may be worthwhile for people
                    interested in recovery, Stoic philosophy, spirituality,
                    personal growth, and related subjects. When an affiliate
                    relationship exists, I will disclose it.
                </p>

                <p>
                    Affiliate commissions help support the cost of
                    maintaining this website and creating its content.
                </p>
            </div>
        </article>
    </PublicLayout>
</template>
