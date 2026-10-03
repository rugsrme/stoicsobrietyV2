<?php

namespace App\Support;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Sleep;
use RuntimeException;

/**
 * Posts to the site's Facebook Page and its linked Instagram professional
 * account through Meta's Graph API.
 *
 * @see https://developers.facebook.com/docs/pages-api/posts
 * @see https://developers.facebook.com/docs/instagram-platform/content-publishing
 */
class MetaPublisher
{
    public function facebookEnabled(): bool
    {
        return filled(config('services.meta.page_id')) && filled(config('services.meta.page_access_token'));
    }

    public function instagramEnabled(): bool
    {
        return filled(config('services.meta.instagram_account_id')) && filled(config('services.meta.page_access_token'));
    }

    /**
     * Publish a link post to the Facebook Page. Facebook builds the preview
     * card from the link's Open Graph tags.
     *
     * @return string The new post's ID.
     */
    public function postToFacebook(string $message, string $link): string
    {
        return $this->send('/'.config('services.meta.page_id').'/feed', [
            'message' => $message,
            'link' => $link,
        ])['id'];
    }

    /**
     * Publish a single-image post to Instagram. The image must be a JPEG
     * that Meta can download from a public URL.
     *
     * @return string The new media's ID.
     */
    public function postToInstagram(string $imageUrl, string $caption): string
    {
        $account = config('services.meta.instagram_account_id');

        $container = $this->send("/{$account}/media", [
            'image_url' => $imageUrl,
            'caption' => $caption,
        ])['id'];

        $this->waitUntilReady($container);

        return $this->send("/{$account}/media_publish", [
            'creation_id' => $container,
        ])['id'];
    }

    /**
     * Instagram fetches and processes the image before it can be published.
     */
    private function waitUntilReady(string $container): void
    {
        for ($attempt = 0; $attempt < 10; $attempt++) {
            $status = $this->request()->get("/{$container}", ['fields' => 'status_code'])
                ->throw()
                ->json('status_code');

            if ($status === 'FINISHED') {
                return;
            }

            if ($status === 'ERROR' || $status === 'EXPIRED') {
                throw new RuntimeException("Instagram could not process the image ({$status}).");
            }

            Sleep::for(3)->seconds();
        }

        throw new RuntimeException('Instagram took too long to process the image.');
    }

    /**
     * @param  array<string, string>  $data
     * @return array{id: string}
     */
    private function send(string $path, array $data): array
    {
        try {
            /** @var array{id: string} */
            return $this->request()->asForm()->post($path, $data)->throw()->json();
        } catch (RequestException $e) {
            throw new RuntimeException($e->response->json('error.message') ?? $e->getMessage(), previous: $e);
        }
    }

    private function request(): PendingRequest
    {
        return Http::baseUrl('https://graph.facebook.com/'.config('services.meta.graph_version'))
            ->withToken((string) config('services.meta.page_access_token'))
            ->timeout(30);
    }
}
