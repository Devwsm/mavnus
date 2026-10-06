<?php

namespace App\Support;

use Illuminate\Support\Str;

/**
 * Kunci keranjang belanja untuk pengunjung ini.
 *
 * Dulu keranjang diikat ke session()->getId(). Masalahnya, ID session SENGAJA
 * diganti (regenerate) tiap login, daftar, dan logout untuk mencegah session
 * fixation — akibatnya isi keranjang "hilang" persis saat pembeli login di tengah
 * belanja. Sekarang keranjang diikat ke token sendiri yang disimpan DI DALAM
 * session, jadi ikut terbawa walau ID session berganti.
 */
class CartSession
{
    private const KEY = 'cart_key';

    /** Ambil kunci keranjang; dibuat kalau belum ada (dipakai saat menulis/membaca keranjang). */
    public static function key(): string
    {
        $key = session(self::KEY);

        if (! is_string($key) || $key === '') {
            $key = (string) Str::uuid();
            session([self::KEY => $key]);
        }

        return $key;
    }

    /** Ambil kunci tanpa membuatnya (dipakai untuk sekadar menghitung badge keranjang). */
    public static function peek(): ?string
    {
        $key = session(self::KEY);

        return is_string($key) && $key !== '' ? $key : null;
    }
}