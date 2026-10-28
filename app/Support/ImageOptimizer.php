<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Optimiza las imágenes subidas: las reduce a un ancho máximo y las guarda en WebP (mucho más
 * ligeras que los JPG/PNG originales de móvil o cámara), y crea una miniatura para los listados.
 * Si GD no puede escribir WebP, guarda el archivo tal cual.
 *
 * La miniatura de `properties/abc.webp` es `properties/thumbs/abc.webp`.
 */
class ImageOptimizer
{
    public const THUMB_WIDTH = 480;

    public static function store(UploadedFile $file, string $dir, int $maxWidth = 1600): string
    {
        $source = function_exists('imagewebp') ? @imagecreatefromstring((string) file_get_contents($file->getRealPath())) : false;

        if (! $source) {
            return $file->store($dir, 'public');
        }

        $name = Str::uuid().'.webp';
        $path = "{$dir}/{$name}";

        Storage::disk('public')->put($path, self::encode(self::scaled($source, $maxWidth)));
        Storage::disk('public')->put(self::thumbPath($path), self::encode(self::scaled($source, self::THUMB_WIDTH), 78));

        return $path;
    }

    public static function thumbPath(string $path): string
    {
        return dirname($path).'/thumbs/'.basename($path);
    }

    /** Miniatura si existe (imágenes ya optimizadas); si no, la imagen original. */
    public static function thumbOrOriginal(string $path): string
    {
        return Storage::disk('public')->exists(self::thumbPath($path)) ? self::thumbPath($path) : $path;
    }

    public static function delete(?string $path): void
    {
        if (! $path || str_starts_with($path, 'http')) {
            return;
        }

        Storage::disk('public')->delete([$path, self::thumbPath($path)]);
    }

    /** Copia una imagen (y su miniatura) a un archivo nuevo, para que borrar una no afecte a la otra. */
    public static function duplicate(string $path): ?string
    {
        $disk = Storage::disk('public');

        if (str_starts_with($path, 'http') || ! $disk->exists($path)) {
            return null;
        }

        $copy = dirname($path).'/'.Str::uuid().'.'.pathinfo($path, PATHINFO_EXTENSION);

        if (! $disk->copy($path, $copy)) {
            return null;
        }

        if ($disk->exists(self::thumbPath($path))) {
            $disk->copy(self::thumbPath($path), self::thumbPath($copy));
        }

        return $copy;
    }

    private static function scaled(\GdImage $image, int $maxWidth): \GdImage
    {
        $width = imagesx($image);

        if ($width <= $maxWidth) {
            return $image; // nunca se amplía
        }

        return imagescale($image, $maxWidth, -1, IMG_BICUBIC) ?: $image;
    }

    private static function encode(\GdImage $image, int $quality = 82): string
    {
        imagepalettetotruecolor($image);
        imagealphablending($image, false);
        imagesavealpha($image, true);

        ob_start();
        imagewebp($image, null, $quality);

        return (string) ob_get_clean();
    }
}
