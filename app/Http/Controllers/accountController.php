<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class accountController extends Controller
{
    // Halaman akun customer - cuma bisa diakses kalau udah login (middleware 'auth' bawaan Laravel)
    public function index()
    {
        return view('pages.account', [
            'user' => Auth::user(),
        ]);
    }

    // Proses simpan perubahan profil (nama, email, no. HP, alamat)
    public function update(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'email'            => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone'            => 'nullable|string|max:20',
            'address'          => 'nullable|string|max:1000',
            'current_password' => 'required|string',
        ], [
            'name.required'             => 'Nama wajib diisi.',
            'email.required'            => 'Email wajib diisi.',
            'email.email'               => 'Format email tidak valid.',
            'email.unique'               => 'Email ini udah dipake akun lain.',
            'phone.max'                 => 'Nomor HP maksimal 20 karakter.',
            'current_password.required' => 'Masukin password saat ini buat konfirmasi perubahan.',
        ]);

        // Konfirmasi password saat ini - biar perubahan data (apalagi ganti email)
        // gak bisa dilakuin sembarangan kalau sesi ke-hijack orang lain
        if (! Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Password salah.'])->withInput();
        }

        $user->update([
            'name'    => $validated['name'],
            'email'   => $validated['email'],
            'phone'   => $validated['phone'],
            'address' => $validated['address'],
        ]);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    // Riwayat pesanan - cuma nampilin order milik akun yang lagi login
    public function orders()
    {
        $orders = Order::where('user_id', Auth::id())
            ->with('items')
            ->latest()
            ->paginate(10);

        return view('pages.account-orders', [
            'user'   => Auth::user(),
            'orders' => $orders,
        ]);
    }
}