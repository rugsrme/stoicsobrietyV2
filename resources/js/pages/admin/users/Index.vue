<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import Heading from '@/components/Heading.vue';
import { Badge } from '@/components/ui/badge';
import { Input } from '@/components/ui/input';
import { dashboard } from '@/routes/admin';
import { index, show } from '@/routes/admin/users';
import type { AdminUserSummary, Paginated } from '@/types';

const props = defineProps<{
    users: Paginated<AdminUserSummary>;
    search: string;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Admin', href: dashboard() },
            { title: 'Users', href: index() },
        ],
    },
});

const search = ref(props.search);
let debounceTimer: ReturnType<typeof setTimeout> | undefined;

watch(search, (value) => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        router.get(
            index().url,
            { search: value || undefined },
            { preserveState: true, replace: true },
        );
    }, 300);
});

function formatDate(value: string): string {
    return new Date(value).toLocaleDateString(undefined, {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
}
</script>

<template>
    <Head title="Users" />

    <div class="flex flex-col gap-6">
        <div class="flex items-center justify-between gap-4">
            <Heading title="Users" description="Everyone with an account" />
            <Input
                v-model="search"
                type="search"
                placeholder="Search by name or email"
                class="max-w-xs"
            />
        </div>

        <div
            v-if="users.data.length"
            class="border-border divide-border divide-y rounded-lg border"
        >
            <Link
                v-for="user in users.data"
                :key="user.id"
                :href="show(user.id)"
                class="hover:bg-accent flex items-center justify-between gap-4 px-4 py-3 transition-colors"
            >
                <div>
                    <p class="flex items-center gap-2 font-medium">
                        {{ user.display_name || user.name }}
                        <Badge v-if="user.is_admin" variant="secondary"
                            >Admin</Badge
                        >
                    </p>
                    <p class="text-muted-foreground text-sm">
                        {{ user.email }}
                    </p>
                </div>
                <p class="text-muted-foreground text-xs">
                    Joined {{ formatDate(user.created_at) }}
                </p>
            </Link>
        </div>
        <p v-else class="text-muted-foreground text-sm">No users found.</p>

        <nav v-if="users.links.length > 3" class="flex flex-wrap gap-4 text-sm">
            <Link
                v-for="(link, i) in users.links"
                :key="i"
                :href="link.url ?? '#'"
                class="border-b pb-0.5 transition-colors"
                :class="[
                    link.active
                        ? 'border-current'
                        : 'text-muted-foreground hover:text-foreground border-transparent',
                    !link.url && 'pointer-events-none opacity-30',
                ]"
                v-html="link.label"
            />
        </nav>
    </div>
</template>
