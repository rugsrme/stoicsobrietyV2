<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import { useCurrentUrl } from '@/composables/useCurrentUrl';
import { toUrl } from '@/lib/utils';
import { dashboard } from '@/routes/admin';
import { index as ordersIndex } from '@/routes/admin/orders';
import { index as postsIndex } from '@/routes/admin/posts';
import { index as usersIndex } from '@/routes/admin/users';
import type { NavItem } from '@/types';

/**
 * Admin section modules. Add a new module here (plus its route and page)
 * to extend the admin area — e.g. analytics, promotions, etc.
 */
const sidebarNavItems: NavItem[] = [
    { title: 'Overview', href: dashboard() },
    { title: 'Users', href: usersIndex() },
    { title: 'Orders', href: ordersIndex() },
    { title: 'Posts', href: postsIndex() },
];

const { isCurrentOrParentUrl } = useCurrentUrl();
</script>

<template>
    <div class="px-4 py-6">
        <Heading
            title="Admin"
            description="Manage users, orders, and site content"
        />

        <div class="flex flex-col lg:flex-row lg:space-x-12">
            <aside class="w-full max-w-xl lg:w-48">
                <nav
                    class="flex flex-col space-y-1 space-x-0"
                    aria-label="Admin"
                >
                    <Button
                        v-for="item in sidebarNavItems"
                        :key="toUrl(item.href)"
                        variant="ghost"
                        :class="[
                            'w-full justify-start',
                            { 'bg-muted': isCurrentOrParentUrl(item.href) },
                        ]"
                        as-child
                    >
                        <Link :href="item.href">
                            {{ item.title }}
                        </Link>
                    </Button>
                </nav>
            </aside>

            <Separator class="my-6 lg:hidden" />

            <div class="flex-1 md:max-w-3xl">
                <section class="space-y-12">
                    <slot />
                </section>
            </div>
        </div>
    </div>
</template>
