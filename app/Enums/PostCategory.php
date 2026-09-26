<?php

namespace App\Enums;

enum PostCategory: string
{
    case Reflection = 'reflection';
    case BookReview = 'book-review';
    case Journal = 'journal';

    public function label(): string
    {
        return match ($this) {
            self::Reflection => 'Reflection',
            self::BookReview => 'Book Review',
            self::Journal => 'Journal',
        };
    }

    public function pluralLabel(): string
    {
        return match ($this) {
            self::Reflection => 'Reflections',
            self::BookReview => 'Book Reviews',
            self::Journal => 'Journal',
        };
    }

    /**
     * Whether posts in this category appear on the public site. Journal
     * entries are private to admins.
     */
    public function isPublic(): bool
    {
        return $this !== self::Journal;
    }

    /**
     * The named route that shows a single post in this category.
     */
    public function showRoute(): string
    {
        return match ($this) {
            self::Reflection => 'reflections.show',
            self::BookReview => 'reviews.show',
            self::Journal => 'journal.show',
        };
    }

    /**
     * @return array<int, array{value: string, label: string, plural: string}>
     */
    public static function options(): array
    {
        return array_map(
            fn (self $category) => [
                'value' => $category->value,
                'label' => $category->label(),
                'plural' => $category->pluralLabel(),
            ],
            self::cases(),
        );
    }
}
