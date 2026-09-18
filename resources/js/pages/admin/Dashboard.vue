<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { show as usersShow } from '@/routes/admin/users';
import type { AdminStats, AdminUserSummary, Order } from '@/types';

defineProps<{
    stats: AdminStats;
    recentUsers: AdminUserSummary[];
    recentOrders: Order[];
}>();

function formatDate(value: string): string {
    return new Date(value).toLocaleDateString(undefined, {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
}
</script>

<template>
    <Head title="Admin" />

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <Card>
            <CardHeader>
                <CardTitle>Users</CardTitle>
            </CardHeader>
            <CardContent>
                <p class="text-2xl font-semibold">{{ stats.total_users }}</p>
                <p class="text-muted-foreground text-sm">
                    {{ stats.total_admins }} admin{{
                        stats.total_admins === 1 ? '' : 's'
                    }}
                    · {{ stats.new_users_last_30_days }} new in 30 days
                </p>
            </CardContent>
        </Card>

        <Card>
            <CardHeader>
                <CardTitle>Orders</CardTitle>
            </CardHeader>
            <CardContent>
                <p class="text-2xl font-semibold">{{ stats.total_orders }}</p>
                <p class="text-muted-foreground text-sm">logged to date</p>
            </CardContent>
        </Card>

        <Card>
            <CardHeader>
                <CardTitle>Needs fulfillment</CardTitle>
            </CardHeader>
            <CardContent>
                <p class="text-2xl font-semibold">
                    {{ stats.orders_needing_fulfillment }}
                </p>
                <p class="text-muted-foreground text-sm">awaiting delivery</p>
            </CardContent>
        </Card>
    </div>

    <div class="mt-10 grid gap-8 lg:grid-cols-2">
        <div>
            <h3 class="mb-3 text-sm font-medium">Recent users</h3>
            <div
                v-if="recentUsers.length"
                class="border-border divide-border divide-y rounded-lg border"
            >
                <Link
                    v-for="user in recentUsers"
                    :key="user.id"
                    :href="usersShow(user.id)"
                    class="hover:bg-accent flex items-center justify-between gap-4 px-4 py-3 transition-colors"
                >
                    <div>
                        <p class="font-medium">
                            {{ user.display_name || user.name }}
                        </p>
                        <p class="text-muted-foreground text-sm">
                            {{ user.email }}
                        </p>
                    </div>
                    <p class="text-muted-foreground text-xs">
                        {{ formatDate(user.created_at) }}
                    </p>
                </Link>
            </div>
            <p v-else class="text-muted-foreground text-sm">No users yet.</p>
        </div>

        <div>
            <h3 class="mb-3 text-sm font-medium">Recent orders</h3>
            <div
                v-if="recentOrders.length"
                class="border-border divide-border divide-y rounded-lg border"
            >
                <div
                    v-for="order in recentOrders"
                    :key="order.id"
                    class="flex items-center justify-between gap-4 px-4 py-3"
                >
                    <div>
                        <p class="font-medium">{{ order.customer_name }}</p>
                        <p class="text-muted-foreground text-sm">
                            {{ order.book?.title ?? 'No book set' }}
                        </p>
                    </div>
                    <p class="text-muted-foreground text-xs capitalize">
                        {{ order.status }}
                    </p>
                </div>
            </div>
            <p v-else class="text-muted-foreground text-sm">
                No orders logged yet.
            </p>
        </div>
    </div>
</template>
