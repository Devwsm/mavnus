<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class homeController extends Controller
{
    //
    public function home()
    {
        return view('pages.home');
    }
    public function clothes()
    {
        return view('pages.clothes');
    }
    public function accessoris()
    {
        return view('pages.accessoris');
    }
}