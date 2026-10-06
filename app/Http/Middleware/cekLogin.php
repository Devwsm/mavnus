<?php

namespace App\Http\Middleware;

use App\Models\account;
use Closure;
use Illuminate\Http\Request;

class cekLogin
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->session()->get('login')) {
            // Simpan tujuan awal di key sendiri ('staff_intended_url'), bukan
            // 'url.intended' bawaan Laravel — key itu punya flow auth customer.
            $request->session()->put('staff_intended_url', $request->fullUrl());
            return redirect()->route('crew.login');
        }

        // Cek ulang akunnya di database tiap request. Dulu role & status disimpan di session
        // saat login dan dipercaya sampai session habis, jadi staf yang SUDAH dinonaktifkan
        // (resign) atau diturunkan rolenya tetap punya akses penuh sampai logout sendiri.
        $staff = account::where('username', $request->session()->get('user'))->first();

        if (! $staff || ! $staff->is_active) {
            $request->session()->forget(['login', 'user', 'role', 'name']);

            return redirect()->route('crew.login')
                ->withErrors(['login' => 'Sesi kamu sudah tidak berlaku. Silakan login ulang.']);
        }

        // Role selalu ikut data terbaru di database (cekRole membaca dari session)
        $request->session()->put('role', $staff->role);
        $request->session()->put('name', $staff->name);

        return $next($request);
    }
}