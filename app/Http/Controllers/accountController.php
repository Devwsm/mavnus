<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class accountController extends Controller
{
    // Halaman akun customer (read-only overview) - cuma bisa diakses kalau udah login
    public function index()
    {
        $userId = Auth::id();

        return view('pages.account', [
            'user' => Auth::user(),
            // Ringkasan status pesanan buat quick-access di halaman profil
            'orderCounts' => [
                'pending'    => Order::where('user_id', $userId)->where('status', 'pending')->count(),
                'processing' => Order::where('user_id', $userId)->where('status', 'processing')->count(),
                'shipped'    => Order::where('user_id', $userId)->where('status', 'shipped')->count(),
                'completed'  => Order::where('user_id', $userId)->where('status', 'completed')->count(),
            ],
        ]);
    }

    // Halaman edit profil - terpisah dari halaman overview, diakses lewat ikon pensil
    public function edit()
    {
        return view('pages.account-edit', [
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

        // Balik ke halaman overview (bukan back(), biar konsisten abis simpan
        // selalu mendarat di halaman profil, bukan nyangkut di form edit)
        return redirect()->route('account')->with('success', 'Profil berhasil diperbarui.');
    }

    // Riwayat pesanan - cuma nampilin order milik akun yang lagi login.
    // Bisa difilter per status lewat query ?status= (dipakai chip filter di halaman).
    public function orders(Request $request)
    {
        $validStatuses = ['pending', 'processing', 'shipped', 'completed', 'cancelled'];
        $status = $request->query('status');
        $status = in_array($status, $validStatuses, true) ? $status : null;

        $orders = Order::where('user_id', Auth::id())
            ->when($status, fn($query) => $query->where('status', $status))
            ->with('items')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $filters = [
            'all'        => 'Semua',
            'pending'    => 'Menunggu',
            'processing' => 'Diproses',
            'shipped'    => 'Dikirim',
            'completed'  => 'Selesai',
            'cancelled'  => 'Dibatalkan',
        ];

        return view('pages.account-orders', [
            'user'         => Auth::user(),
            'orders'       => $orders,
            'statusFilter' => $status,
            'filters'      => $filters,
        ]);
    }
}