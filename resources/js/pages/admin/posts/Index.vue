<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { BookOpen, Newspaper, NotebookPen } from '@lucide/vue';
import PostController from '@/actions/App/Http/Controllers/Admin/PostController';
import Heading from '@/components/Heading.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { postUrl } from '@/lib/posts';
import { create, edit, index } from '@/routes/admin/posts';
import type { PostCategory, PostCategoryOption, PostSummary } from '@/types';

const props = defineProps<{
    posts: (Pick<
        PostSummary,
        | 'id'
        | 'category'
        | 'title'
        | 'slug'
        | 'published_at'
        | 'cover_image_url'
        | 'is_featured'
    > & { updated_at: string })[];
    category: PostCategory | null;
    categories: PostCategoryOption[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Writing', href: index() }],
    },
});

function status(publishedAt: string | null): string {
    if (publishedAt === null) return 'Draft';

    return new Date(publishedAt) <= new Date() ? 'Published' : 'Scheduled';
}

function destroy(post: Pick<PostSummary, 'id' | 'title'>) {
    if (!confirm(`Delete “${post.title}”? This can't be undone.`)) return;

    router.delete(PostController.destroy.url(post.id), {
        preserveScroll: true,
    });
}

function categoryLabel(value: PostCategory): string {
    return props.categories.find((c) => c.value === value)?.label ?? value;
}
</script>

<template>
    <Head title="Writing" />

    <div class="flex flex-col gap-6">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <Heading
                title="Writing"
                description="Reflections and book reviews for the public site, plus your private journal"
            />
            <div class="flex flex-wrap gap-2">
                <Button as-child variant="outline">
                    <Link :href="create({ query: { category: 'journal' } })">
                        <NotebookPen class="size-4" />
                        New journal entry
                    </Link>
                </Button>
                <Button as-child variant="outline">
                    <Link
                        :href="create({ query: { category: 'book-review' } })"
                    >
                        <BookOpen class="size-4" />
                        New review
                    </Link>
                </Button>
                <Button as-child>
                    <Link :href="create({ query: { category: 'reflection' } })">
                        <Newspaper class="size-4" />
                        New reflection
                    </Link>
                </Button>
            </div>
        </div>

        <nav class="flex gap-1 border-b" aria-label="Filter by section">
            <Link
                v-for="tab in [{ value: null, plural: 'All' }, ...categories]"
                :key="tab.value ?? 'all'"
                :href="
                    index(tab.value ? { query: { category: tab.value } } : {})
                "
                class="-mb-px border-b-2 px-3 py-2 text-sm transition-colors"
                :class="
                    category === tab.value
                        ? 'border-foreground text-foreground font-medium'
                        : 'text-muted-foreground hover:text-foreground border-transparent'
                "
                preserve-state
            >
                {{ tab.plural }}
            </Link>
        </nav>

        <div
            v-if="posts.length"
            class="divide-border border-border divide-y rounded-lg border"
        >
            <div
                v-for="post in posts"
                :key="post.id"
                class="flex items-center gap-4 px-4 py-3"
            >
                <div
                    class="bg-muted flex size-12 shrink-0 items-center justify-center overflow-hidden rounded"
                >
                    <img
                        v-if="post.cover_image_url"
                        :src="post.cover_image_url"
                        alt=""
                        class="size-full object-cover"
                    />
                    <BookOpen
                        v-else-if="post.category === 'book-review'"
                        class="text-muted-foreground size-5"
                    />
                    <NotebookPen
                        v-else-if="post.category === 'journal'"
                        class="text-muted-foreground size-5"
                    />
                    <Newspaper v-else class="text-muted-foreground size-5" />
                </div>

                <div class="min-w-0 flex-1">
                    <Link
                        :href="edit(post.id)"
                        class="block truncate font-medium hover:underline"
                        >{{ post.title }}</Link
                    >
                    <div
                        class="text-muted-foreground mt-1 flex items-center gap-2 text-sm"
                    >
                        <Badge variant="secondary">{{
                            categoryLabel(post.category)
                        }}</Badge>
                        <span>{{ status(post.published_at) }}</span>
                        <Badge v-if="post.is_featured" variant="outline"
                            >On home page</Badge
                        >
                    </div>
                </div>

                <div class="flex shrink-0 items-center gap-2">
                    <Button
                        v-if="status(post.published_at) === 'Published'"
                        as-child
                        variant="ghost"
                        size="sm"
                    >
                        <Link :href="postUrl(post)">View</Link>
                    </Button>
                    <Button as-child variant="secondary" size="sm">
                        <Link :href="edit(post.id)">Edit</Link>
                    </Button>
                    <Button
                        type="button"
                        variant="destructive"
                        size="sm"
                        @click="destroy(post)"
                        >Delete</Button
                    >
                </div>
            </div>
        </div>

        <p v-else class="text-muted-foreground text-sm">Nothing here yet.</p>
    </div>
</template>
