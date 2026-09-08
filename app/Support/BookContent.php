<?php

namespace App\Support;

use Illuminate\Support\Facades\File;

class BookContent
{
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
     * Split raw chapter text into paragraph and heading blocks.
     *
     * @return array<int, array{type: string, text: string}>
     */
    public static function blocks(string $raw): array
    {
        $paragraphs = preg_split('/\n\s*\n/', trim($raw)) ?: [];

        return collect($paragraphs)
            ->map(function (string $paragraph) {
                $paragraph = trim($paragraph);

                if (str_starts_with($paragraph, '## ')) {
                    return [
                        'type' => 'heading',
                        'text' => trim(substr($paragraph, 3)),
                    ];
                }

                return [
                    'type' => 'paragraph',
                    'text' => trim(preg_replace('/\s+/', ' ', $paragraph)),
                ];
            })
            ->values()
            ->all();
    }
}
