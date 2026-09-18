<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreOrderRequest;
use App\Http\Requests\Admin\UpdateOrderRequest;
use App\Models\Book;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OrderController extends Controller
{
    public function index(Request $request): Response
    {
        $status = $request->query('status');

        $orders = Order::query()
            ->with('book:id,title')
            ->when($status, fn ($query) => $query->where('status', $status))
            ->latest('ordered_at')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('admin/orders/Index', [
            'orders' => $orders,
            'status' => $status,
            'statuses' => Order::STATUSES,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/orders/Create', [
            'books' => Book::query()->orderBy('title')->get(['id', 'title']),
            'sources' => Order::SOURCES,
            'statuses' => Order::STATUSES,
            'deliveryMethods' => Order::DELIVERY_METHODS,
            'deliveryStatuses' => Order::DELIVERY_STATUSES,
        ]);
    }

    public function store(StoreOrderRequest $request): RedirectResponse
    {
        $order = Order::create($this->mapData($request->validated()));

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Order logged.')]);

        return to_route('admin.orders.edit', $order);
    }

    public function edit(Order $order): Response
    {
        return Inertia::render('admin/orders/Edit', [
            'order' => $order,
            'books' => Book::query()->orderBy('title')->get(['id', 'title']),
            'sources' => Order::SOURCES,
            'statuses' => Order::STATUSES,
            'deliveryMethods' => Order::DELIVERY_METHODS,
            'deliveryStatuses' => Order::DELIVERY_STATUSES,
        ]);
    }

    public function update(UpdateOrderRequest $request, Order $order): RedirectResponse
    {
        $order->update($this->mapData($request->validated()));

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Order updated.')]);

        return to_route('admin.orders.edit', $order);
    }

    public function destroy(Order $order): RedirectResponse
    {
        $order->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Order deleted.')]);

        return to_route('admin.orders.index');
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function mapData(array $data): array
    {
        $amount = $data['amount'] ?? null;
        unset($data['amount']);

        $data['amount_cents'] = $amount === null || $amount === '' ? null : (int) round(((float) $amount) * 100);

        if (($data['status'] ?? null) === 'fulfilled' && ($data['fulfilled_at'] ?? null) === null) {
            $data['fulfilled_at'] = now();
        }

        return $data;
    }
}
