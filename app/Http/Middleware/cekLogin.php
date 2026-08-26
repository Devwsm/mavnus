<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

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
        return $next($request);
    }
}