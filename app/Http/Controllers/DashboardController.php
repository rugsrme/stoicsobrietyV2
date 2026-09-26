<?php

namespace App\Http\Controllers;

use App\Enums\PostCategory;
use App\Models\Post;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Dashboard', [
            'latestReflection' => $this->latest(PostCategory::Reflection),
            'latestReview' => $this->latest(PostCategory::BookReview),
        ]);
    }

    private function latest(PostCategory $category): ?Post
    {
        return Post::query()
            ->published()
            ->inCategory($category)
            ->with('author:id,name,display_name')
            ->latest('published_at')
            ->first()
            ?->append('summary')
            ->makeHidden('body');
    }
}
