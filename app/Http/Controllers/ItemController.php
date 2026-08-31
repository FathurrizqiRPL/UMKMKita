<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
    // ==========================================
    // KODINGAN ASLIMU (TIDAK DIUBAH)
    // ==========================================
    public function store(Request $request)
    {
        $umkm = $request->user()->umkm()->firstOrFail();

        $data = $request->validate([
            'type' => ['required', 'in:product,service'],
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'duration' => ['nullable', 'string', 'max:50'],
            'image' => ['nullable', 'image', 'max:4096'],
        ]);

        $data['umkm_id'] = $umkm->id;

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('items', 'public');
        }

        Item::create($data);

        return back()->with('success', 'Produk/layanan berhasil ditambahkan.');
    }

    public function destroy(Request $request, Item $item)
    {
        abort_unless($item->umkm_id === $request->user()->umkm?->id, 403);

        if ($item->image) {
            Storage::disk('public')->delete($item->image);
        }

        $item->delete();

        return back()->with('success', 'Item berhasil dihapus.');
    }


    // ==========================================
    // TAMBAHAN BARU: UNTUK EDIT & UPDATE
    // ==========================================
    
    // Menampilkan halaman edit semua produk
public function editSemua(Request $request)
{
    $umkm = $request->user()->umkm()->firstOrFail();
    $items = $umkm->items()->get(); // Ambil semua item

    return view('umkm.editproduk', compact('umkm', 'items'));
}

// Memproses penyimpanan massal (Bulk Update)
public function updateSemua(Request $request)
{
    $umkm = $request->user()->umkm()->firstOrFail();
    
    // Validasi data array
    $request->validate([
        'items' => 'required|array',
        'items.*.name' => 'required|string|max:100',
        'items.*.type' => 'required|in:product,service',
        'items.*.price' => 'nullable|numeric|min:0',
        'items.*.duration' => 'nullable|string|max:50',
        'items.*.description' => 'nullable|string|max:500',
        'items.*.image' => 'nullable|image|max:4096',
    ]);

    // Looping data yang dikirim dari form
    foreach ($request->items as $id => $data) {
        // Cari item berdasarkan ID dan pastikan milik UMKM yang login
        $item = Item::where('id', $id)->where('umkm_id', $umkm->id)->first();
        
        if ($item) {
            // Jika ada file gambar baru di-upload untuk item ini
            if ($request->hasFile("items.{$id}.image")) {
                if ($item->image) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($item->image);
                }
                $data['image'] = $request->file("items.{$id}.image")->store('items', 'public');
            }
            
            $item->update($data);
        }
    }

    return redirect()->route('dashboard')->with('success', 'Semua produk berhasil diperbarui.');
}
    public function update(Request $request, Item $item)
    {
        // Keamanan: Cek apakah item ini benar milik user yang sedang login
        abort_unless($item->umkm_id === $request->user()->umkm?->id, 403);

        // Validasi disamakan dengan fungsi store milikmu
        $data = $request->validate([
            'type' => ['required', 'in:product,service'],
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'duration' => ['nullable', 'string', 'max:50'],
            'image' => ['nullable', 'image', 'max:4096'],
        ]);

        // Jika user upload gambar baru saat edit
        if ($request->hasFile('image')) {
            // Hapus gambar lama dulu dari storage (biar storage nggak penuh)
            if ($item->image) {
                Storage::disk('public')->delete($item->image);
            }
            // Simpan gambar baru
            $data['image'] = $request->file('image')->store('items', 'public');
        }

        // Update data ke database
        $item->update($data);

        // Kembali ke dashboard dengan pesan sukses
        return redirect()->route('dashboard')->with('success', 'Produk berhasil diperbarui.');
    }
}