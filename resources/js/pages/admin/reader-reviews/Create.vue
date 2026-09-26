<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import ReaderReviewForm from '@/components/ReaderReviewForm.vue';
import { dashboard } from '@/routes/admin';
import { create, index } from '@/routes/admin/reader-reviews';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Admin', href: dashboard() },
            { title: 'Reader reviews', href: index() },
            { title: 'Add', href: create() },
        ],
    },
});
</script>

<template>
    <Head title="Add reader review" />

    <Heading
        title="Add reader review"
        description="A short quote from a reader, for the home page carousel"
    />

    <ReaderReviewForm />
</template>
