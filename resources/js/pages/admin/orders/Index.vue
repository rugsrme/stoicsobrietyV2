<script setup lang="ts">
import { Form, Head, Link, router } from '@inertiajs/vue3';
import OrderController from '@/actions/App/Http/Controllers/Admin/OrderController';
import Heading from '@/components/Heading.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { dashboard } from '@/routes/admin';
import { create, edit, index } from '@/routes/admin/orders';
import type { Order, OrderStatus, Paginated } from '@/types';

const props = defineProps<{
    orders: Paginated<Order>;
    status: OrderStatus | null;
    statuses: OrderStatus[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Admin', href: dashboard() },
            { title: 'Orders', href: index() },
        ],
    },
});

const statusVariant: Record<
    OrderStatus,
    'default' | 'secondary' | 'destructive' | 'outline'
> = {
    pending: 'outline',
    paid: 'secondary',
    fulfilled: 'default',
    cancelled: 'destructive',
    refunded: 'destructive',
};

function filterByStatus(value: string | null) {
    router.get(index().url, value ? { status: value } : {}, {
        preserveState: true,
    });
}

function formatDate(value: string | null): string {
    if (!value) return '—';
    return new Date(value).toLocaleDateString(undefined, {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
}
</script>

<template>
    <Head title="Orders" />

    <div class="flex flex-col gap-6">
        <div class="flex items-center justify-between">
            <Heading
                title="Orders"
                description="Manually logged and future direct-sale orders"
            />
            <Button as-child>
                <Link :href="create()">Log order</Link>
            </Button>
        </div>

        <div class="flex flex-wrap gap-2">
            <Button
                size="sm"
                :variant="!props.status ? 'default' : 'outline'"
                @click="filterByStatus(null)"
            >
                All
            </Button>
            <Button
                v-for="option in statuses"
                :key="option"
                size="sm"
                :variant="props.status === option ? 'default' : 'outline'"
                class="capitalize"
                @click="filterByStatus(option)"
            >
                {{ option }}
            </Button>
        </div>

        <div
            v-if="orders.data.length"
            class="border-border divide-border divide-y rounded-lg border"
        >
            <div
                v-for="order in orders.data"
                :key="order.id"
                class="flex items-center justify-between gap-4 px-4 py-3"
            >
                <Link :href="edit(order.id)" class="min-w-0 flex-1">
                    <p class="font-medium">{{ order.customer_name }}</p>
                    <p class="text-muted-foreground truncate text-sm">
                        {{ order.book?.title ?? 'No book set' }} ·
                        {{ order.source }}
                        <span v-if="order.amount_formatted">
                            · {{ order.currency.toUpperCase() }}
                            {{ order.amount_formatted }}
                        </span>
                    </p>
                </Link>
                <div class="flex items-center gap-3">
                    <p class="text-muted-foreground hidden text-xs sm:block">
                        {{ formatDate(order.ordered_at) }}
                    </p>
                    <Badge
                        :variant="statusVariant[order.status]"
                        class="capitalize"
                    >
                        {{ order.status }}
                    </Badge>
                    <Button as-child variant="secondary" size="sm">
                        <Link :href="edit(order.id)">Edit</Link>
                    </Button>
                    <Form
                        v-bind="OrderController.destroy.form(order.id)"
                        :options="{ preserveScroll: true }"
                    >
                        <Button type="submit" variant="destructive" size="sm">
                            Delete
                        </Button>
                    </Form>
                </div>
            </div>
        </div>
        <p v-else class="text-muted-foreground text-sm">
            No orders logged yet.
        </p>

        <nav
            v-if="orders.links.length > 3"
            class="flex flex-wrap gap-4 text-sm"
        >
            <Link
                v-for="(link, i) in orders.links"
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
