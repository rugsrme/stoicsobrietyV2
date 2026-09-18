<script setup lang="ts">
import { Form, Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import UserController from '@/actions/App/Http/Controllers/Admin/UserController';
import Heading from '@/components/Heading.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { dashboard } from '@/routes/admin';
import { index } from '@/routes/admin/users';
import type { AdminUserSummary, Order } from '@/types';

const props = defineProps<{
    user: AdminUserSummary & { orders_count: number };
    orders: Order[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Admin', href: dashboard() },
            { title: 'Users', href: index() },
            { title: 'User', href: index() },
        ],
    },
});

const page = usePage();
const isSelf = computed(() => page.props.auth.user?.id === props.user.id);

function formatDate(value: string): string {
    return new Date(value).toLocaleDateString(undefined, {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
}
</script>

<template>
    <Head :title="user.display_name || user.name" />

    <div class="max-w-2xl flex-col gap-6">
        <div class="flex items-center justify-between">
            <Heading
                :title="user.display_name || user.name"
                :description="user.email"
            />
            <Badge v-if="user.is_admin" variant="secondary">Admin</Badge>
        </div>

        <dl class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <dt class="text-muted-foreground">Joined</dt>
                <dd>{{ formatDate(user.created_at) }}</dd>
            </div>
            <div>
                <dt class="text-muted-foreground">Orders</dt>
                <dd>{{ user.orders_count }}</dd>
            </div>
        </dl>

        <Form
            v-bind="UserController.update.form(user.id)"
            :data="{ is_admin: !user.is_admin }"
            :options="{ preserveScroll: true }"
            class="mt-6"
        >
            <Button
                type="submit"
                variant="outline"
                :disabled="isSelf && user.is_admin"
                :title="
                    isSelf && user.is_admin
                        ? 'You cannot remove your own admin access'
                        : undefined
                "
            >
                {{ user.is_admin ? 'Remove admin access' : 'Make admin' }}
            </Button>
        </Form>

        <div class="mt-10">
            <h3 class="mb-3 text-sm font-medium">Orders</h3>
            <div
                v-if="orders.length"
                class="border-border divide-border divide-y rounded-lg border"
            >
                <div
                    v-for="order in orders"
                    :key="order.id"
                    class="flex items-center justify-between gap-4 px-4 py-3"
                >
                    <div>
                        <p class="font-medium">
                            {{ order.book?.title ?? 'No book set' }}
                        </p>
                        <p class="text-muted-foreground text-sm">
                            {{ formatDate(order.created_at) }}
                        </p>
                    </div>
                    <p class="text-muted-foreground text-xs capitalize">
                        {{ order.status }}
                    </p>
                </div>
            </div>
            <p v-else class="text-muted-foreground text-sm">No orders yet.</p>
        </div>
    </div>
</template>
