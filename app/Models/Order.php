<?php

namespace App\Models;

use Database\Factories\OrderFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int|null $book_id
 * @property int|null $user_id
 * @property string $customer_name
 * @property string|null $customer_email
 * @property string $source
 * @property string|null $external_reference
 * @property int $quantity
 * @property int|null $amount_cents
 * @property string $currency
 * @property string $status
 * @property string $delivery_method
 * @property string $delivery_status
 * @property string|null $tracking_number
 * @property string|null $shipping_address
 * @property string|null $notes
 * @property Carbon|null $ordered_at
 * @property Carbon|null $fulfilled_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Order extends Model
{
    /** @use HasFactory<OrderFactory> */
    use HasFactory;

    public const STATUSES = ['pending', 'paid', 'fulfilled', 'cancelled', 'refunded'];

    public const DELIVERY_METHODS = ['external', 'digital', 'physical'];

    public const DELIVERY_STATUSES = ['not_required', 'pending', 'in_progress', 'delivered'];

    public const SOURCES = ['manual', 'amazon', 'barnes-noble', 'other-retailer', 'direct'];

    /**
     * @var array<int, string>
     */
    protected $appends = ['amount_formatted'];

    protected $fillable = [
        'book_id',
        'user_id',
        'customer_name',
        'customer_email',
        'source',
        'external_reference',
        'quantity',
        'amount_cents',
        'currency',
        'status',
        'delivery_method',
        'delivery_status',
        'tracking_number',
        'shipping_address',
        'notes',
        'ordered_at',
        'fulfilled_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'amount_cents' => 'integer',
            'ordered_at' => 'datetime',
            'fulfilled_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Book, $this>
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return Attribute<string|null, never>
     */
    protected function amountFormatted(): Attribute
    {
        return Attribute::get(fn (): ?string => $this->amount_cents === null
            ? null
            : number_format($this->amount_cents / 100, 2));
    }
}
