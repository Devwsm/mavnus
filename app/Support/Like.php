<?php

namespace App\Support;

class Like
{
    /**
     * Ubah kata kunci jadi pola LIKE "mengandung" yang aman: karakter % dan _ yang diketik
     * pengguna dicari apa adanya (bukan wildcard). '!' dipakai sebagai karakter escape
     * (bukan backslash) supaya hasilnya sama di MySQL dan SQLite, jadi query HARUS memakai
     * "... LIKE ? ESCAPE '!'". Lihat Like::SQL_ESCAPE.
     */
    public static function contains(string $term): string
    {
        return '%' . str_replace(['!', '%', '_'], ['!!', '!%', '!_'], $term) . '%';
    }

    /** Potongan SQL "<kolom> LIKE ? ESCAPE '!'" untuk dipakai bersama Like::contains(). */
    public static function sql(string $column): string
    {
        return "{$column} LIKE ? ESCAPE '!'";
    }
}