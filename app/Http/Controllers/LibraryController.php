<?php

namespace App\Http\Controllers;

use App\Support\BookContent;
use Illuminate\Support\Arr;
use Inertia\Inertia;
use Inertia\Response;

class LibraryController extends Controller
{
    public function full(): Response
    {
        $chapters = BookContent::chapters()
            ->map(fn (array $chapter) => BookContent::load($chapter));

        return Inertia::render('library/Full', [
            'chapters' => $chapters,
        ]);
    }

    public function chapter(string $chapter): Response
    {
        $chapters = BookContent::chapters();
        $index = $chapters->search(fn (array $c) => $c['slug'] === $chapter);

        abort_if($index === false, 404);

        return Inertia::render('library/Chapter', [
            'chapter' => BookContent::load($chapters[$index]),
            'prev' => $index > 0
                ? Arr::only($chapters[$index - 1], ['slug', 'number', 'title'])
                : null,
            'next' => $index < $chapters->count() - 1
                ? Arr::only($chapters[$index + 1], ['slug', 'number', 'title'])
                : null,
            'chapters' => $chapters
                ->map(fn (array $c) => Arr::only($c, ['slug', 'number', 'title']))
                ->values(),
        ]);
    }
}
