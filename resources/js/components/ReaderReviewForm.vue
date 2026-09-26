<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import ReaderReviewController from '@/actions/App/Http/Controllers/Admin/ReaderReviewController';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import type { ReaderReview } from '@/types';

const props = defineProps<{
    review?: ReaderReview;
}>();

const form = useForm({
    quote: props.review?.quote ?? '',
    author: props.review?.author ?? '',
    context: props.review?.context ?? '',
    sort_order: props.review?.sort_order ?? 0,
    is_published: props.review?.is_published ?? true,
    is_featured: props.review?.is_featured ?? true,
});

function submit() {
    if (props.review) {
        form.submit(ReaderReviewController.update(props.review.id), {
            preserveScroll: true,
        });
    } else {
        form.submit(ReaderReviewController.store());
    }
}
</script>

<template>
    <form class="max-w-2xl space-y-6" @submit.prevent="submit">
        <div class="grid gap-2">
            <Label for="quote">What they said</Label>
            <Textarea
                id="quote"
                v-model="form.quote"
                rows="4"
                required
                placeholder="One to three sentences reads best in the carousel."
            />
            <InputError :message="form.errors.quote" />
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div class="grid gap-2">
                <Label for="author">Name</Label>
                <Input
                    id="author"
                    v-model="form.author"
                    required
                    placeholder="How they'd like to be credited"
                />
                <InputError :message="form.errors.author" />
            </div>
            <div class="grid gap-2">
                <Label for="context">
                    Where from
                    <span class="text-muted-foreground font-normal"
                        >(optional)</span
                    >
                </Label>
                <Input
                    id="context"
                    v-model="form.context"
                    placeholder="e.g. Amazon review, sponsor, pastor"
                />
                <InputError :message="form.errors.context" />
            </div>
        </div>

        <div class="grid max-w-40 gap-2">
            <Label for="sort_order">Position</Label>
            <Input
                id="sort_order"
                v-model.number="form.sort_order"
                type="number"
                min="0"
            />
            <p class="text-muted-foreground text-sm">Lower shows first.</p>
            <InputError :message="form.errors.sort_order" />
        </div>

        <div class="grid gap-3">
            <div class="flex items-center gap-2">
                <input
                    id="is_published"
                    v-model="form.is_published"
                    type="checkbox"
                    class="size-4"
                />
                <Label for="is_published">Published</Label>
            </div>
            <div class="flex items-start gap-2">
                <input
                    id="is_featured"
                    v-model="form.is_featured"
                    type="checkbox"
                    class="mt-0.5 size-4"
                />
                <div class="grid gap-1">
                    <Label for="is_featured">Show in home carousel</Label>
                    <p class="text-muted-foreground text-sm">
                        Only published reviews appear, even when this is ticked.
                    </p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <Button type="submit" :disabled="form.processing">
                {{ review ? 'Save changes' : 'Add review' }}
            </Button>
            <span
                v-if="form.recentlySuccessful"
                class="text-muted-foreground text-sm"
                >Saved.</span
            >
        </div>
    </form>
</template>
