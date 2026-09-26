<?php

namespace App\Support;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

/**
 * Cleans rich-text post bodies coming out of the admin editor before they
 * are stored, so the public pages can render them as HTML safely.
 */
class PostHtml
{
    private const BLOCKS = ['p', 'h2', 'h3', 'h4', 'blockquote', 'li'];

    private const IMAGE_TYPES = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
        'image/webp' => 'webp',
    ];

    public static function sanitize(string $html): string
    {
        $config = (new HtmlSanitizerConfig)
            ->allowLinkSchemes(['http', 'https', 'mailto'])
            ->allowMediaSchemes(['http', 'https'])
            ->allowRelativeLinks()
            ->allowRelativeMedias()
            ->allowElement('p', ['style'])
            ->allowElement('h2', ['style'])
            ->allowElement('h3', ['style'])
            ->allowElement('h4', ['style'])
            ->allowElement('blockquote', ['style'])
            ->allowElement('strong')
            ->allowElement('b')
            ->allowElement('em')
            ->allowElement('i')
            ->allowElement('u')
            ->allowElement('s')
            ->allowElement('br')
            ->allowElement('hr')
            ->allowElement('ul')
            ->allowElement('ol', ['start'])
            ->allowElement('li', ['style'])
            ->allowElement('a', ['href', 'target', 'rel'])
            ->allowElement('img', ['src', 'alt', 'title'])
            ->forceAttribute('a', 'rel', 'noopener noreferrer')
            ->withMaxInputLength(1_000_000);

        $clean = (new HtmlSanitizer($config))->sanitize($html);

        // The only inline style the editor produces is text alignment.
        return preg_replace_callback(
            '/ style="([^"]*)"/',
            function (array $m): string {
                return preg_match('/text-align:\s*(left|center|right|justify)/', html_entity_decode($m[1]), $align)
                    ? ' style="text-align: '.$align[1].'"'
                    : '';
            },
            $clean,
        ) ?? '';
    }

    /**
     * Copy images hosted elsewhere (e.g. pasted from Facebook, whose image
     * URLs expire) onto the public disk and point the post at the copies.
     * Images that can't be fetched keep their original URL.
     */
    public static function localizeImages(string $html): string
    {
        $disk = Storage::disk('public');
        $ownPrefix = rtrim($disk->url(''), '/');

        return preg_replace_callback(
            '/(<img\b[^>]*?\bsrc=")([^"]+)(")/i',
            function (array $m) use ($disk, $ownPrefix): string {
                $src = html_entity_decode($m[2]);

                if (! preg_match('#^https?://#i', $src) || str_starts_with($src, $ownPrefix)) {
                    return $m[0];
                }

                try {
                    $response = Http::timeout(10)->get($src);
                } catch (\Throwable) {
                    return $m[0];
                }

                $bytes = $response->body();
                $contentType = strtolower(trim(explode(';', $response->header('Content-Type'))[0]));
                $extension = self::IMAGE_TYPES[$contentType] ?? null;

                if (! $response->successful() || $extension === null || strlen($bytes) > 5 * 1024 * 1024 || @getimagesizefromstring($bytes) === false) {
                    return $m[0];
                }

                $path = 'posts/content/'.Str::random(40).'.'.$extension;
                $disk->put($path, $bytes);

                return $m[1].e($disk->url($path)).$m[3];
            },
            $html,
        ) ?? $html;
    }

    /**
     * A plain-text summary of a post body for listings.
     */
    public static function excerpt(string $html, int $limit = 220): string
    {
        $text = html_entity_decode(strip_tags(preg_replace('/<\/(?:'.implode('|', self::BLOCKS).')>/', '$0 ', $html) ?? ''));

        return Str::limit(trim(preg_replace('/\s+/u', ' ', $text) ?? ''), $limit);
    }
}
