/**
 * Shrink an image in the browser before uploading it.
 *
 * PHP often caps uploads at 2 MB (upload_max_filesize), and phone photos are
 * routinely bigger, so photos are scaled to a sensible web size and
 * re-encoded until they fit. GIFs (possibly animated) and SVGs are left as-is.
 */
const MAX_DIMENSION = 2000;
const MAX_BYTES = 1.9 * 1024 * 1024;

export async function shrinkImage(file: File): Promise<File> {
    if (!file.type.startsWith('image/') || /gif|svg/.test(file.type)) {
        return file;
    }

    let bitmap: ImageBitmap;

    try {
        bitmap = await createImageBitmap(file);
    } catch {
        return file; // A format the browser can't decode; let the server decide.
    }

    const scale = Math.min(
        1,
        MAX_DIMENSION / Math.max(bitmap.width, bitmap.height),
    );

    if (scale === 1 && file.size <= MAX_BYTES) {
        bitmap.close();

        return file;
    }

    const canvas = document.createElement('canvas');
    canvas.width = Math.round(bitmap.width * scale);
    canvas.height = Math.round(bitmap.height * scale);
    canvas
        .getContext('2d')
        ?.drawImage(bitmap, 0, 0, canvas.width, canvas.height);
    bitmap.close();

    // PNGs may have transparency: prefer WebP (keeps alpha) where the browser
    // can encode it, otherwise JPEG.
    const preferred = file.type === 'image/png' ? 'image/webp' : 'image/jpeg';
    let blob: Blob | null = null;

    for (const quality of [0.85, 0.75, 0.65, 0.5]) {
        blob = await new Promise<Blob | null>((resolve) =>
            canvas.toBlob(resolve, preferred, quality),
        );

        if (blob && blob.type !== preferred) {
            blob = await new Promise<Blob | null>((resolve) =>
                canvas.toBlob(resolve, 'image/jpeg', quality),
            );
        }

        if (blob && blob.size <= MAX_BYTES) break;
    }

    if (!blob) return file;

    const extension = blob.type === 'image/webp' ? 'webp' : 'jpg';
    const name = file.name.replace(/\.[^.]+$/, '') + '.' + extension;

    return new File([blob], name, { type: blob.type });
}
