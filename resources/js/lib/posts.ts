import { index as journalIndex, show as journalShow } from '@/routes/journal';
import {
    index as reflectionsIndex,
    show as reflectionsShow,
} from '@/routes/reflections';
import { index as reviewsIndex, show as reviewsShow } from '@/routes/reviews';
import type { PostCategory } from '@/types';

/** The page listing a category's posts. Journal is admin-only. */
export function postIndexUrl(category: PostCategory) {
    switch (category) {
        case 'book-review':
            return reviewsIndex();
        case 'journal':
            return journalIndex();
        default:
            return reflectionsIndex();
    }
}

/** The page showing a single post, in the section its category belongs to. */
export function postUrl(post: { category: PostCategory; slug: string }) {
    switch (post.category) {
        case 'book-review':
            return reviewsShow(post.slug);
        case 'journal':
            return journalShow(post.slug);
        default:
            return reflectionsShow(post.slug);
    }
}

/** What one post in a category is called, e.g. "New journal entry". */
export function postNoun(category: PostCategory): string {
    switch (category) {
        case 'book-review':
            return 'book review';
        case 'journal':
            return 'journal entry';
        default:
            return 'reflection';
    }
}
