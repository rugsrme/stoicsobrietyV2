<?php

namespace App\Models;

use Database\Factories\ReaderReviewFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * A short quote from a reader about the book, shown in the home page carousel.
 *
 * @property int $id
 * @property string $quote
 * @property string $author
 * @property string|null $context
 * @property bool $is_published
 * @property bool $is_featured
 * @property int $sort_order
 */
class ReaderReview extends Model
{
    /** @use HasFactory<ReaderReviewFactory> */
    use HasFactory;

    protected $fillable = [
        'quote',
        'author',
        'context',
        'is_published',
        'is_featured',
        'sort_order',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * Published reviews ticked "Show in home carousel", in carousel order.
     *
     * @param  Builder<ReaderReview>  $query
     * @return Builder<ReaderReview>
     */
    public function scopeForCarousel(Builder $query): Builder
    {
        return $query->where('is_published', true)
            ->where('is_featured', true)
            ->orderBy('sort_order')
            ->orderBy('id');
    }
}
