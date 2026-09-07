<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Inertia\Inertia;
use Inertia\Response;

class BlogController extends Controller
{
    public function index(): Response
    {
        $posts = Post::query()
            ->published()
            ->with('author:id,name,display_name')
            ->latest('published_at')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('blog/Index', [
            'posts' => $posts,
        ]);
    }

    public function show(Post $post): Response
    {
        abort_unless($post->isPublished(), 404);

        $post->load('author:id,name,display_name');

        return Inertia::render('blog/Show', [
            'post' => $post,
        ]);
    }
}
