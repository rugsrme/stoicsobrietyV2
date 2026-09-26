<script setup lang="ts">
import {
    AlignCenter,
    AlignLeft,
    AlignRight,
    Bold,
    Heading2,
    Heading3,
    ImagePlus,
    Italic,
    Link as LinkIcon,
    List,
    ListOrdered,
    Minus,
    Quote,
    Redo2,
    Strikethrough,
    Underline as UnderlineIcon,
    Undo2,
} from '@lucide/vue';
import Image from '@tiptap/extension-image';
import Placeholder from '@tiptap/extension-placeholder';
import TextAlign from '@tiptap/extension-text-align';
import StarterKit from '@tiptap/starter-kit';
import { EditorContent, useEditor } from '@tiptap/vue-3';
import type { Component } from 'vue';
import { onBeforeUnmount, ref, watch } from 'vue';
import { toast } from 'vue-sonner';
import { Spinner } from '@/components/ui/spinner';
import { shrinkImage } from '@/lib/image';
import { store as storeImage } from '@/routes/admin/posts/images';

const model = defineModel<string>({ required: true });

const props = defineProps<{
    placeholder?: string;
}>();

const uploading = ref(0);
const fileInput = ref<HTMLInputElement | null>(null);

function xsrfToken(): string {
    const match = document.cookie.match(/(?:^|;\s*)XSRF-TOKEN=([^;]+)/);

    return match ? decodeURIComponent(match[1]) : '';
}

async function upload(file: File): Promise<string | null> {
    uploading.value++;

    try {
        const body = new FormData();
        body.append('image', await shrinkImage(file));

        const response = await fetch(storeImage().url, {
            method: 'POST',
            body,
            credentials: 'same-origin',
            headers: {
                Accept: 'application/json',
                'X-XSRF-TOKEN': xsrfToken(),
            },
        });

        if (!response.ok) {
            const error = await response.json().catch(() => null);
            const message: string | undefined =
                error?.errors?.image?.[0] ?? error?.message;

            toast.error(
                response.status === 413 || message?.includes('failed to upload')
                    ? `“${file.name}” is too large to upload. Try a smaller image.`
                    : (message ?? 'The image could not be uploaded.'),
            );

            return null;
        }

        return (await response.json()).url as string;
    } catch {
        toast.error('The image could not be uploaded.');

        return null;
    } finally {
        uploading.value--;
    }
}

async function insertImages(files: File[], pos?: number) {
    for (const file of files) {
        const url = await upload(file);

        if (!url || !editor.value) continue;

        const chain = editor.value.chain().focus();

        if (pos !== undefined) {
            chain.insertContentAt(pos, {
                type: 'image',
                attrs: { src: url, alt: '' },
            });
        } else {
            chain.setImage({ src: url, alt: '' });
        }

        chain.run();
    }
}

function imageFiles(list: FileList | null | undefined): File[] {
    return Array.from(list ?? []).filter((f) => f.type.startsWith('image/'));
}

const editor = useEditor({
    content: model.value,
    extensions: [
        StarterKit.configure({
            heading: { levels: [2, 3, 4] },
            link: {
                openOnClick: false,
                autolink: true,
                defaultProtocol: 'https',
            },
        }),
        Image.configure({ inline: false }),
        TextAlign.configure({ types: ['heading', 'paragraph'] }),
        Placeholder.configure({
            placeholder:
                props.placeholder ??
                'Write, or paste a post from Facebook or anywhere else…',
        }),
    ],
    editorProps: {
        attributes: {
            class: 'rich-content min-h-80 px-4 py-3 text-[15px]',
        },
        handlePaste(_view, event) {
            const files = imageFiles(event.clipboardData?.files);

            // Pasted text/HTML (with or without images) goes through the
            // normal paste path; only raw image data is uploaded here.
            if (!files.length || event.clipboardData?.getData('text/html')) {
                return false;
            }

            insertImages(files);

            return true;
        },
        handleDrop(view, event) {
            const files = imageFiles(event.dataTransfer?.files);

            if (!files.length) return false;

            event.preventDefault();

            const pos = view.posAtCoords({
                left: event.clientX,
                top: event.clientY,
            })?.pos;

            insertImages(files, pos);

            return true;
        },
    },
    onUpdate({ editor }) {
        model.value = editor.isEmpty ? '' : editor.getHTML();
    },
});

watch(model, (value) => {
    if (!editor.value) return;

    const current = editor.value.isEmpty ? '' : editor.value.getHTML();

    if (value !== current) {
        editor.value.commands.setContent(value, { emitUpdate: false });
    }
});

onBeforeUnmount(() => editor.value?.destroy());

function setLink() {
    if (!editor.value) return;

    const previous = editor.value.getAttributes('link').href as
        | string
        | undefined;
    const url = window.prompt('Link URL (leave empty to remove)', previous);

    if (url === null) return;

    const chain = editor.value.chain().focus().extendMarkRange('link');

    if (url.trim() === '') {
        chain.unsetLink().run();
    } else {
        chain.setLink({ href: url.trim() }).run();
    }
}

async function onFilePicked(event: Event) {
    const input = event.target as HTMLInputElement;
    await insertImages(imageFiles(input.files));
    input.value = '';
}

type ToolbarButton = {
    label: string;
    icon: Component;
    action: () => void;
    active?: () => boolean;
};

const groups: ToolbarButton[][] = [
    [
        {
            label: 'Bold',
            icon: Bold,
            action: () => editor.value?.chain().focus().toggleBold().run(),
            active: () => !!editor.value?.isActive('bold'),
        },
        {
            label: 'Italic',
            icon: Italic,
            action: () => editor.value?.chain().focus().toggleItalic().run(),
            active: () => !!editor.value?.isActive('italic'),
        },
        {
            label: 'Underline',
            icon: UnderlineIcon,
            action: () => editor.value?.chain().focus().toggleUnderline().run(),
            active: () => !!editor.value?.isActive('underline'),
        },
        {
            label: 'Strikethrough',
            icon: Strikethrough,
            action: () => editor.value?.chain().focus().toggleStrike().run(),
            active: () => !!editor.value?.isActive('strike'),
        },
    ],
    [
        {
            label: 'Heading',
            icon: Heading2,
            action: () =>
                editor.value?.chain().focus().toggleHeading({ level: 2 }).run(),
            active: () => !!editor.value?.isActive('heading', { level: 2 }),
        },
        {
            label: 'Subheading',
            icon: Heading3,
            action: () =>
                editor.value?.chain().focus().toggleHeading({ level: 3 }).run(),
            active: () => !!editor.value?.isActive('heading', { level: 3 }),
        },
        {
            label: 'Quote',
            icon: Quote,
            action: () =>
                editor.value?.chain().focus().toggleBlockquote().run(),
            active: () => !!editor.value?.isActive('blockquote'),
        },
    ],
    [
        {
            label: 'Bulleted list',
            icon: List,
            action: () =>
                editor.value?.chain().focus().toggleBulletList().run(),
            active: () => !!editor.value?.isActive('bulletList'),
        },
        {
            label: 'Numbered list',
            icon: ListOrdered,
            action: () =>
                editor.value?.chain().focus().toggleOrderedList().run(),
            active: () => !!editor.value?.isActive('orderedList'),
        },
    ],
    [
        {
            label: 'Align left',
            icon: AlignLeft,
            action: () =>
                editor.value?.chain().focus().setTextAlign('left').run(),
            active: () => !!editor.value?.isActive({ textAlign: 'left' }),
        },
        {
            label: 'Align center',
            icon: AlignCenter,
            action: () =>
                editor.value?.chain().focus().setTextAlign('center').run(),
            active: () => !!editor.value?.isActive({ textAlign: 'center' }),
        },
        {
            label: 'Align right',
            icon: AlignRight,
            action: () =>
                editor.value?.chain().focus().setTextAlign('right').run(),
            active: () => !!editor.value?.isActive({ textAlign: 'right' }),
        },
    ],
    [
        {
            label: 'Link',
            icon: LinkIcon,
            action: setLink,
            active: () => !!editor.value?.isActive('link'),
        },
        {
            label: 'Insert image',
            icon: ImagePlus,
            action: () => fileInput.value?.click(),
        },
        {
            label: 'Divider',
            icon: Minus,
            action: () =>
                editor.value?.chain().focus().setHorizontalRule().run(),
        },
    ],
    [
        {
            label: 'Undo',
            icon: Undo2,
            action: () => editor.value?.chain().focus().undo().run(),
        },
        {
            label: 'Redo',
            icon: Redo2,
            action: () => editor.value?.chain().focus().redo().run(),
        },
    ],
];
</script>

<template>
    <div
        class="border-input bg-background focus-within:ring-ring/50 overflow-hidden rounded-md border shadow-xs focus-within:ring-[3px]"
    >
        <div
            class="bg-muted/60 sticky top-0 z-10 flex flex-wrap items-center gap-1 border-b px-2 py-1.5 backdrop-blur"
            role="toolbar"
            aria-label="Formatting"
        >
            <template v-for="(group, g) in groups" :key="g">
                <span v-if="g > 0" class="bg-border mx-1 h-5 w-px" />
                <button
                    v-for="button in group"
                    :key="button.label"
                    type="button"
                    :title="button.label"
                    :aria-label="button.label"
                    :aria-pressed="button.active ? button.active() : undefined"
                    class="text-muted-foreground hover:bg-accent hover:text-foreground inline-flex size-8 items-center justify-center rounded transition-colors"
                    :class="
                        button.active?.() ? 'bg-accent text-foreground' : ''
                    "
                    @click="button.action"
                >
                    <component :is="button.icon" class="size-4" />
                </button>
            </template>

            <span
                v-if="uploading"
                class="text-muted-foreground ml-auto inline-flex items-center gap-2 pr-1 text-xs"
            >
                <Spinner class="size-3.5" />
                Uploading image…
            </span>
        </div>

        <EditorContent :editor="editor" />

        <input
            ref="fileInput"
            type="file"
            accept="image/*"
            multiple
            class="hidden"
            @change="onFilePicked"
        />
    </div>
</template>
