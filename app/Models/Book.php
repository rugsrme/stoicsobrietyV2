<?php

namespace App\Models;

use Database\Factories\BookFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

/**
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string|null $subtitle
 * @property string|null $description
 * @property string|null $cover_path
 * @property string|null $sample_path
 * @property string|null $author_name
 * @property string|null $author_bio
 * @property string|null $author_photo_path
 * @property array<int, string>|null $excerpts
 * @property array<string, string>|null $retailer_links
 * @property int|null $price
 * @property string $currency
 * @property string $purchase_type
 * @property string|null $stripe_price_id
 * @property bool $is_featured
 * @property Carbon|null $published_at
 */
class Book extends Model
{
    /** @use HasFactory<BookFactory> */
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $appends = ['cover_url', 'author_photo_url', 'price_formatted'];

    protected $fillable = [
        'title',
        'slug',
        'subtitle',
        'description',
        'cover_path',
        'sample_path',
        'author_name',
        'author_bio',
        'author_photo_path',
        'excerpts',
        'retailer_links',
        'price',
        'currency',
        'purchase_type',
        'stripe_price_id',
        'is_featured',
        'published_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'excerpts' => 'array',
            'retailer_links' => 'array',
            'is_featured' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    /**
     * @return Attribute<string|null, never>
     */
    protected function coverUrl(): Attribute
    {
        return Attribute::get(fn (): ?string => $this->cover_path
            ? Storage::disk('public')->url($this->cover_path)
            : null);
    }

    /**
     * @return Attribute<string|null, never>
     */
    protected function authorPhotoUrl(): Attribute
    {
        return Attribute::get(fn (): ?string => $this->author_photo_path
            ? Storage::disk('public')->url($this->author_photo_path)
            : null);
    }

    /**
     * @return Attribute<string|null, never>
     */
    protected function priceFormatted(): Attribute
    {
        return Attribute::get(fn (): ?string => $this->price === null
            ? null
            : number_format($this->price / 100, 2));
    }
}
