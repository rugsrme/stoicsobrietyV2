<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Support\BookContent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Inertia\Inertia;
use Inertia\Response;

/**
 * The free, public sample of the book: the opening pages and the first
 * few chapters (config('book.public_chapters')). Everything else lives in
 * the subscribers' library.
 */
class SampleController extends Controller
{
    public function index(): RedirectResponse
    {
        return to_route('sample.chapter', Config::array('book.public_chapters')[0]);
    }

    public function chapter(string $chapter): Response|RedirectResponse
    {
        $chapters = BookContent::chapters();
        $index = $chapters->search(fn (array $c) => $c['slug'] === $chapter);

        abort_if($index === false, 404);

        if (! BookContent::isPublic($chapter)) {
            return to_route('library.chapter', $chapter);
        }

        $summary = fn (array $c) => [
            ...Arr::only($c, ['slug', 'number', 'title']),
            'public' => BookContent::isPublic($c['slug']),
        ];

        $next = $chapters->get($index + 1);

        return Inertia::render('sample/Chapter', [
            'chapter' => BookContent::load($chapters[$index]),
            'prev' => $index > 0 ? $summary($chapters[$index - 1]) : null,
            'next' => $next ? $summary($next) : null,
            'chapters' => $chapters->map($summary)->values(),
            'book' => Book::query()
                ->whereNotNull('published_at')
                ->orderByDesc('is_featured')
                ->first(['id', 'title', 'slug', 'retailer_links']),
        ]);
    }
}
