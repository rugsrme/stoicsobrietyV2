<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class BookController extends Controller
{
    public function index(): Response
    {
        $books = Book::query()
            ->whereNotNull('published_at')
            ->orderByDesc('is_featured')
            ->orderBy('published_at')
            ->get();

        return Inertia::render('books/Index', [
            'books' => $books,
        ]);
    }

    public function show(Book $book): Response
    {
        abort_unless($book->published_at !== null, 404);

        return Inertia::render('books/Show', [
            'book' => $book,
        ]);
    }

    public function sample(Book $book): HttpResponse
    {
        abort_unless($book->sample_path !== null, 404);

        return Storage::disk('public')->download($book->sample_path);
    }
}
