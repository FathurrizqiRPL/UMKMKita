<?php

namespace App\Http\Controllers;

use App\Models\Umkm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UmkmController extends Controller
{
    public function dashboard(Request $request)
    {
        $umkm = $request->user()->umkm()->with('items')->first();

        return view('dashboard', compact('umkm'));
    }

    public function create(Request $request)
    {
        abort_if($request->user()->umkm()->exists(), 403, 'Kamu sudah memiliki website UMKM.');

        return view('umkm.create');
    }

    public function store(Request $request)
    {
        abort_if($request->user()->umkm()->exists(), 403, 'Kamu sudah memiliki website UMKM.');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'category' => ['required', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:1000'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:255'],
            'landmark' => ['nullable', 'string', 'max:255'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'cover' => ['nullable', 'image', 'max:4096'],
        ]);

        $data['user_id'] = $request->user()->id;
        $data['slug'] = $this->uniqueSlug($data['name']);

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('umkm', 'public');
        }

        if ($request->hasFile('cover')) {
            $data['cover'] = $request->file('cover')->store('umkm', 'public');
        }

        $umkm = Umkm::create($data);

        return redirect()
            ->route('dashboard')
            ->with('success', 'Website UMKM berhasil dibuat.');
    }

    public function edit(Request $request)
    {
        $umkm = $request->user()->umkm()->with('items')->firstOrFail();

        return view('umkm.edit', compact('umkm'));
    }

// Fungsi untuk menampilkan view
public function delete(Request $request)
{
    $umkm = $request->user()->umkm()->firstOrFail();
    
    // Kita arahkan ke file baru bernama hapus.blade.php
    return view('umkm.hapus', compact('umkm'));
}

// Fungsi untuk mengeksekusi penghapusan
public function destroy(Request $request)
{
    $umkm = $request->user()->umkm()->firstOrFail();

    // Opsional: Hapus logo/cover dari storage jika ada
    if ($umkm->logo) {
        \Illuminate\Support\Facades\Storage::disk('public')->delete($umkm->logo);
    }
    if ($umkm->cover) {
        \Illuminate\Support\Facades\Storage::disk('public')->delete($umkm->cover);
    }

    // Hapus data dari database
    $umkm->delete();

    // Kembalikan user ke halaman utama/home setelah website dihapus
    return redirect('/')->with('success', 'Website UMKM berhasil dihapus.');
}

    public function update(Request $request)
    {
        $umkm = $request->user()->umkm()->firstOrFail();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'category' => ['required', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:1000'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:255'],
            'landmark' => ['nullable', 'string', 'max:255'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'cover' => ['nullable', 'image', 'max:4096'],
        ]);

        if ($data['name'] !== $umkm->name) {
            $data['slug'] = $this->uniqueSlug($data['name'], $umkm->id);
        }

        if ($request->hasFile('logo')) {
            if ($umkm->logo) {
                Storage::disk('public')->delete($umkm->logo);
            }
            $data['logo'] = $request->file('logo')->store('umkm', 'public');
        }

        if ($request->hasFile('cover')) {
            if ($umkm->cover) {
                Storage::disk('public')->delete($umkm->cover);
            }
            $data['cover'] = $request->file('cover')->store('umkm', 'public');
        }

        $umkm->update($data);

        return redirect()
            ->route('dashboard')
            ->with('success', 'Profil UMKM berhasil diperbarui.');
    }

    public function show(string $slug)
    {
        $umkm = Umkm::with('items')
            ->where('slug', $slug)
            ->firstOrFail();

        return view('umkm.show', compact('umkm'));
    }

    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'umkm';
        $slug = $base;
        $number = 2;

        while (
            Umkm::where('slug', $slug)
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base . '-' . $number++;
        }

        return $slug;
    }
}
