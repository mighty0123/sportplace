<?php

namespace App\Support;

class Media
{
    /**
     * Alamat gambar dari path di database
     */
    public static function url(?string $path, ?string $fallback = null): ?string
    {
        if (blank($path)) {
            return $fallback;
        }
        if (preg_match('#^https?://#i', $path)) {
            return $path;
        }

        $path = ltrim($path, '/');

        // File statis yang memang ada di public/ (mis. images/courts/...), selain itu hasil unggahan di storage.
        return is_file(public_path($path)) ? asset($path) : asset('storage/' . $path);
    }
}