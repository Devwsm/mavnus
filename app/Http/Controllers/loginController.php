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
        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request)
    {
        // Invalidate seluruh session + regenerate token — bukan hanya forget satu key
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }
}