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

    public function radar()
    {
        $umkms = Umkm::where('status', 'active')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->select([
                'id',
                'name',
                'slug',
                'category',
                'description',
                'address',
                'landmark',
                'logo',
                'cover',
                'latitude',
                'longitude',
            ])
            ->get();

        return view('radar', compact('umkms'));
    }
}
