<?php

namespace App\Jobs;

use App\Models\Post;
use App\Support\MetaPublisher;
use App\Support\SocialImage;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

/**
 * Shares a published reflection or book review to the Facebook Page and
 * Instagram. Dispatched after the response, so the editor doesn't wait on
 * Meta and no queue worker is needed.
 *
 * Each network is shared to at most once; a failure is recorded on the post
 * and the next save with the box ticked tries again.
 */
class SharePostToSocial
{
    use Dispatchable;

    public function __construct(
        public Post $post,
        public bool $facebook,
        public bool $instagram,
    ) {}

    public function handle(MetaPublisher $meta): void
    {
        $post = $this->post->fresh();

        if (! $post || ! $post->isPublished() || ! $post->category->isPublic()) {
            return;
        }

        $errors = [];

        if ($this->facebook && $meta->facebookEnabled() && ! $post->facebook_post_id) {
            try {
                $post->facebook_post_id = $meta->postToFacebook($post->facebookMessage(), $post->publicUrl());
            } catch (Throwable $e) {
                $errors[] = 'Facebook: '.$e->getMessage();
            }
        }

        if ($this->instagram && $meta->instagramEnabled() && ! $post->instagram_media_id) {
            $image = null;

            try {
                $image = SocialImage::forInstagram($post);
                $post->instagram_media_id = $meta->postToInstagram(
                    Storage::disk('public')->url($image),
                    $post->instagramCaption(),
                );
            } catch (Throwable $e) {
                $errors[] = 'Instagram: '.$e->getMessage();
            } finally {
                // Meta keeps its own copy once the post is published.
                if ($image) {
                    Storage::disk('public')->delete($image);
                }
            }
        }

        if ($errors) {
            Log::warning('Sharing post failed', ['post' => $post->id, 'errors' => $errors]);
        }

        $post->social_share_error = $errors ? implode("\n", $errors) : null;
        $post->save();
    }
}
