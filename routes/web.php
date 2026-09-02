<?php
use App\Http\Controllers\AdminController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UmkmController;
use App\Http\Controllers\ItemController;
use Illuminate\Support\Facades\Route;

// ... (route milikmu yang lain di atasnya) ...

Route::get('/preview-template/{name}', function ($name) {
    // Siapkan data contoh (dummy) agar template bisa tampil tanpa error
    $dummyUmkm = (object) [
        'name' => 'Kedai Senja (Contoh)',
        'category' => 'Makanan & Minuman',
        'description' => 'Ini adalah contoh deskripsi usaha Kedai Senja untuk melihat hasil preview desain template.',
        'phone' => '081234567890',
        'cover' => null,
        'items' => collect([]) // Collection kosong supaya @forelse di template tidak error
    ];

    // Validasi template yang diizinkan
    $allowedTemplates = ['template1', 'template2'];

    if (in_array($name, $allowedTemplates)) {
        // Mengarah ke resources/views/templates/template1.blade.php dst
        return view("templates.{$name}", ['umkm' => $dummyUmkm]);
    }

    abort(404, 'Template tidak ditemukan');
})->name('preview.template');

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/radar-umkm', [HomeController::class, 'radar'])->name('radar');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [UmkmController::class, 'dashboard'])->name('dashboard');

    Route::get('/buat-website', [UmkmController::class, 'create'])->name('umkm.create');
    Route::post('/buat-website', [UmkmController::class, 'store'])->name('umkm.store');

    Route::get('/umkm/edit', [UmkmController::class, 'edit'])->name('umkm.edit');
    Route::put('/umkm/edit', [UmkmController::class, 'update'])->name('umkm.update');

    Route::post('/umkm/items', [ItemController::class, 'store'])->name('items.store');
    Route::delete('/umkm/items/{item}', [ItemController::class, 'destroy'])->name('items.destroy');

    // Menampilkan halaman konfirmasi hapus website
Route::get('/umkm/hapus-website', [App\Http\Controllers\UmkmController::class, 'delete'])->name('umkm.delete.website');

// Memproses penghapusan website secara permanen
Route::delete('/umkm/hapus-website', [App\Http\Controllers\UmkmController::class, 'destroy'])->name('umkm.destroy.website');

// Tampilkan form edit semua produk
Route::get('/umkm/produk/edit-semua', [App\Http\Controllers\ItemController::class, 'editSemua'])->name('umkm.edit.semua.produk');

// Proses simpan perubahannya
Route::put('/umkm/produk/edit-semua', [App\Http\Controllers\ItemController::class, 'updateSemua'])->name('umkm.update.semua.produk');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');

    Route::get('/umkms', [AdminController::class, 'umkms'])->name('umkms');
    Route::patch('/umkms/{umkm}/status', [AdminController::class, 'updateUmkmStatus'])->name('umkms.status');

    Route::get('/users', [AdminController::class, 'users'])->name('users');
});


Route::get('/umkm/{slug}', [UmkmController::class, 'show'])->name('umkm.show');

require __DIR__.'/auth.php';
