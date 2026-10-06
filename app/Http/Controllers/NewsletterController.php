<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    // Form di footer. Sebelumnya <form> itu tidak punya action: tombol kirim cuma memuat
    // ulang halaman dan emailnya tidak disimpan ke mana pun.
    public function subscribe(Request $request)
    {
        // Honeypot: kolom tersembunyi yang tidak akan diisi manusia, tapi sering diisi bot.
        // Bot dibuat seolah berhasil (tanpa menyimpan apa pun) supaya tidak mencoba ulang.
        if ($request->filled('website')) {
            return response()->json(['message' => 'Terima kasih sudah berlangganan!']);
        }

        $validated = $request->validate([
            'email' => 'required|email:rfc|max:255',
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email'    => 'Format email tidak valid.',
        ]);

        // Email yang sudah terdaftar dijawab sama persis dengan yang baru, supaya form ini
        // tidak bisa dipakai untuk mengecek email siapa saja yang sudah berlangganan.
        NewsletterSubscriber::firstOrCreate(['email' => mb_strtolower($validated['email'])]);

        return response()->json(['message' => 'Terima kasih sudah berlangganan!']);
    }
}