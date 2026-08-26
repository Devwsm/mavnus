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
            'email.unique'      => 'Email ini udah kepake, coba masuk aja.',
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
            'password.required' => 'Password wajib diisi.',
        ]);

        if (! Auth::attempt(['email' => $validated['email'], 'password' => $validated['password']], $request->boolean('remember'))) {
            // Pesan sengaja dibuat generik - gak bocorin mana yang salah (email atau password)
            return back()->withErrors(['login' => 'Email atau password salah.'])->onlyInput('email');
        }

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