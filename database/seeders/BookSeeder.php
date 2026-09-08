<?php

namespace Database\Seeders;

use App\Models\Book;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class BookSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $coverPath = $this->seedImage(
            resource_path('seed-images/covers/architecture-of-surrender-front.jpg'),
            'covers/architecture-of-surrender-front.jpg',
        );

        $backCoverPath = $this->seedImage(
            resource_path('seed-images/covers/architecture-of-surrender-back.jpg'),
            'covers/architecture-of-surrender-back.jpg',
        );

        $authorPhotoPath = $this->seedImage(
            resource_path('seed-images/authors/quinn-stewart.jpg'),
            'authors/quinn-stewart.jpg',
        );

        Book::query()->updateOrCreate(
            ['slug' => 'architecture-of-surrender'],
            [
                'title' => 'The Architecture of Surrender',
                'subtitle' => 'How a Rabbi, an Emperor, and a Roomful of Drunks Found the Same Way Out — three traditions, one way through.',
                'description' => implode("\n\n", [
                    'A rabbi teaching on a hillside. A Roman emperor writing to himself by candlelight. Two drunks talking in a hotel lobby. Two thousand years apart, no contact between them — and somehow, the same answer.',
                    'Written by a man who got sober at fifty after thirty years of drinking, this book traces one idea across three unlikely sources: the wisdom of the Twelve Steps, the honesty of Scripture, and the clarity of Stoic philosophy. It\'s not a memoir, and it\'s not a program. It\'s an argument — built quietly, chapter by chapter — that real peace was never about winning the fight for good. It was about finally understanding what was yours to fight in the first place.',
                    'For the person still white-knuckling their way through recovery, one day at a time as agony instead of philosophy, this book offers a way further in. For the family member trying to understand what\'s actually happening inside someone they love, it offers a window instead of a wall. And for the sponsor, the pastor, the counselor — anyone who\'s spent years helping others find the words for something wordless — it offers a shared language, drawn from traditions that already speak fluently to the people they serve.',
                    'No judgment. No finish line promised. Just the same door, found three different ways, standing open.',
                ]),
                'cover_path' => $coverPath,
                'back_cover_path' => $backCoverPath,
                'author_name' => 'Quinn Stewart',
                'author_photo_path' => $authorPhotoPath,
                'author_bio' => implode("\n\n", [
                    'I got sober on December 10, 2018. I was fifty years old, and it took me that long to finally get myself help.',
                    'Before that: over thirty years of drinking. The last few of them, morning until night, every single day. I was what people call a "functioning alcoholic," which is a polite way of saying I was good enough at hiding it, and smart enough — or thought I was smart enough — to keep telling myself it wasn\'t a problem yet. It was a problem the whole time.',
                    'I spent the next eight years in AA, plus time around NA — visiting, speaking to people going through it, showing up for Renewal Day about once a month when I could make it. Those are the rooms where I\'ve heard the stories, over and over. Somewhere in all of that, I started noticing something I couldn\'t unsee. A lot of people — good people, people trying hard — hit a plateau and stayed there. Sober, technically. Alive, technically. But it was "one day at a time" as agony, not as a philosophy — white-knuckling their way to the next meeting, keeping track of who they had to call before the craving won. They weren\'t drinking anymore, but the resentments, the fear, the old bonds to whatever hurt them in the first place — none of that had actually let go. It was just being managed instead of resolved.',
                    'Somewhere in that same stretch, I got curious about Stoicism — mostly because I realized I didn\'t actually know what the word meant. What I found instead was a two-thousand-year-old philosophy built almost entirely around the exact problem I\'d been circling in meetings and in church: what\'s actually yours to control, and what never was. My father is a devout born-again Christian, and my aunt studies the Bible and the Torah seriously — original Hebrew, original Greek. Between my father\'s church, my aunt\'s original-language study, the rooms, and the Stoics I\'d stumbled into almost by accident, I kept running into the same idea wearing three different outfits. That\'s the real origin of this book — not a decision to write something, but the slow accumulation of finding the same answer in three places that had no business agreeing with each other.',
                    'That\'s what this book is trying to fix. Not the drinking — the part underneath the drinking. I want people to stop needing the fail-safes: the emergency call, the meeting you drag yourself to because the alternative is worse. Those things save lives, mine included, and I\'m not telling anyone to walk away from what keeps them alive. I\'m saying there\'s a further place to get to, where you\'re not white-knuckling the dichotomy of control — where you actually know, in your gut and not just in a slogan, what\'s yours to work on and what never was. That\'s the only real serenity I\'ve found.',
                    'I wrote this from an alcoholic\'s seat, because that\'s the seat I sat in. But I don\'t think this is only about alcohol. Whatever you use to numb it, outrun it, or bury it — I hope this translates.',
                    'And here\'s the last thing, and it\'s the thing I most want you to believe: I am not the judge of you. I\'ve sat in the rooms and heard the same story come around again — someone back in treatment, someone starting over for the third or fifth or tenth time — and I know that feeling from both sides, telling it and hearing it. That story doesn\'t disqualify you from anything. I\'m here because I want to help you tell a different story — one that doesn\'t loop, one that actually goes somewhere. A happy one, if we\'re lucky. That\'s the whole reason this book exists.',
                ]),
                'excerpts' => [
                    [
                        'quote' => 'Sin, ignorance, self-will run riot. Three words, three traditions, and underneath all three the same shape: a demand that reality answer to you, instead of the slower work of answering to reality.',
                        'source' => 'Chapter 2 — Self-Will Run Riot',
                    ],
                    [
                        'quote' => 'Your judgments. Your intentions. Your effort. Your next choice. That\'s close to the whole list.',
                        'source' => 'Chapter 4 — The Dichotomy of Control',
                    ],
                    [
                        'quote' => '"That\'s not an inventory. That\'s a verdict. Go back and do the inventory."',
                        'source' => 'Chapter 5 — Putting Down the Gavel',
                    ],
                    [
                        'quote' => 'Half the actual battle in all of this isn\'t learning the words — it\'s living long enough inside them for one of them to finally land as real instead of recited.',
                        'source' => 'Chapter 12 — Life on Life\'s Terms',
                    ],
                    [
                        'quote' => 'I am not the judge of you. I\'m here because I want to help you tell a different story — one that doesn\'t loop, one that actually goes somewhere.',
                        'source' => 'About the Author',
                    ],
                ],
                // TODO(Quinn): swap these out for real reader/reviewer quotes as they come in.
                'testimonials' => [
                    [
                        'quote' => 'Replace me with a real reader quote — pick one line that names a specific moment the book landed for them, not a generic "great book."',
                        'author' => 'Placeholder Reviewer',
                        'context' => 'Swap me out',
                    ],
                    [
                        'quote' => 'A second placeholder so the carousel has something to rotate through — delete once you have two or more real quotes.',
                        'author' => 'Placeholder Reviewer',
                        'context' => 'Swap me out',
                    ],
                    [
                        'quote' => 'A third placeholder. Three to five short quotes is a good target for the carousel.',
                        'author' => 'Placeholder Reviewer',
                        'context' => 'Swap me out',
                    ],
                ],
                'retailer_links' => [
                    'amazon' => 'https://www.amazon.com/',
                    'barnes_noble' => 'https://www.barnesandnoble.com/',
                    'bookshop' => 'https://bookshop.org/',
                    'apple_books' => 'https://books.apple.com/',
                ],
                'price' => 1899,
                'currency' => 'USD',
                'purchase_type' => 'link',
                'is_featured' => true,
                'published_at' => now(),
            ],
        );
    }

    /**
     * Copy a repo-committed seed image into the public storage disk, once.
     * Safe to call on every seeder run — it's a no-op if the file already exists.
     */
    private function seedImage(string $sourceAbsolutePath, string $destinationRelativePath): ?string
    {
        if (! File::exists($sourceAbsolutePath)) {
            return null;
        }

        $disk = Storage::disk('public');

        if (! $disk->exists($destinationRelativePath)) {
            $disk->put($destinationRelativePath, File::get($sourceAbsolutePath));
        }

        return $destinationRelativePath;
    }
}
