<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import OrderController from '@/actions/App/Http/Controllers/Admin/OrderController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { dashboard } from '@/routes/admin';
import { index } from '@/routes/admin/orders';
import type {
    Order,
    OrderBook,
    OrderDeliveryMethod,
    OrderDeliveryStatus,
    OrderSource,
    OrderStatus,
} from '@/types';

const props = defineProps<{
    order: Order;
    books: OrderBook[];
    sources: OrderSource[];
    statuses: OrderStatus[];
    deliveryMethods: OrderDeliveryMethod[];
    deliveryStatuses: OrderDeliveryStatus[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Admin', href: dashboard() },
            { title: 'Orders', href: index() },
            { title: 'Edit order', href: index() },
        ],
    },
});

const selectClass =
    'border-input h-9 w-full min-w-0 rounded-md border bg-transparent px-3 py-1 text-sm shadow-xs outline-none focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px]';

function toDateInputValue(value: string | null): string {
    return value ? value.slice(0, 10) : '';
}
</script>

<template>
    <Head title="Edit order" />

    <div class="max-w-2xl">
        <Heading title="Edit order" :description="order.customer_name" />

        <Form
            v-bind="OrderController.update.form(order.id)"
            class="space-y-6"
            v-slot="{ errors, processing }"
        >
            <div class="grid grid-cols-2 gap-4">
                <div class="grid gap-2">
                    <Label for="customer_name">Customer name</Label>
                    <Input
                        id="customer_name"
                        name="customer_name"
                        :default-value="order.customer_name"
                        required
                    />
                    <InputError :message="errors.customer_name" />
                </div>
                <div class="grid gap-2">
                    <Label for="customer_email">Customer email</Label>
                    <Input
                        id="customer_email"
                        name="customer_email"
                        type="email"
                        :default-value="order.customer_email ?? ''"
                    />
                    <InputError :message="errors.customer_email" />
                </div>
            </div>

            <div class="grid gap-2">
                <Label for="book_id">Book</Label>
                <select
                    id="book_id"
                    name="book_id"
                    :class="selectClass"
                    :value="order.book_id ?? ''"
                >
                    <option value="">No book set</option>
                    <option
                        v-for="book in books"
                        :key="book.id"
                        :value="book.id"
                    >
                        {{ book.title }}
                    </option>
                </select>
                <InputError :message="errors.book_id" />
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div class="grid gap-2">
                    <Label for="source">Source</Label>
                    <select
                        id="source"
                        name="source"
                        :class="selectClass"
                        :value="order.source"
                        required
                    >
                        <option
                            v-for="option in sources"
                            :key="option"
                            :value="option"
                        >
                            {{ option }}
                        </option>
                    </select>
                    <InputError :message="errors.source" />
                </div>
                <div class="grid gap-2">
                    <Label for="quantity">Quantity</Label>
                    <Input
                        id="quantity"
                        name="quantity"
                        type="number"
                        min="1"
                        :default-value="order.quantity"
                        required
                    />
                    <InputError :message="errors.quantity" />
                </div>
                <div class="grid gap-2">
                    <Label for="external_reference">Order ref</Label>
                    <Input
                        id="external_reference"
                        name="external_reference"
                        :default-value="order.external_reference ?? ''"
                    />
                    <InputError :message="errors.external_reference" />
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="grid gap-2">
                    <Label for="amount">Amount</Label>
                    <Input
                        id="amount"
                        name="amount"
                        type="number"
                        min="0"
                        step="0.01"
                        :default-value="
                            order.amount_cents !== null
                                ? (order.amount_cents / 100).toFixed(2)
                                : ''
                        "
                    />
                    <InputError :message="errors.amount" />
                </div>
                <div class="grid gap-2">
                    <Label for="currency">Currency</Label>
                    <Input
                        id="currency"
                        name="currency"
                        :default-value="order.currency"
                        required
                    />
                    <InputError :message="errors.currency" />
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div class="grid gap-2">
                    <Label for="status">Status</Label>
                    <select
                        id="status"
                        name="status"
                        :class="selectClass"
                        :value="order.status"
                        required
                    >
                        <option
                            v-for="option in statuses"
                            :key="option"
                            :value="option"
                        >
                            {{ option }}
                        </option>
                    </select>
                    <InputError :message="errors.status" />
                </div>
                <div class="grid gap-2">
                    <Label for="delivery_method">Delivery method</Label>
                    <select
                        id="delivery_method"
                        name="delivery_method"
                        :class="selectClass"
                        :value="order.delivery_method"
                        required
                    >
                        <option
                            v-for="option in deliveryMethods"
                            :key="option"
                            :value="option"
                        >
                            {{ option }}
                        </option>
                    </select>
                    <InputError :message="errors.delivery_method" />
                </div>
                <div class="grid gap-2">
                    <Label for="delivery_status">Delivery status</Label>
                    <select
                        id="delivery_status"
                        name="delivery_status"
                        :class="selectClass"
                        :value="order.delivery_status"
                        required
                    >
                        <option
                            v-for="option in deliveryStatuses"
                            :key="option"
                            :value="option"
                        >
                            {{ option }}
                        </option>
                    </select>
                    <InputError :message="errors.delivery_status" />
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="grid gap-2">
                    <Label for="tracking_number">Tracking number</Label>
                    <Input
                        id="tracking_number"
                        name="tracking_number"
                        :default-value="order.tracking_number ?? ''"
                    />
                    <InputError :message="errors.tracking_number" />
                </div>
                <div class="grid gap-2">
                    <Label for="ordered_at">Ordered at</Label>
                    <Input
                        id="ordered_at"
                        name="ordered_at"
                        type="date"
                        :default-value="toDateInputValue(order.ordered_at)"
                    />
                    <InputError :message="errors.ordered_at" />
                </div>
            </div>

            <div class="grid gap-2">
                <Label for="shipping_address">Shipping address</Label>
                <Textarea
                    id="shipping_address"
                    name="shipping_address"
                    :default-value="order.shipping_address ?? ''"
                />
                <InputError :message="errors.shipping_address" />
            </div>

            <div class="grid gap-2">
                <Label for="notes">Notes</Label>
                <Textarea
                    id="notes"
                    name="notes"
                    :default-value="order.notes ?? ''"
                />
                <InputError :message="errors.notes" />
            </div>

            <div class="flex items-center gap-4">
                <Button type="submit" :disabled="processing"
                    >Save changes</Button
                >
            </div>
        </Form>

        <Form
            v-bind="OrderController.destroy.form(props.order.id)"
            :options="{ preserveScroll: true }"
            class="mt-6"
        >
            <Button type="submit" variant="destructive">Delete order</Button>
        </Form>
    </div>
</template>
