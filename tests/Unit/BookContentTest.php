<?php

use App\Support\BookContent;

test('chapter text is split into headings, subheadings, numbered steps and paragraphs', function () {
    $blocks = BookContent::blocks(<<<'MD'
        Opening *italic* line
        continues here.

        ## A Heading

        ### A Subheading

        12. A numbered step.
        MD);

    expect($blocks)->toBe([
        ['type' => 'paragraph', 'text' => 'Opening *italic* line continues here.'],
        ['type' => 'heading', 'text' => 'A Heading'],
        ['type' => 'subheading', 'text' => 'A Subheading'],
        ['type' => 'item', 'number' => 12, 'text' => 'A numbered step.'],
    ]);
});

test('every configured chapter file exists', function () {
    foreach (require __DIR__.'/../../config/book.php' as $key => $value) {
        if ($key !== 'chapters') {
            continue;
        }

        foreach ($value as $chapter) {
            expect(file_exists(__DIR__.'/../../resources/book/chapters/'.$chapter['file']))->toBeTrue();
        }
    }
});
