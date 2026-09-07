<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Post;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function index(): Response
    {
        $book = Book::query()
            ->whereNotNull('published_at')
            ->orderByDesc('is_featured')
            ->orderBy('published_at')
            ->first();

        $recentPosts = Post::query()
            ->published()
            ->latest('published_at')
            ->take(3)
            ->get(['id', 'title', 'slug', 'excerpt', 'published_at']);

        return Inertia::render('Welcome', [
            'book' => $book,
            'recentPosts' => $recentPosts,
        ]);
    }
}
