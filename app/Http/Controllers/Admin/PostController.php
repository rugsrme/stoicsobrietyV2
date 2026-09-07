<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePostRequest;
use App\Http\Requests\Admin\UpdatePostRequest;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class PostController extends Controller
{
    public function index(): Response
    {
        $posts = Post::query()
            ->latest('updated_at')
            ->get(['id', 'title', 'slug', 'published_at', 'updated_at']);

        return Inertia::render('admin/posts/Index', [
            'posts' => $posts,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/posts/Create');
    }

    public function store(StorePostRequest $request): RedirectResponse
    {
        $data = $request->validated();

        Post::create([
            'author_id' => $request->user()->id,
            'title' => $data['title'],
            'slug' => $data['slug'],
            'excerpt' => $data['excerpt'] ?? null,
            'body' => $data['body'],
            'published_at' => ($data['published'] ?? false) ? now() : null,
        ]);

        return to_route('admin.posts.index');
    }

    public function edit(Post $post): Response
    {
        return Inertia::render('admin/posts/Edit', [
            'post' => $post,
        ]);
    }

    public function update(UpdatePostRequest $request, Post $post): RedirectResponse
    {
        $data = $request->validated();

        $published = $data['published'] ?? false;

        $post->update([
            'title' => $data['title'],
            'slug' => $data['slug'],
            'excerpt' => $data['excerpt'] ?? null,
            'body' => $data['body'],
            'published_at' => $published ? ($post->published_at ?? now()) : null,
        ]);

        return to_route('admin.posts.index');
    }

    public function destroy(Post $post): RedirectResponse
    {
        $post->delete();

        return to_route('admin.posts.index');
    }
}
