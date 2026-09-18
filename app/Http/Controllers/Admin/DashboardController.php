<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('admin/Dashboard', [
            'stats' => [
                'total_users' => User::query()->count(),
                'total_admins' => User::query()->where('is_admin', true)->count(),
                'new_users_last_30_days' => User::query()->where('created_at', '>=', now()->subDays(30))->count(),
                'total_orders' => Order::query()->count(),
                'orders_needing_fulfillment' => Order::query()
                    ->whereIn('status', ['pending', 'paid'])
                    ->whereIn('delivery_status', ['pending', 'in_progress'])
                    ->count(),
            ],
            'recentUsers' => User::query()
                ->latest('created_at')
                ->take(5)
                ->get(['id', 'name', 'display_name', 'email', 'created_at']),
            'recentOrders' => Order::query()
                ->with('book:id,title')
                ->latest('created_at')
                ->take(5)
                ->get(['id', 'book_id', 'customer_name', 'status', 'delivery_status', 'amount_cents', 'currency', 'created_at']),
        ]);
    }
}
