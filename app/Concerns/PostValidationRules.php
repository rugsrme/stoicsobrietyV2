<?php

namespace App\Concerns;

use App\Enums\PostCategory;
use App\Models\Post;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;

trait PostValidationRules
{
    /**
     * Get the validation rules used to validate reflections, book reviews and journal entries.
     *
     * @return array<string, array<int, ValidationRule|array<mixed>|string>>
     */
    protected function postRules(?Post $post = null): array
    {
        $isReview = 'required_if:category,'.PostCategory::BookReview->value;

        return [
            'category' => ['required', Rule::enum(PostCategory::class)],
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                'alpha_dash',
                $post === null
                    ? Rule::unique('posts', 'slug')
                    : Rule::unique('posts', 'slug')->ignore($post),
            ],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'body' => ['required', 'string', 'max:1000000'],
            'cover_image' => ['nullable', 'image', 'max:5120'],
            'remove_cover_image' => ['boolean'],
            'reviewed_book_title' => ['nullable', $isReview, 'string', 'max:255'],
            'reviewed_book_author' => ['nullable', 'string', 'max:255'],
            'rating' => ['nullable', 'integer', 'between:1,5'],
            'affiliate_links' => ['nullable', 'array', 'max:10'],
            'affiliate_links.*.label' => ['required', 'string', 'max:60'],
            'affiliate_links.*.url' => ['required', 'url:http,https', 'max:2048'],
            'published' => ['boolean'],
            'is_featured' => ['boolean'],
        ];
    }
}
