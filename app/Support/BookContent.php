<?php

namespace App\Support;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;

class BookContent
{
    /**
     * The book's chapters, in reading order, from config('book.chapters').
     *
     * @return Collection<int, array{slug: string, number: int|null, title: string, file: string}>
     */
    public static function chapters(): Collection
    {
        return collect(Config::array('book.chapters'))
            ->map(fn (array $c) => [
                'slug' => (string) $c['slug'],
                'number' => isset($c['number']) ? (int) $c['number'] : null,
                'title' => (string) $c['title'],
                'file' => (string) $c['file'],
            ])
            ->values();
    }

    /**
     * Load a chapter definition (from config('book.chapters')) and parse
     * its source file into renderable blocks.
     *
     * @param  array{slug: string, number: int|null, title: string, file: string}  $chapter
     * @return array{slug: string, number: int|null, title: string, blocks: array<int, array{type: string, text: string}>}
     */
    public static function load(array $chapter): array
    {
        $path = resource_path('book/chapters/'.$chapter['file']);

        return [
            'slug' => $chapter['slug'],
            'number' => $chapter['number'],
            'title' => $chapter['title'],
            'blocks' => static::blocks(File::get($path)),
        ];
    }

    /**
     * Whether a chapter (by slug) is part of the free public sample.
     */
    public static function isPublic(string $slug): bool
    {
        return in_array($slug, Config::array('book.public_chapters', []), true);
    }

    /**
     * Split raw chapter text into heading, subheading, numbered-item and
     * paragraph blocks. Inline *emphasis* is kept in the text for the
     * front end to render.
     *
     * @return array<int, array{type: string, text: string, number?: int}>
     */
    public static function blocks(string $raw): array
    {
        $paragraphs = preg_split('/\n\s*\n/', trim($raw)) ?: [];

        return collect($paragraphs)
            ->map(function (string $paragraph) {
                $paragraph = trim(preg_replace('/\s+/', ' ', $paragraph) ?? '');

                if (str_starts_with($paragraph, '### ')) {
                    return ['type' => 'subheading', 'text' => trim(substr($paragraph, 4))];
                }

                if (str_starts_with($paragraph, '## ')) {
                    return ['type' => 'heading', 'text' => trim(substr($paragraph, 3))];
                }

                if (preg_match('/^(\d+)\.\s+(.*)$/', $paragraph, $matches)) {
                    return ['type' => 'item', 'number' => (int) $matches[1], 'text' => $matches[2]];
                }

                return ['type' => 'paragraph', 'text' => $paragraph];
            })
            ->values()
            ->all();
    }
}
