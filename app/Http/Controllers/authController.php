<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class authController extends Controller
{
    // Halaman daftar akun customer
    public function register()
    {
        return view('pages.register');
    }

    public function processRegister(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'name.required'     => 'Nama wajib diisi.',
            'email.required'    => 'Email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'email.unique'      => 'Email ini sudah ada.',
            'password.required' => 'Password wajib diisi.',
            'password.min'      => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password gak cocok.',
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Regenerate session ID - cegah session fixation attack
        $request->session()->regenerate();
        Auth::login($user);

        return redirect()->intended(route('home'))->with('success', 'Akun berhasil dibuat, selamat datang!');
    }

    public function processLogin(Request $request)
    {
        $validated = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ], [
            'email.required'    => 'Email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $user = User::where('email', $validated['email'])->first();

        // Pesan SENGAJA sama untuk "email gak terdaftar" dan "password salah", biar
        // login gak bisa dipakai buat ngecek email mana yang punya akun. Kalau email
        // gak ada, tetap jalanin hashing sekali supaya waktu responsnya mirip (kalau
        // enggak, respons yang lebih cepat = email gak terdaftar).
        if (! $user) {
            Hash::make($validated['password']);
            return back()->withErrors(['login' => 'Email atau password salah, coba lagi.'])->onlyInput('email');
        }

        if (! Hash::check($validated['password'], $user->password)) {
            return back()->withErrors(['login' => 'Email atau password salah, coba lagi.'])->onlyInput('email');
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->intended(route('home'));
    }

    public function logout(Request $request)
    {
        // Auth::logout() sendiri udah cuma bersihin data guard 'web' (customer).
        // Sebelumnya dipanggil bareng session()->invalidate(), yang ngehapus SEMUA
        // isi session — termasuk session('login')/session('user') milik staff yang
        // numpang di session sama, jadi staff ikut ke-logout. Ganti ke regenerate()
        // biasa: tetap ganti session ID + CSRF token (proteksi fixation), tapi
        // gak flush data lain.
        Auth::logout();
        $request->session()->regenerate();

        return redirect()->route('home');
    }
}