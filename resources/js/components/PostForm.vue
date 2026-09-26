<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { ImageUp, Plus, Star, Trash2, X } from '@lucide/vue';
import { computed, onBeforeUnmount, ref } from 'vue';
import PostController from '@/actions/App/Http/Controllers/Admin/PostController';
import InputError from '@/components/InputError.vue';
import RichTextEditor from '@/components/RichTextEditor.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { shrinkImage } from '@/lib/image';
import { postNoun } from '@/lib/posts';
import type {
    AffiliateLink,
    Post,
    PostCategory,
    PostCategoryOption,
} from '@/types';

const props = defineProps<{
    categories: PostCategoryOption[];
    post?: Post;
    initialCategory?: PostCategory;
}>();

const form = useForm({
    category: (props.post?.category ??
        props.initialCategory ??
        'reflection') as PostCategory,
    title: props.post?.title ?? '',
    slug: props.post?.slug ?? '',
    excerpt: props.post?.excerpt ?? '',
    body: props.post?.body ?? '',
    cover_image: null as File | null,
    remove_cover_image: false,
    reviewed_book_title: props.post?.reviewed_book_title ?? '',
    reviewed_book_author: props.post?.reviewed_book_author ?? '',
    rating: (props.post?.rating ?? null) as number | null,
    affiliate_links: (props.post?.affiliate_links ?? []).map((l) => ({
        ...l,
    })) as AffiliateLink[],
    published: Boolean(props.post?.published_at),
});

const isReview = computed(() => form.category === 'book-review');

const sectionHint: Record<PostCategory, string> = {
    reflection: 'Appears under Reflections on the public site.',
    'book-review':
        'Appears under Book Reviews, with the book cover and your buy links.',
    journal:
        'Private: only admins can see journal entries. Never shown publicly.',
};

const sectionPath: Record<PostCategory, string> = {
    reflection: '/reflections/',
    'book-review': '/reviews/',
    journal: '/journal/',
};

// Slug follows the title until it's edited by hand (new posts only).
let slugTouched = Boolean(props.post);

function slugify(value: string): string {
    return value
        .toLowerCase()
        .trim()
        .replace(/['’]/g, '')
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-+|-+$/g, '');
}

function onTitleInput() {
    if (!slugTouched) form.slug = slugify(form.title);
}

// Cover / featured image
const coverPreview = ref<string | null>(props.post?.cover_image_url ?? null);
let objectUrl: string | null = null;

async function onCoverPicked(event: Event) {
    const picked = (event.target as HTMLInputElement).files?.[0] ?? null;

    if (!picked) return;

    const file = await shrinkImage(picked);

    if (objectUrl) URL.revokeObjectURL(objectUrl);
    objectUrl = URL.createObjectURL(file);

    form.cover_image = file;
    form.remove_cover_image = false;
    coverPreview.value = objectUrl;
}

function removeCover() {
    form.cover_image = null;
    form.remove_cover_image = Boolean(props.post?.cover_image_url);
    coverPreview.value = null;
}

onBeforeUnmount(() => {
    if (objectUrl) URL.revokeObjectURL(objectUrl);
});

function addLink() {
    form.affiliate_links.push({
        label: form.affiliate_links.length ? '' : 'Amazon',
        url: '',
    });
}

function setRating(value: number) {
    form.rating = form.rating === value ? null : value;
}

function submit() {
    const options = {
        forceFormData: true,
        preserveScroll: true,
    };

    if (props.post) {
        form.transform((data) => ({ ...data, _method: 'put' })).post(
            PostController.update.url(props.post.id),
            options,
        );
    } else {
        form.post(PostController.store.url(), options);
    }
}

function linkError(index: number, field: 'label' | 'url'): string | undefined {
    return (form.errors as Record<string, string>)[
        `affiliate_links.${index}.${field}`
    ];
}
</script>

<template>
    <form class="space-y-8" @submit.prevent="submit">
        <!-- Category -->
        <div class="grid gap-2">
            <Label>Section</Label>
            <div
                class="bg-muted/40 inline-flex w-fit rounded-md border p-1"
                role="radiogroup"
                aria-label="Section"
            >
                <button
                    v-for="option in categories"
                    :key="option.value"
                    type="button"
                    role="radio"
                    :aria-checked="form.category === option.value"
                    class="rounded px-4 py-1.5 text-sm font-medium transition-colors"
                    :class="
                        form.category === option.value
                            ? 'bg-background text-foreground shadow-sm'
                            : 'text-muted-foreground hover:text-foreground'
                    "
                    @click="form.category = option.value"
                >
                    {{ option.label }}
                </button>
            </div>
            <p class="text-muted-foreground text-sm">
                {{ sectionHint[form.category] }}
            </p>
            <InputError :message="form.errors.category" />
        </div>

        <!-- Title & slug -->
        <div class="grid gap-2">
            <Label for="title">Title</Label>
            <Input
                id="title"
                v-model="form.title"
                required
                @input="onTitleInput"
            />
            <InputError :message="form.errors.title" />
        </div>

        <div class="grid gap-2">
            <Label for="slug">URL slug</Label>
            <Input
                id="slug"
                v-model="form.slug"
                required
                @input="slugTouched = true"
            />
            <p class="text-muted-foreground text-xs">
                {{ sectionPath[form.category] }}{{ form.slug || '…' }}
            </p>
            <InputError :message="form.errors.slug" />
        </div>

        <!-- Book details (reviews only) -->
        <fieldset v-if="isReview" class="grid gap-5 rounded-lg border p-5">
            <legend class="px-1 text-sm font-semibold">Book details</legend>

            <div class="grid gap-5 sm:grid-cols-2">
                <div class="grid gap-2">
                    <Label for="reviewed_book_title">Book title</Label>
                    <Input
                        id="reviewed_book_title"
                        v-model="form.reviewed_book_title"
                        required
                    />
                    <InputError :message="form.errors.reviewed_book_title" />
                </div>
                <div class="grid gap-2">
                    <Label for="reviewed_book_author">Book author</Label>
                    <Input
                        id="reviewed_book_author"
                        v-model="form.reviewed_book_author"
                    />
                    <InputError :message="form.errors.reviewed_book_author" />
                </div>
            </div>

            <div class="grid gap-2">
                <Label
                    >Rating
                    <span class="text-muted-foreground font-normal"
                        >(optional)</span
                    ></Label
                >
                <div class="flex items-center gap-1">
                    <button
                        v-for="n in 5"
                        :key="n"
                        type="button"
                        :aria-label="`${n} star${n > 1 ? 's' : ''}`"
                        class="text-muted-foreground rounded p-0.5 transition-colors hover:text-amber-500"
                        @click="setRating(n)"
                    >
                        <Star
                            class="size-6"
                            :class="
                                form.rating && n <= form.rating
                                    ? 'fill-amber-400 text-amber-500'
                                    : ''
                            "
                        />
                    </button>
                    <span
                        v-if="form.rating"
                        class="text-muted-foreground ml-2 text-sm"
                        >{{ form.rating }} / 5 — click again to clear</span
                    >
                </div>
                <InputError :message="form.errors.rating" />
            </div>

            <div class="grid gap-3">
                <div>
                    <Label>Affiliate links</Label>
                    <p class="text-muted-foreground mt-1 text-sm">
                        Shown as "Buy" buttons on the review. Marked as
                        sponsored links.
                    </p>
                </div>

                <div
                    v-for="(link, i) in form.affiliate_links"
                    :key="i"
                    class="grid gap-2 sm:grid-cols-[10rem_1fr_auto]"
                >
                    <div>
                        <Input
                            v-model="link.label"
                            placeholder="Amazon"
                            aria-label="Store name"
                            required
                        />
                        <InputError :message="linkError(i, 'label')" />
                    </div>
                    <div>
                        <Input
                            v-model="link.url"
                            type="url"
                            placeholder="https://amzn.to/…"
                            aria-label="Affiliate URL"
                            required
                        />
                        <InputError :message="linkError(i, 'url')" />
                    </div>
                    <Button
                        type="button"
                        variant="ghost"
                        size="icon"
                        aria-label="Remove link"
                        @click="form.affiliate_links.splice(i, 1)"
                    >
                        <X class="size-4" />
                    </Button>
                </div>

                <Button
                    type="button"
                    variant="outline"
                    size="sm"
                    class="w-fit"
                    :disabled="form.affiliate_links.length >= 10"
                    @click="addLink"
                >
                    <Plus class="size-4" />
                    Add link
                </Button>
                <InputError :message="form.errors.affiliate_links" />
            </div>
        </fieldset>

        <!-- Cover / featured image -->
        <div class="grid gap-2">
            <Label for="cover_image">
                {{ isReview ? 'Book cover' : 'Featured image' }}
                <span class="text-muted-foreground font-normal"
                    >(optional)</span
                >
            </Label>
            <div class="flex flex-wrap items-start gap-4">
                <div
                    v-if="coverPreview"
                    class="relative overflow-hidden rounded-md border"
                    :class="isReview ? 'w-32' : 'w-64'"
                >
                    <img
                        :src="coverPreview"
                        alt=""
                        class="w-full object-cover"
                        :class="isReview ? 'aspect-[2/3]' : 'aspect-video'"
                    />
                </div>
                <div class="flex flex-col gap-2">
                    <Button as-child variant="outline" size="sm" class="w-fit">
                        <label for="cover_image" class="cursor-pointer">
                            <ImageUp class="size-4" />
                            {{
                                coverPreview ? 'Replace image' : 'Choose image'
                            }}
                        </label>
                    </Button>
                    <input
                        id="cover_image"
                        type="file"
                        accept="image/*"
                        class="sr-only"
                        @change="onCoverPicked"
                    />
                    <Button
                        v-if="coverPreview"
                        type="button"
                        variant="ghost"
                        size="sm"
                        class="text-muted-foreground w-fit"
                        @click="removeCover"
                    >
                        <Trash2 class="size-4" />
                        Remove
                    </Button>
                </div>
            </div>
            <InputError :message="form.errors.cover_image" />
        </div>

        <!-- Body -->
        <div class="grid gap-2">
            <Label class="capitalize">{{
                isReview ? 'Review' : postNoun(form.category)
            }}</Label>
            <p class="text-muted-foreground text-sm">
                Paste straight from Facebook or a document — formatting and
                images come along. You can also drag images in.
            </p>
            <RichTextEditor v-model="form.body" />
            <InputError :message="form.errors.body" />
        </div>

        <div class="grid gap-2">
            <Label for="excerpt">
                Excerpt
                <span class="text-muted-foreground font-normal"
                    >(optional)</span
                >
            </Label>
            <Textarea
                id="excerpt"
                v-model="form.excerpt"
                placeholder="Short summary for listings. Leave empty to use the opening lines."
            />
            <InputError :message="form.errors.excerpt" />
        </div>

        <div class="flex items-center gap-2">
            <input
                id="published"
                v-model="form.published"
                type="checkbox"
                class="size-4"
            />
            <Label for="published">
                {{ post?.published_at ? 'Published' : 'Publish now' }}
            </Label>
        </div>

        <div
            class="bg-background/95 sticky bottom-0 -mx-1 flex items-center gap-4 border-t px-1 py-4 backdrop-blur"
        >
            <Button type="submit" :disabled="form.processing">
                {{ post ? 'Save changes' : `Save ${postNoun(form.category)}` }}
            </Button>
            <span
                v-if="form.recentlySuccessful"
                class="text-muted-foreground text-sm"
                >Saved.</span
            >
            <span v-if="form.progress" class="text-muted-foreground text-sm"
                >Uploading… {{ form.progress.percentage }}%</span
            >
        </div>
    </form>
</template>
