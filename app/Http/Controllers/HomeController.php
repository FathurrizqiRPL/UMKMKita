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
        ->get();

    $radarUmkms = $umkms->map(function ($umkm) {
        return [
            'name' => $umkm->name,
            'slug' => $umkm->slug,
            'category' => $umkm->category,
            'description' => $umkm->description,
            'address' => $umkm->address,
            'landmark' => $umkm->landmark,
            'logo' => $umkm->logo ? asset('storage/' . $umkm->logo) : null,
            'cover' => $umkm->cover ? asset('storage/' . $umkm->cover) : null,
            'latitude' => (float) $umkm->latitude,
            'longitude' => (float) $umkm->longitude,
            'url' => route('umkm.show', $umkm->slug),
        ];
    })->values();

    return view('radar', compact('radarUmkms'));
}
}
