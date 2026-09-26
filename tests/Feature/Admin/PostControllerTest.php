<?php

use App\Enums\PostCategory;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

test('guests cannot access the admin posts area', function () {
    $response = $this->get(route('admin.posts.index'));

    $response->assertRedirect(route('login'));
});

test('non-admin authenticated users cannot manage posts', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create();

    $this->actingAs($user)->get(route('admin.posts.index'))->assertForbidden();
    $this->actingAs($user)->get(route('admin.posts.edit', $post))->assertForbidden();
    $this->actingAs($user)->post(route('admin.posts.images.store'))->assertForbidden();

    $this->actingAs($user)->get(route('admin.posts.create'))->assertForbidden();

    $this->actingAs($user)->post(route('admin.posts.store'), [
        'title' => 'Nope',
        'slug' => 'nope',
        'body' => 'Nope',
        'published' => false,
    ])->assertForbidden();

    $this->actingAs($user)->put(route('admin.posts.update', $post), [
        'title' => 'Nope',
        'slug' => $post->slug,
        'body' => 'Nope',
        'published' => false,
    ])->assertForbidden();

    $this->actingAs($user)->delete(route('admin.posts.destroy', $post))->assertForbidden();
});

test('admins can list posts', function () {
    $user = User::factory()->admin()->create();
    Post::factory()->create();

    $response = $this->actingAs($user)->get(route('admin.posts.index'));

    $response->assertOk();
});

test('a post can be created as a draft', function () {
    $user = User::factory()->admin()->create();

    $response = $this->actingAs($user)->post(route('admin.posts.store'), [
        'category' => 'reflection',
        'title' => 'A New Post',
        'slug' => 'a-new-post',
        'excerpt' => 'A short summary',
        'body' => 'The full body of the post.',
        'published' => false,
    ]);

    $response->assertRedirect(route('admin.posts.index', ['category' => 'reflection']));

    $post = Post::where('slug', 'a-new-post')->firstOrFail();

    expect($post->author_id)->toBe($user->id);
    expect($post->published_at)->toBeNull();
});

test('a post can be created published', function () {
    $user = User::factory()->admin()->create();

    $this->actingAs($user)->post(route('admin.posts.store'), [
        'category' => 'reflection',
        'title' => 'A Published Post',
        'slug' => 'a-published-post',
        'body' => 'The full body of the post.',
        'published' => true,
    ]);

    $post = Post::where('slug', 'a-published-post')->firstOrFail();

    expect($post->published_at)->not->toBeNull();
});

test('a post slug must be unique', function () {
    $user = User::factory()->admin()->create();
    Post::factory()->create(['slug' => 'taken']);

    $response = $this->actingAs($user)->post(route('admin.posts.store'), [
        'category' => 'reflection',
        'title' => 'Another Post',
        'slug' => 'taken',
        'body' => 'Body',
        'published' => false,
    ]);

    $response->assertSessionHasErrors('slug');
});

test('a post can be updated and published', function () {
    $user = User::factory()->admin()->create();
    $post = Post::factory()->create(['published_at' => null]);

    $response = $this->actingAs($user)->put(route('admin.posts.update', $post), [
        'category' => 'reflection',
        'title' => 'Updated Title',
        'slug' => $post->slug,
        'body' => 'Updated body',
        'published' => true,
    ]);

    $response->assertRedirect(route('admin.posts.index', ['category' => 'reflection']));

    $post->refresh();

    expect($post->title)->toBe('Updated Title');
    expect($post->published_at)->not->toBeNull();
});

test('a post can be unpublished', function () {
    $user = User::factory()->admin()->create();
    $post = Post::factory()->create(['published_at' => now()]);

    $this->actingAs($user)->put(route('admin.posts.update', $post), [
        'category' => 'reflection',
        'title' => $post->title,
        'slug' => $post->slug,
        'body' => $post->body,
        'published' => false,
    ]);

    expect($post->refresh()->published_at)->toBeNull();
});

test('a post can be deleted', function () {
    $user = User::factory()->admin()->create();
    $post = Post::factory()->create();

    $response = $this->actingAs($user)->delete(route('admin.posts.destroy', $post));

    $response->assertRedirect(route('admin.posts.index'));
    expect(Post::find($post->id))->toBeNull();
});

test('admins can filter the list by section', function () {
    $user = User::factory()->admin()->create();
    Post::factory()->create();
    $review = Post::factory()->review()->create();

    $this->actingAs($user)
        ->get(route('admin.posts.index', ['category' => 'book-review']))
        ->assertInertia(fn ($page) => $page
            ->component('admin/posts/Index')
            ->has('posts', 1)
            ->where('posts.0.id', $review->id)
        );
});

test('a book review can be created with its book details and affiliate links', function () {
    Storage::fake('public');
    $user = User::factory()->admin()->create();

    $this->actingAs($user)->post(route('admin.posts.store'), [
        'category' => 'book-review',
        'title' => 'A Review',
        'slug' => 'a-review',
        'body' => '<p>Worth reading.</p>',
        'reviewed_book_title' => 'Meditations',
        'reviewed_book_author' => 'Marcus Aurelius',
        'rating' => 5,
        'affiliate_links' => [
            ['label' => 'Amazon', 'url' => 'https://amzn.to/example'],
        ],
        'cover_image' => UploadedFile::fake()->image('cover.jpg', 400, 600),
        'published' => true,
    ])->assertRedirect(route('admin.posts.index', ['category' => 'book-review']));

    $post = Post::where('slug', 'a-review')->firstOrFail();

    expect($post->category)->toBe(PostCategory::BookReview);
    expect($post->reviewed_book_title)->toBe('Meditations');
    expect($post->rating)->toBe(5);
    expect($post->affiliate_links)->toBe([['label' => 'Amazon', 'url' => 'https://amzn.to/example']]);
    Storage::disk('public')->assertExists($post->cover_image_path);
});

test('a book review needs the title of the book', function () {
    $user = User::factory()->admin()->create();

    $this->actingAs($user)->post(route('admin.posts.store'), [
        'category' => 'book-review',
        'title' => 'A Review',
        'slug' => 'a-review',
        'body' => '<p>Worth reading.</p>',
    ])->assertSessionHasErrors('reviewed_book_title');
});

test('affiliate links must be web URLs', function () {
    $user = User::factory()->admin()->create();

    $this->actingAs($user)->post(route('admin.posts.store'), [
        'category' => 'book-review',
        'title' => 'A Review',
        'slug' => 'a-review',
        'body' => '<p>Worth reading.</p>',
        'reviewed_book_title' => 'Meditations',
        'affiliate_links' => [['label' => 'Bad', 'url' => 'javascript:alert(1)']],
    ])->assertSessionHasErrors('affiliate_links.0.url');
});

test('review-only fields are cleared on reflections', function () {
    $user = User::factory()->admin()->create();
    $post = Post::factory()->review()->create();

    $this->actingAs($user)->put(route('admin.posts.update', $post), [
        'category' => 'reflection',
        'title' => $post->title,
        'slug' => $post->slug,
        'body' => $post->body,
        'reviewed_book_title' => 'Still here?',
        'rating' => 4,
    ]);

    $post->refresh();

    expect($post->category)->toBe(PostCategory::Reflection);
    expect($post->reviewed_book_title)->toBeNull();
    expect($post->rating)->toBeNull();
    expect($post->affiliate_links)->toBeNull();
});

test('post bodies are sanitized', function () {
    $user = User::factory()->admin()->create();

    $this->actingAs($user)->post(route('admin.posts.store'), [
        'category' => 'reflection',
        'title' => 'Formatting',
        'slug' => 'formatting',
        'body' => '<h2 style="text-align: center; color: red">Hi</h2>'
            .'<p onclick="steal()">Text <strong>bold</strong> <a href="javascript:alert(1)">bad</a> <a href="https://example.com">good</a></p>'
            .'<script>alert(1)</script><img src="/storage/posts/content/a.jpg" onerror="x()">',
    ]);

    $body = Post::where('slug', 'formatting')->firstOrFail()->body;

    expect($body)
        ->toContain('<h2 style="text-align: center">Hi</h2>')
        ->toContain('<strong>bold</strong>')
        ->toContain('<a href="https://example.com" rel="noopener noreferrer">good</a>')
        ->toContain('<img src="/storage/posts/content/a.jpg" />')
        ->not->toContain('script')
        ->not->toContain('onclick')
        ->not->toContain('onerror')
        ->not->toContain('javascript:')
        ->not->toContain('color: red');
});

test('images pasted from other sites are copied to local storage', function () {
    Storage::fake('public');
    Http::fake([
        'scontent.example.net/*' => Http::response(
            UploadedFile::fake()->image('fb.jpg', 20, 20)->getContent(),
            200,
            ['Content-Type' => 'image/jpeg'],
        ),
        'broken.example.net/*' => Http::response('nope', 404),
    ]);
    $user = User::factory()->admin()->create();

    $this->actingAs($user)->post(route('admin.posts.store'), [
        'category' => 'reflection',
        'title' => 'From Facebook',
        'slug' => 'from-facebook',
        'body' => '<p>Hi</p><img src="https://scontent.example.net/photo.jpg?sig=abc&amp;x=1"><img src="https://broken.example.net/gone.jpg">',
    ]);

    $body = Post::where('slug', 'from-facebook')->firstOrFail()->body;

    expect($body)->not->toContain('scontent.example.net');
    expect($body)->toContain('https://broken.example.net/gone.jpg');
    expect(Storage::disk('public')->files('posts/content'))->toHaveCount(1);
});

test('admins can upload images from the editor', function () {
    Storage::fake('public');
    $user = User::factory()->admin()->create();

    $response = $this->actingAs($user)->post(route('admin.posts.images.store'), [
        'image' => UploadedFile::fake()->image('photo.png'),
    ]);

    $response->assertOk()->assertJsonStructure(['url']);
    expect(Storage::disk('public')->files('posts/content'))->toHaveCount(1);
});

test('the editor upload only accepts images', function () {
    $user = User::factory()->admin()->create();

    $this->actingAs($user)
        ->postJson(route('admin.posts.images.store'), [
            'image' => UploadedFile::fake()->create('notes.pdf', 10, 'application/pdf'),
        ])
        ->assertUnprocessable();
});

test('a journal entry can be created', function () {
    $user = User::factory()->admin()->create();

    $this->actingAs($user)->post(route('admin.posts.store'), [
        'category' => 'journal',
        'title' => 'Private thoughts',
        'slug' => 'private-thoughts',
        'body' => '<p>Just for me.</p>',
        'published' => true,
    ])->assertRedirect(route('admin.posts.index', ['category' => 'journal']));

    expect(Post::where('slug', 'private-thoughts')->firstOrFail()->category)
        ->toBe(PostCategory::Journal);
});

test('the editor offers all three sections', function () {
    $user = User::factory()->admin()->create();

    $this->actingAs($user)
        ->get(route('admin.posts.create', ['category' => 'journal']))
        ->assertInertia(fn ($page) => $page
            ->where('category', 'journal')
            ->where('categories', fn ($categories) => collect($categories)->pluck('value')->all() === ['reflection', 'book-review', 'journal'])
        );
});

test('a post can be ticked to show on the home page', function () {
    $user = User::factory()->admin()->create();

    $this->actingAs($user)->post(route('admin.posts.store'), [
        'category' => 'reflection',
        'title' => 'Front Page',
        'slug' => 'front-page',
        'body' => 'Body.',
        'published' => true,
        'is_featured' => true,
    ])->assertRedirect();

    expect(Post::where('slug', 'front-page')->first()->is_featured)->toBeTrue();
});

test('journal entries can never be put on the home page', function () {
    $user = User::factory()->admin()->create();

    $this->actingAs($user)->post(route('admin.posts.store'), [
        'category' => 'journal',
        'title' => 'Private',
        'slug' => 'private',
        'body' => 'Body.',
        'published' => true,
        'is_featured' => true,
    ])->assertRedirect();

    expect(Post::where('slug', 'private')->first()->is_featured)->toBeFalse();
});
