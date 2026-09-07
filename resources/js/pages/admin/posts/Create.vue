<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import PostController from '@/actions/App/Http/Controllers/Admin/PostController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { create, index } from '@/routes/admin/posts';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Posts', href: index() },
            { title: 'New post', href: create() },
        ],
    },
});

const slug = ref('');
let slugTouched = false;

function slugify(value: string): string {
    return value
        .toLowerCase()
        .trim()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-+|-+$/g, '');
}

function onTitleInput(event: Event) {
    if (slugTouched) return;
    slug.value = slugify((event.target as HTMLInputElement).value);
}

function onSlugInput() {
    slugTouched = true;
}
</script>

<template>
    <Head title="New post" />

    <div class="max-w-2xl">
        <Heading title="New post" description="Draft a new journal entry" />

        <Form
            v-bind="PostController.store.form()"
            class="space-y-6"
            v-slot="{ errors, processing }"
        >
            <div class="grid gap-2">
                <Label for="title">Title</Label>
                <Input id="title" name="title" required @input="onTitleInput" />
                <InputError :message="errors.title" />
            </div>

            <div class="grid gap-2">
                <Label for="slug">Slug</Label>
                <Input
                    id="slug"
                    name="slug"
                    v-model="slug"
                    required
                    @input="onSlugInput"
                />
                <InputError :message="errors.slug" />
            </div>

            <div class="grid gap-2">
                <Label for="excerpt">Excerpt</Label>
                <Textarea
                    id="excerpt"
                    name="excerpt"
                    placeholder="Optional short summary"
                />
                <InputError :message="errors.excerpt" />
            </div>

            <div class="grid gap-2">
                <Label for="body">Body</Label>
                <Textarea id="body" name="body" class="min-h-64" required />
                <InputError :message="errors.body" />
            </div>

            <div class="flex items-center gap-2">
                <input
                    id="published"
                    type="checkbox"
                    name="published"
                    value="1"
                    class="size-4"
                />
                <Label for="published">Publish immediately</Label>
            </div>

            <div class="flex items-center gap-4">
                <Button type="submit" :disabled="processing">Save post</Button>
            </div>
        </Form>
    </div>
</template>
