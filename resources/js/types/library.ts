export type BookBlock = {
    type: 'heading' | 'subheading' | 'item' | 'paragraph';
    text: string;
    number?: number;
};

export type LibraryChapterSummary = {
    slug: string;
    number: number | null;
    title: string;
};

export type LibraryChapter = LibraryChapterSummary & {
    blocks: BookBlock[];
};

export type SampleChapterSummary = LibraryChapterSummary & {
    public: boolean;
};
