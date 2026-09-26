<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import ReaderReviewForm from '@/components/ReaderReviewForm.vue';
import { dashboard } from '@/routes/admin';
import { index } from '@/routes/admin/reader-reviews';
import type { ReaderReview } from '@/types';

defineProps<{
    review: ReaderReview;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Admin', href: dashboard() },
            { title: 'Reader reviews', href: index() },
            { title: 'Edit', href: index() },
        ],
    },
});
</script>

<template>
    <Head title="Edit reader review" />

    <Heading title="Edit reader review" :description="review.author" />

    <ReaderReviewForm :key="review.id" :review="review" />
</template>
