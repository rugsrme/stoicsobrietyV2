<?php

namespace App\Http\Controllers;

use App\Models\Book;
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

        return Inertia::render('Welcome', [
            'book' => $book,
        ]);
    }
}
