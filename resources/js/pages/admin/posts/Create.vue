<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';
import Heading from '@/components/Heading.vue';
import PostForm from '@/components/PostForm.vue';
import { postNoun } from '@/lib/posts';
import { create, index } from '@/routes/admin/posts';
import type { PostCategory, PostCategoryOption } from '@/types';

const props = defineProps<{
    categories: PostCategoryOption[];
    category: PostCategory;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Writing', href: index() },
            { title: 'New', href: create() },
        ],
    },
});

const title = computed(() => `New ${postNoun(props.category)}`);
</script>

<template>
    <Head :title="title" />

    <Heading
        :title="title"
        description="Write it here, or paste it in from Facebook"
    />

    <PostForm :categories="categories" :initial-category="category" />
</template>
