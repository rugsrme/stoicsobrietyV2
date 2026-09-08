export type BookBlock = {
    type: 'heading' | 'paragraph';
    text: string;
};

export type LibraryChapterSummary = {
    slug: string;
    number: number | null;
    title: string;
};

export type LibraryChapter = LibraryChapterSummary & {
    blocks: BookBlock[];
};
