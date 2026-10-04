<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A slug a post used to have. Links already shared to Facebook and elsewhere
 * point at the old address, so it redirects to the post's current one.
 *
 * @property int $id
 * @property int $post_id
 * @property string $slug
 */
class PostSlugRedirect extends Model
{
    protected $fillable = ['post_id', 'slug'];

    /**
     * @return BelongsTo<Post, $this>
     */
    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
