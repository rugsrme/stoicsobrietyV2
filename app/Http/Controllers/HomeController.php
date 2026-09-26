<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Post;
use App\Models\ReaderReview;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Welcome', [
            'book' => Book::current(),
            'posts' => Post::forHomePage(),
            'readerReviews' => ReaderReview::query()->forCarousel()->get(['id', 'quote', 'author', 'context']),
        ]);
    }
}
