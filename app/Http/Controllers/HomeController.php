<?php

namespace App\Http\Controllers;

use App\Models\Umkm;

class HomeController extends Controller
{
    public function index()
    {
        $umkms = Umkm::where('status', 'active')
            ->latest()
            ->get();

        return view('home', compact('umkms'));
    }
}