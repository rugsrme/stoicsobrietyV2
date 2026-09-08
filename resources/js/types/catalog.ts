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

export type PostSummary = {
    id: number;
    title: string;
    slug: string;
    excerpt: string | null;
    published_at: string | null;
};

export type PostAuthor = {
    id: number;
    name: string;
    display_name: string | null;
};

export type Post = PostSummary & {
    body: string;
    author: PostAuthor;
};
