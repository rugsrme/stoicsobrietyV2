<?php

namespace App\Support;

use App\Models\Post;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

/**
 * Builds the image posted to Instagram for a reflection or book review.
 *
 * Instagram only accepts JPEGs between 4:5 portrait and 1.91:1 landscape, so
 * the cover (or the site's brand image when there is none) is centred on a
 * 4:5 canvas in the site's background colour. A 2:3 book cover would
 * otherwise be rejected.
 */
class SocialImage
{
    private const WIDTH = 1080;

    private const HEIGHT = 1350;

    private const PADDING = 90;

    /** The site's dark background (theme-color in app.blade.php). */
    private const BACKGROUND = [0x05, 0x0A, 0x13];

    /**
     * @return string The image's path on the public disk.
     */
    public static function forInstagram(Post $post): string
    {
        $source = $post->cover_image_path
            ? Storage::disk('public')->get($post->cover_image_path)
            : file_get_contents(public_path('brand/og-image.jpg'));

        $image = $source ? @imagecreatefromstring($source) : false;

        if ($image === false) {
            throw new RuntimeException('The cover image could not be read.');
        }

        $canvas = imagecreatetruecolor(self::WIDTH, self::HEIGHT);
        imagefill($canvas, 0, 0, (int) imagecolorallocate($canvas, ...self::BACKGROUND));

        $width = imagesx($image);
        $height = imagesy($image);
        $scale = min((self::WIDTH - 2 * self::PADDING) / $width, (self::HEIGHT - 2 * self::PADDING) / $height);
        $fitWidth = (int) round($width * $scale);
        $fitHeight = (int) round($height * $scale);

        imagecopyresampled(
            $canvas, $image,
            intdiv(self::WIDTH - $fitWidth, 2), intdiv(self::HEIGHT - $fitHeight, 2), 0, 0,
            $fitWidth, $fitHeight, $width, $height,
        );

        ob_start();
        imagejpeg($canvas, null, 88);
        $jpeg = (string) ob_get_clean();

        // A fresh name each time, so Meta never fetches a cached older version.
        $path = "posts/social/{$post->id}-".time().'.jpg';
        Storage::disk('public')->put($path, $jpeg);

        return $path;
    }
}
