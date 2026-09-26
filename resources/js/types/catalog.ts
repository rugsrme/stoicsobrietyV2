export type BookExcerpt = {
    quote: string;
    source: string;
};

export type BookTestimonial = {
    quote: string;
    author: string;
    context?: string;
};

export type Book = {
    id: number;
    title: string;
    slug: string;
    subtitle: string | null;
    description: string | null;
    cover_url: string | null;
    back_cover_url: string | null;
    sample_path: string | null;
    author_name: string | null;
    author_bio: string | null;
    author_photo_url: string | null;
    excerpts: BookExcerpt[] | null;
    testimonials: BookTestimonial[] | null;
    retailer_links: Record<string, string> | null;
    price: number | null;
    price_formatted: string | null;
    currency: string;
    purchase_type: 'link' | 'stripe';
    is_featured: boolean;
    published_at: string | null;
};

export type PostCategory = 'reflection' | 'book-review' | 'journal';

export type PostCategoryOption = {
    value: PostCategory;
    label: string;
    plural: string;
};

export type AffiliateLink = {
    label: string;
    url: string;
};

export type PostSummary = {
    id: number;
    category: PostCategory;
    title: string;
    slug: string;
    excerpt: string | null;
    /** The excerpt, or the opening of the body when none was written. */
    summary?: string;
    cover_image_url: string | null;
    reviewed_book_title: string | null;
    reviewed_book_author: string | null;
    rating: number | null;
    published_at: string | null;
    author?: PostAuthor;
};

export type PostAuthor = {
    id: number;
    name: string;
    display_name: string | null;
};

export type Post = PostSummary & {
    body: string;
    affiliate_links: AffiliateLink[] | null;
    author: PostAuthor;
};
