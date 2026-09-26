<?php

namespace App\Http\Controllers;

use App\Enums\PostCategory;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

/**
 * A listing + single-post section of the site for one post category
 * (Reflections, Book Reviews, Journal).
 */
abstract class PostSectionController extends Controller
{
    abstract protected function category(): PostCategory;

    /**
     * The Inertia page directory that renders this section.
     */
    protected function pages(): string
    {
        return 'posts';
    }

    public function index(): Response
    {
        return Inertia::render($this->pages().'/Index', [
            'section' => $this->category()->value,
            'posts' => Post::listing($this->category()),
        ]);
    }

    public function show(Post $post): Response|RedirectResponse
    {
        abort_unless($post->isPublished(), 404);

        if ($post->category !== $this->category()) {
            // Send readers to the right section, but never reveal a private
            // journal entry through a public address.
            abort_unless($post->category->isPublic(), 404);

            return redirect()->route($post->category->showRoute(), $post, 301);
        }

        $post->load('author:id,name,display_name');

        return Inertia::render($this->pages().'/Show', [
            'section' => $this->category()->value,
            'post' => $post,
        ]);
    }
}
