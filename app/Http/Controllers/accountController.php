<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class accountController extends Controller
{
    // Halaman akun customer - cuma bisa diakses kalau udah login (middleware 'auth' bawaan Laravel)
    public function index()
    {
        return view('pages.account', [
            'user' => Auth::user(),
        ]);
    }
}