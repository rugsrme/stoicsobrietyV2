<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PostCategory;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePostRequest;
use App\Http\Requests\Admin\UpdatePostRequest;
use App\Models\Post;
use App\Support\PostHtml;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class PostController extends Controller
{
    public function index(Request $request): Response
    {
        $category = PostCategory::tryFrom((string) $request->query('category'));

        $posts = Post::query()
            ->when($category, fn ($query) => $query->inCategory($category))
            ->latest('updated_at')
            ->get(['id', 'category', 'title', 'slug', 'cover_image_path', 'published_at', 'updated_at']);

        return Inertia::render('admin/posts/Index', [
            'posts' => $posts,
            'category' => $category?->value,
            'categories' => PostCategory::options(),
        ]);
    }

    public function create(Request $request): Response
    {
        return Inertia::render('admin/posts/Create', [
            'categories' => PostCategory::options(),
            'category' => (PostCategory::tryFrom((string) $request->query('category')) ?? PostCategory::Reflection)->value,
        ]);
    }

    public function store(StorePostRequest $request): RedirectResponse
    {
        $post = new Post(['author_id' => $request->user()->id]);

        $this->fill($post, $request);
        $post->fill(['published_at' => $request->boolean('published') ? now() : null]);
        $post->save();

        return to_route('admin.posts.index', ['category' => $post->category->value]);
    }

    public function edit(Post $post): Response
    {
        return Inertia::render('admin/posts/Edit', [
            'post' => $post,
            'categories' => PostCategory::options(),
        ]);
    }

    public function update(UpdatePostRequest $request, Post $post): RedirectResponse
    {
        $this->fill($post, $request);
        $post->fill(['published_at' => $request->boolean('published') ? ($post->published_at ?? now()) : null]);
        $post->save();

        return to_route('admin.posts.index', ['category' => $post->category->value]);
    }

    public function destroy(Post $post): RedirectResponse
    {
        if ($post->cover_image_path) {
            Storage::disk('public')->delete($post->cover_image_path);
        }

        $post->delete();

        return to_route('admin.posts.index');
    }

    /**
     * Store an image dropped or pasted into the editor and return its URL.
     */
    public function uploadImage(Request $request): JsonResponse
    {
        $request->validate([
            'image' => ['required', 'image', 'max:5120'],
        ]);

        $path = $this->storeImage($request->file('image'), 'posts/content');

        return response()->json([
            'url' => Storage::disk('public')->url($path),
        ]);
    }

    private function fill(Post $post, StorePostRequest|UpdatePostRequest $request): void
    {
        $data = $request->validated();
        $category = PostCategory::from($data['category']);
        $isReview = $category === PostCategory::BookReview;

        $post->fill([
            'category' => $category,
            'title' => $data['title'],
            'slug' => $data['slug'],
            'excerpt' => $data['excerpt'] ?? null,
            'body' => PostHtml::localizeImages(PostHtml::sanitize($data['body'])),
            'reviewed_book_title' => $isReview ? ($data['reviewed_book_title'] ?? null) : null,
            'reviewed_book_author' => $isReview ? ($data['reviewed_book_author'] ?? null) : null,
            'rating' => $isReview ? ($data['rating'] ?? null) : null,
            'affiliate_links' => $isReview ? array_values($data['affiliate_links'] ?? []) : null,
        ]);

        if ($request->hasFile('cover_image') || $request->boolean('remove_cover_image')) {
            if ($post->cover_image_path) {
                Storage::disk('public')->delete($post->cover_image_path);
            }

            $post->cover_image_path = $request->hasFile('cover_image')
                ? $this->storeImage($request->file('cover_image'), 'posts/covers')
                : null;
        }
    }

    private function storeImage(UploadedFile $file, string $directory): string
    {
        $path = $file->store($directory, 'public');

        abort_if($path === false, 500, 'The image could not be saved.');

        return $path;
    }
}
