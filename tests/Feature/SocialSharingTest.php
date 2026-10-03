<?php

use App\Jobs\SharePostToSocial;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Client\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Sleep;

beforeEach(function () {
    config([
        'services.meta.page_id' => 'page1',
        'services.meta.page_access_token' => 'token',
        'services.meta.instagram_account_id' => 'ig1',
    ]);

    Storage::fake('public');
    Sleep::fake();
});

function fakeMeta(): void
{
    Http::fake([
        '*/page1/feed' => Http::response(['id' => 'page1_post1']),
        '*/ig1/media_publish' => Http::response(['id' => 'media1']),
        '*/ig1/media' => Http::response(['id' => 'container1']),
        '*/container1*' => Http::response(['status_code' => 'FINISHED']),
    ]);
}

function publishPost(array $overrides = []): Illuminate\Testing\TestResponse
{
    return test()->actingAs(User::factory()->admin()->create())->post(route('admin.posts.store'), [
        'category' => 'reflection',
        'title' => 'Sharing Is Caring',
        'slug' => 'sharing-is-caring',
        'body' => 'The body.',
        'published' => true,
        'share_facebook' => true,
        'share_instagram' => true,
        ...$overrides,
    ]);
}

test('publishing a reflection shares it to the ticked networks', function () {
    Bus::fake();

    publishPost(['share_instagram' => false]);

    Bus::assertDispatched(SharePostToSocial::class, fn ($job) => $job->facebook && ! $job->instagram);
});

test('drafts and journal entries are never shared', function () {
    Bus::fake();

    publishPost(['published' => false]);
    publishPost(['category' => 'journal', 'slug' => 'private']);

    Bus::assertNotDispatched(SharePostToSocial::class);
});

test('a post already shared is not shared again', function () {
    Bus::fake();
    $post = Post::factory()->create(['facebook_post_id' => 'x', 'instagram_media_id' => 'y']);

    $this->actingAs(User::factory()->admin()->create())->put(route('admin.posts.update', $post), [
        'category' => 'reflection',
        'title' => $post->title,
        'slug' => $post->slug,
        'body' => $post->body,
        'published' => true,
        'share_facebook' => true,
        'share_instagram' => true,
    ]);

    Bus::assertNotDispatched(SharePostToSocial::class);
});

test('a published post is posted to Facebook and Instagram', function () {
    fakeMeta();

    publishPost([
        'cover_image' => UploadedFile::fake()->image('cover.png', 400, 600),
    ]);

    $post = Post::where('slug', 'sharing-is-caring')->firstOrFail();

    expect($post->facebook_post_id)->toBe('page1_post1')
        ->and($post->instagram_media_id)->toBe('media1')
        ->and($post->social_share_error)->toBeNull();

    Http::assertSent(fn (Request $request) => str_ends_with($request->url(), '/page1/feed')
        && $request['link'] === route('reflections.show', $post)
        && $request->hasHeader('Authorization', 'Bearer token'));

    Http::assertSent(fn (Request $request) => str_ends_with($request->url(), '/ig1/media')
        && str_ends_with($request['image_url'], '.jpg')
        && str_contains($request['caption'], 'Read the full reflection at'));

    // The Instagram image is only needed until Meta has fetched it.
    expect(Storage::disk('public')->files('posts/social'))->toBeEmpty();
});

test('the Instagram image is a 4:5 JPEG even for a tall book cover', function () {
    Http::fake([
        '*/ig1/media_publish' => Http::response(['id' => 'media1']),
        '*/ig1/media' => function (Request $request) {
            $path = str($request['image_url'])->after('/storage/')->toString();
            [$width, $height, $type] = getimagesizefromstring(Storage::disk('public')->get($path));

            expect([$width, $height, $type])->toBe([1080, 1350, IMAGETYPE_JPEG]);

            return Http::response(['id' => 'container1']);
        },
        '*/container1*' => Http::response(['status_code' => 'FINISHED']),
    ]);

    $post = Post::factory()->review()->create([
        'cover_image_path' => UploadedFile::fake()->image('cover.png', 400, 600)->store('posts/covers', 'public'),
    ]);

    (new SharePostToSocial($post, false, true))->handle(app(App\Support\MetaPublisher::class));

    expect($post->fresh()->instagram_media_id)->toBe('media1');
});

test('a failed share is recorded and the other network still goes out', function () {
    Http::fake([
        '*/page1/feed' => Http::response(['error' => ['message' => 'Token expired']], 400),
        '*/ig1/media_publish' => Http::response(['id' => 'media1']),
        '*/ig1/media' => Http::response(['id' => 'container1']),
        '*/container1*' => Http::response(['status_code' => 'FINISHED']),
    ]);

    publishPost();

    $post = Post::where('slug', 'sharing-is-caring')->firstOrFail();

    expect($post->facebook_post_id)->toBeNull()
        ->and($post->instagram_media_id)->toBe('media1')
        ->and($post->social_share_error)->toBe('Facebook: Token expired');
});

test('networks that are not set up are skipped', function () {
    config(['services.meta.instagram_account_id' => null]);
    fakeMeta();

    publishPost();

    Http::assertSentCount(1);
    expect(Post::where('slug', 'sharing-is-caring')->value('instagram_media_id'))->toBeNull();
});

test('the editor only offers the networks that are set up', function () {
    config(['services.meta.instagram_account_id' => null]);

    $this->actingAs(User::factory()->admin()->create())
        ->get(route('admin.posts.create'))
        ->assertInertia(fn ($page) => $page
            ->where('social.facebook', true)
            ->where('social.instagram', false)
        );
});

test('sharing status is not exposed on the public site', function () {
    $post = Post::factory()->create(['social_share_error' => 'Facebook: secret']);

    $this->get(route('reflections.show', $post))
        ->assertInertia(fn ($page) => $page->missing('post.social_share_error'));
});

test('posts have their own link preview', function () {
    $post = Post::factory()->review()->create([
        'title' => 'A Review Worth Sharing',
        'excerpt' => 'Why this book matters.',
        'cover_image_path' => 'posts/covers/cover.jpg',
    ]);

    $this->get(route('reviews.show', $post))
        ->assertSee('<meta property="og:title" content="A Review Worth Sharing">', false)
        ->assertSee('<meta property="og:description" content="Why this book matters.">', false)
        ->assertSee('<meta property="og:image" content="'.Storage::disk('public')->url('posts/covers/cover.jpg').'">', false)
        ->assertSee('<meta property="og:type" content="article">', false);
});

test('journal entries get no link preview', function () {
    $post = Post::factory()->journal()->create(['title' => 'Private Thoughts']);

    $this->actingAs(User::factory()->admin()->create())
        ->get(route('journal.show', $post))
        ->assertDontSee('content="Private Thoughts"', false);
});
