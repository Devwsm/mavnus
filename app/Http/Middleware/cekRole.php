<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class cekRole
{
    /**
     * Batasi route ke role tertentu aja. Dipasang SETELAH 'cekLogin' di
     * route group, jadi pas method ini jalan, staff udah pasti login.
     *
     * Pemakaian: ->middleware('cekLogin', 'role:owner,admin_produk')
     *
     * Kalau role staff yang login gak ada di daftar $roles, dia dilempar
     * balik ke landing dashboard dia sendiri (bukan halaman 403 polos),
     * biar gak bingung — dashboardController yang nanti nentuin landing
     * mana yang cocok buat role dia.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $role = $request->session()->get('role');

        if (!in_array($role, $roles, true)) {
            return redirect()->route('dashboard')
                ->with('error', 'Kamu gak punya akses ke halaman itu.');
        }

        return $next($request);
    }
}