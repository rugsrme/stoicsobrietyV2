<?php

namespace App\Http\Controllers;

use App\Enums\PostCategory;

class ReviewController extends PostSectionController
{
    protected function category(): PostCategory
    {
        return PostCategory::BookReview;
    }

    protected function pages(): string
    {
        return 'reviews';
    }
}
