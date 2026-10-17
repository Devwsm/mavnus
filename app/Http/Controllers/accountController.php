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

        // Akun yang daftar/login lewat Google gak punya password yang
        // mereka tau (diisi random di belakang layar, lihat authController),
        // jadi konfirmasi "password saat ini" cuma diwajibkan buat akun
        // yang emang daftar manual pakai email & password.
        $requiresPasswordConfirmation = ! $user->google_id;

        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'email'            => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone'            => ['nullable', 'regex:/^[0-9]{9,15}$/'],
            'address'          => 'nullable|string|max:1000',
            'current_password' => $requiresPasswordConfirmation ? 'required|string' : 'nullable|string',
        ], [
            'name.required'             => __('account.validation.name_required'),
            'email.required'            => __('account.validation.email_required'),
            'email.email'               => __('account.validation.email_invalid'),
            'email.unique'               => __('account.validation.email_taken'),
            'phone.regex'               => __('account.validation.phone_regex'),
            'current_password.required' => __('account.validation.current_password_required'),
        ]);

        // Konfirmasi password saat ini - biar perubahan data (apalagi ganti email)
        // gak bisa dilakuin sembarangan kalau sesi ke-hijack orang lain
        if ($requiresPasswordConfirmation && ! Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => __('account.msg.wrong_password')])->withInput();
        }

        $user->update([
            'name'    => $validated['name'],
            'email'   => $validated['email'],
            'phone'   => $validated['phone'],
            'address' => $validated['address'],
        ]);

        // Balik ke halaman overview (bukan back(), biar konsisten abis simpan
        // selalu mendarat di halaman profil, bukan nyangkut di form edit)
        return redirect()->route('account')->with('success', __('account.msg.profile_updated'));
    }

    // Hapus akun sendiri. Orders yang udah dibuat TETAP disimpan (kolom
    // user_id otomatis di-null-in lewat nullOnDelete di migration orders),
    // jadi data buat rekap/laporan staff gak ikut ilang - cuma udah gak
    // nyantol ke akun mana pun lagi (jadi mirip kayak pesanan guest).
    public function destroy(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Akun Google gak punya password yang mereka tau (lihat catatan
        // yang sama di update()), jadi gak perlu konfirmasi password.
        if (! $user->google_id) {
            $validated = $request->validate([
                'password' => 'required|string',
            ], [
                'password.required' => __('account.msg.delete_password_required'),
            ]);

            if (! Hash::check($validated['password'], $user->password)) {
                return back()->withErrors(['password' => __('account.msg.wrong_password')]);
            }
        }

        Auth::logout();
        $user->delete();
        $request->session()->regenerate();

        return redirect()->route('home')->with('success', __('account.msg.deleted'));
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
            'all'        => __('account.filter_all'),
            'pending'    => __('order.status.pending'),
            'processing' => __('order.status.processing'),
            'shipped'    => __('order.status.shipped'),
            'completed'  => __('order.status.completed'),
            'cancelled'  => __('order.status.cancelled'),
        ];

        return view('pages.account-orders', [
            'user'         => Auth::user(),
            'orders'       => $orders,
            'statusFilter' => $status,
            'filters'      => $filters,
        ]);
    }

    // Detail satu pesanan - cuma bisa diliat pemilik akunnya sendiri
    // (beda dari orderController::success yang juga ngizinin guest lewat link).
    public function orderDetail(Order $order)
    {
        if ((int) $order->user_id !== (int) Auth::id()) {
            abort(403);
        }

        $order->load('items');

        return view('pages.account-order-detail', [
            'user'  => Auth::user(),
            'order' => $order,
        ]);
    }
}