<?php

namespace App\Models;

use App\Enums\PostCategory;
use App\Support\PostHtml;
use Database\Factories\PostFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

/**
 * @property int $id
 * @property int $author_id
 * @property PostCategory $category
 * @property string $title
 * @property string $slug
 * @property string|null $excerpt
 * @property string|null $cover_image_path
 * @property string $body
 * @property string|null $reviewed_book_title
 * @property string|null $reviewed_book_author
 * @property int|null $rating
 * @property array<int, array{label: string, url: string}>|null $affiliate_links
 * @property Carbon|null $published_at
 */
class Post extends Model
{
    /** @use HasFactory<PostFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $appends = ['cover_image_url'];

    protected $fillable = [
        'author_id',
        'category',
        'title',
        'slug',
        'excerpt',
        'cover_image_path',
        'body',
        'reviewed_book_title',
        'reviewed_book_author',
        'rating',
        'affiliate_links',
        'published_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'category' => PostCategory::class,
            'affiliate_links' => 'array',
            'rating' => 'integer',
            'published_at' => 'datetime',
        ];
    }

    /**
     * The excerpt, or the opening of the body when no excerpt was written.
     *
     * @return Attribute<string, never>
     */
    protected function summary(): Attribute
    {
        return Attribute::get(fn (): string => $this->excerpt ?: PostHtml::excerpt($this->body));
    }

    /**
     * @return Attribute<string|null, never>
     */
    protected function coverImageUrl(): Attribute
    {
        return Attribute::get(fn (): ?string => $this->cover_image_path
            ? Storage::disk('public')->url($this->cover_image_path)
            : null);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function isPublished(): bool
    {
        return $this->published_at !== null && $this->published_at->isPast();
    }

    /**
     * @param  Builder<Post>  $query
     * @return Builder<Post>
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->whereNotNull('published_at')->where('published_at', '<=', now());
    }

    /**
     * Published posts in a category, newest first, paginated for a public
     * listing page.
     *
     * @return LengthAwarePaginator<int, static>
     */
    public static function listing(PostCategory $category, int $perPage = 10): LengthAwarePaginator
    {
        $posts = static::query()
            ->published()
            ->inCategory($category)
            ->with('author:id,name,display_name')
            ->latest('published_at')
            ->paginate($perPage)
            ->withQueryString();

        $posts->getCollection()->each(function (self $post) {
            $post->append('summary')->makeHidden('body');
        });

        return $posts;
    }

    /**
     * @param  Builder<Post>  $query
     * @return Builder<Post>
     */
    public function scopeInCategory(Builder $query, PostCategory $category): Builder
    {
        return $query->where('category', $category->value);
    }
}
