<?php

namespace App\Http\Requests\Admin;

use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrderRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'book_id' => ['nullable', 'integer', 'exists:books,id'],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['nullable', 'email', 'max:255'],
            'source' => ['required', 'string', Rule::in(Order::SOURCES)],
            'external_reference' => ['nullable', 'string', 'max:255'],
            'quantity' => ['required', 'integer', 'min:1'],
            'amount' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'size:3'],
            'status' => ['required', 'string', Rule::in(Order::STATUSES)],
            'delivery_method' => ['required', 'string', Rule::in(Order::DELIVERY_METHODS)],
            'delivery_status' => ['required', 'string', Rule::in(Order::DELIVERY_STATUSES)],
            'tracking_number' => ['nullable', 'string', 'max:255'],
            'shipping_address' => ['nullable', 'string', 'max:1000'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'ordered_at' => ['nullable', 'date'],
            'fulfilled_at' => ['nullable', 'date'],
        ];
    }
}
