<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Plus } from '@lucide/vue';
import ReaderReviewController from '@/actions/App/Http/Controllers/Admin/ReaderReviewController';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { dashboard } from '@/routes/admin';
import { create, edit, index } from '@/routes/admin/reader-reviews';
import type { ReaderReview } from '@/types';

defineProps<{
    reviews: ReaderReview[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Admin', href: dashboard() },
            { title: 'Reader reviews', href: index() },
        ],
    },
});

function toggle(
    review: ReaderReview,
    field: 'is_published' | 'is_featured',
    value: boolean,
) {
    router.patch(
        ReaderReviewController.update.url(review.id),
        { [field]: value },
        { preserveScroll: true },
    );
}

function destroy(review: ReaderReview) {
    if (
        !confirm(
            `Delete the review from ${review.author}? This can't be undone.`,
        )
    )
        return;

    router.delete(ReaderReviewController.destroy.url(review.id), {
        preserveScroll: true,
    });
}
</script>

<template>
    <Head title="Reader reviews" />

    <div class="flex flex-col gap-6">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <Heading
                title="Reader reviews"
                description="Quotes from readers. Tick “Home carousel” to show one on the home page."
            />
            <Button as-child>
                <Link :href="create()">
                    <Plus class="size-4" />
                    Add review
                </Link>
            </Button>
        </div>

        <div
            v-if="reviews.length"
            class="divide-border border-border divide-y rounded-lg border"
        >
            <div
                v-for="review in reviews"
                :key="review.id"
                class="flex flex-wrap items-center gap-x-6 gap-y-3 px-4 py-3"
            >
                <div class="min-w-0 flex-1 basis-64">
                    <Link
                        :href="edit(review.id)"
                        class="line-clamp-2 hover:underline"
                        >&ldquo;{{ review.quote }}&rdquo;</Link
                    >
                    <p class="text-muted-foreground mt-1 text-sm">
                        {{ review.author
                        }}<span v-if="review.context">
                            &mdash; {{ review.context }}</span
                        >
                    </p>
                </div>

                <div class="flex shrink-0 items-center gap-5 text-sm">
                    <label class="flex items-center gap-2">
                        <input
                            type="checkbox"
                            class="size-4"
                            :checked="review.is_published"
                            @change="
                                toggle(
                                    review,
                                    'is_published',
                                    ($event.target as HTMLInputElement).checked,
                                )
                            "
                        />
                        Published
                    </label>
                    <label class="flex items-center gap-2">
                        <input
                            type="checkbox"
                            class="size-4"
                            :checked="review.is_featured"
                            @change="
                                toggle(
                                    review,
                                    'is_featured',
                                    ($event.target as HTMLInputElement).checked,
                                )
                            "
                        />
                        Home carousel
                    </label>
                </div>

                <div class="flex shrink-0 items-center gap-2">
                    <Button as-child variant="secondary" size="sm">
                        <Link :href="edit(review.id)">Edit</Link>
                    </Button>
                    <Button
                        type="button"
                        variant="destructive"
                        size="sm"
                        @click="destroy(review)"
                        >Delete</Button
                    >
                </div>
            </div>
        </div>

        <p v-else class="text-muted-foreground text-sm">
            No reader reviews yet. The home page hides the carousel until one is
            published and ticked.
        </p>
    </div>
</template>
