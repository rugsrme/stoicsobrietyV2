<?php

return [
    /*
    |--------------------------------------------------------------------------
    | The Architecture of Surrender — reader content
    |--------------------------------------------------------------------------
    |
    | Ordered list of the book's chapters. Each "file" lives under
    | resources/book/chapters/ and is parsed into paragraph/heading blocks
    | by App\Support\BookContent.
    |
    */
    'chapters' => [
        [
            'slug' => 'front-matter',
            'number' => null,
            'title' => 'About This Book',
            'file' => '00-front-matter.md',
        ],
        [
            'slug' => 'chapter-1',
            'number' => 1,
            'title' => "You're Not Fighting Anything",
            'file' => '01-not-fighting-anything.md',
        ],
        [
            'slug' => 'chapter-2',
            'number' => 2,
            'title' => 'Self-Will Run Riot',
            'file' => '02-self-will-run-riot.md',
        ],
        [
            'slug' => 'chapter-3',
            'number' => 3,
            'title' => 'The Illusion of Control',
            'file' => '03-illusion-of-control.md',
        ],
        [
            'slug' => 'chapter-4',
            'number' => 4,
            'title' => 'The Dichotomy of Control',
            'file' => '04-dichotomy-of-control.md',
        ],
        [
            'slug' => 'chapter-5',
            'number' => 5,
            'title' => 'Putting Down the Gavel',
            'file' => '05-putting-down-the-gavel.md',
        ],
        [
            'slug' => 'chapter-6',
            'number' => 6,
            'title' => 'The Debt You Keep Paying',
            'file' => '06-debt-you-keep-paying.md',
        ],
        [
            'slug' => 'chapter-7',
            'number' => 7,
            'title' => 'The Honest Inventory',
            'file' => '07-honest-inventory.md',
        ],
        [
            'slug' => 'chapter-8',
            'number' => 8,
            'title' => "Carried by Something You Didn't Create",
            'file' => '08-carried-by-something-you-didnt-create.md',
        ],
        [
            'slug' => 'chapter-9',
            'number' => 9,
            'title' => 'Where Two or Three Are Gathered',
            'file' => '09-where-two-or-three-are-gathered.md',
        ],
        [
            'slug' => 'chapter-10',
            'number' => 10,
            'title' => 'The Daily Architecture',
            'file' => '10-daily-architecture.md',
        ],
        [
            'slug' => 'chapter-11',
            'number' => 11,
            'title' => "When It Doesn't Hold",
            'file' => '11-when-it-doesnt-hold.md',
        ],
        [
            'slug' => 'chapter-12',
            'number' => 12,
            'title' => "Life on Life's Terms",
            'file' => '12-life-on-lifes-terms.md',
        ],
    ],
];
