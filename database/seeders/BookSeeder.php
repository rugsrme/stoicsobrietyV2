<?php

namespace Database\Seeders;

use App\Models\Book;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Book::query()->updateOrCreate(
            ['slug' => 'architecture-of-surrender'],
            [
                'title' => 'The Architecture of Surrender',
                'subtitle' => 'How a Rabbi, an Emperor, and a Roomful of Drunks Found the Same Way Out — three traditions, one way through.',
                'author_name' => 'Quinn Stewart',
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
}
