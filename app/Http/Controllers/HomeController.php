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
    $umkms = Umkm::with('locations')->where('status', 'active')->get();

    $radarUmkms = $umkms->flatMap(function ($umkm) {
        $base = [
            'umkm_id' => $umkm->id,
            'name' => $umkm->name,
            'slug' => $umkm->slug,
            'category' => $umkm->category,
            'business_type' => $umkm->business_type ?? 'tetap',
            'description' => $umkm->description,
            'logo' => $umkm->logo ? asset('storage/' . $umkm->logo) : null,
            'cover' => $umkm->cover ? asset('storage/' . $umkm->cover) : null,
            'url' => route('umkm.show', $umkm->slug),
        ];

        if ($umkm->business_type === 'keliling') {
            return $umkm->locations->map(function ($location, $index) use ($base) {
                return array_merge($base, [
                    'location_id' => $location->id,
                    'location_number' => $index + 1,
                    'address' => $location->address,
                    'landmark' => $location->landmark,
                    'latitude' => (float) $location->latitude,
                    'longitude' => (float) $location->longitude,
                    'start_time' => $location->start_time ? substr($location->start_time, 0, 5) : null,
                    'end_time' => $location->end_time ? substr($location->end_time, 0, 5) : null,
                ]);
            });
        }

        if ($umkm->latitude === null || $umkm->longitude === null) return [];

        return [[
            ...$base,
            'location_id' => null,
            'location_number' => null,
            'address' => $umkm->address,
            'landmark' => $umkm->landmark,
            'latitude' => (float) $umkm->latitude,
            'longitude' => (float) $umkm->longitude,
            'start_time' => $umkm->opening_time ? substr($umkm->opening_time, 0, 5) : null,
            'end_time' => $umkm->closing_time ? substr($umkm->closing_time, 0, 5) : null,
        ]];
    })->values();

    return view('radar', compact('radarUmkms'));
}
}
