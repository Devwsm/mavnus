<?php

namespace App\Support;

/**
 * Pilih URL gambar hasil optimasi (.webp) kalau filenya benar-benar ada & tidak kosong,
 * kalau tidak jatuh ke gambar asli. Jadi situs tetap tampil normal walau `npm run images`
 * belum dijalankan (atau file .webp belum ikut ter-copy ke server).
 */
class Img
{
    public static function url(string $preferred, string $fallback): string
    {
        $path = public_path($preferred);

        return asset(is_file($path) && filesize($path) > 0 ? $preferred : $fallback);
    }
}