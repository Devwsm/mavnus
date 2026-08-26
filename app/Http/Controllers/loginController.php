<?php

namespace App\Http\Controllers;

use App\Models\account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class loginController extends Controller
{
    //
    // Method login() customer dipindah ke authController.
    // View 'pages.login' sekarang di-render lewat authController context
    // (route GET /login masih nunjuk ke sini cuma buat nampilin view-nya doang).
    public function login()
    {
        return view('pages.login');
    }

    // Login STAFF/crew - halaman terpisah, gak dilink di mana pun di sisi customer
    public function crewLogin()
    {
        return view('pages.crew-login');
    }

    public function prosesLogin(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255',
            'password' => 'required|string|min:6',
        ], [
            'username.required' => 'Username harus diisi.',
            'password.required' => 'Password harus diisi.',
            'password.min'      => 'Password minimal 6 karakter.',
        ]);

        $user = account::where('username', $request->username)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            // Pesan sengaja dibuat generik — tidak bocorkan mana yang salah
            return back()->withErrors(['login' => 'Username atau password salah.'])->onlyInput('username');
        }

        // Regenerate session ID — cegah session fixation attack
        $request->session()->regenerate();
        $request->session()->put('login', true);
        $request->session()->put('user', $user->username);

        // Pakai key intended sendiri ('staff_intended_url'), JANGAN pakai
        // redirect()->intended() bawaan Laravel — itu baca session('url.intended')
        // yang juga dipakai flow auth customer (guard 'web'). Kalau dipakai bareng,
        // staff bisa kelempar ke URL customer yang gak ada hubungannya (lalu balik
        // ke halaman login customer karena belum login sebagai customer).
        $intendedUrl = $request->session()->pull('staff_intended_url', route('dashboard'));
        return redirect()->to($intendedUrl);
    }

    public function logout(Request $request)
    {
        // Cuma bersihkan data punya sesi staff, JANGAN invalidate() seluruh session —
        // itu juga bakal ngehapus status login customer (guard 'web') yang numpang
        // di session yang sama, jadi customer ikut ke-logout.
        $request->session()->forget(['login', 'user']);
        // Tetap regenerate ID + token session buat keamanan, tanpa flush data lain.
        $request->session()->regenerate();
        return redirect()->route('home');
    }
}