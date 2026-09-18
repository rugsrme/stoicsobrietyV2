export type OrderStatus =
    | 'pending'
    | 'paid'
    | 'fulfilled'
    | 'cancelled'
    | 'refunded';

export type OrderDeliveryMethod = 'external' | 'digital' | 'physical';

export type OrderDeliveryStatus =
    | 'not_required'
    | 'pending'
    | 'in_progress'
    | 'delivered';

export type OrderSource =
    | 'manual'
    | 'amazon'
    | 'barnes-noble'
    | 'other-retailer'
    | 'direct';

export type OrderBook = {
    id: number;
    title: string;
};

export type Order = {
    id: number;
    book_id: number | null;
    book?: OrderBook | null;
    user_id: number | null;
    customer_name: string;
    customer_email: string | null;
    source: OrderSource;
    external_reference: string | null;
    quantity: number;
    amount_cents: number | null;
    amount_formatted: string | null;
    currency: string;
    status: OrderStatus;
    delivery_method: OrderDeliveryMethod;
    delivery_status: OrderDeliveryStatus;
    tracking_number: string | null;
    shipping_address: string | null;
    notes: string | null;
    ordered_at: string | null;
    fulfilled_at: string | null;
    created_at: string;
    updated_at: string;
};

export type AdminUserSummary = {
    id: number;
    name: string;
    display_name: string | null;
    email: string;
    is_admin: boolean;
    created_at: string;
};

export type AdminStats = {
    total_users: number;
    total_admins: number;
    new_users_last_30_days: number;
    total_orders: number;
    orders_needing_fulfillment: number;
};
