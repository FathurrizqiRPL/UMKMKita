@extends('layouts.app')

@section('content')

<div class="edit-page">
    <div class="edit-container">

        <a href="{{ route('dashboard') }}" class="edit-back-link">
            ← Batal & Kembali ke dashboard
        </a>

        <div class="edit-heading">
            <span class="edit-eyebrow" style="color: #dc2626;">DANGER ZONE</span>
            <h1>
                Hapus Website<br>
                <em>{{ $umkm->name }}.</em>
            </h1>
            <p>
                Halaman ini digunakan untuk menutup dan menghapus website publik kamu selamanya.
            </p>
        </div>

        <form action="{{ route('umkm.destroy.website') }}" method="POST" class="edit-form" onsubmit="return confirm('Apakah kamu benar-benar yakin? Tindakan ini akan menghapus semua data produk dan tidak bisa dikembalikan.');">
            @csrf
            @method('DELETE')

            <section class="edit-card" style="border: 2px solid #fecaca; background-color: #fef2f2;">
                <div class="edit-card-title">
                    <span style="color: #991b1b;">PERINGATAN KERAS</span>
                    <h2 style="color: #7f1d1d;">Konfirmasi Penghapusan</h2>
                </div>
                
                <p style="color: #991b1b; margin-bottom: 20px; line-height: 1.5;">
                    Dengan menekan tombol di bawah, kamu akan menghapus website <strong>{{ $umkm->name }}</strong> secara permanen. Semua data termasuk deskripsi, logo, foto sampul, dan daftar produk yang ada di dalamnya akan ikut terhapus dari sistem.
                </p>

                <div class="edit-submit" style="margin-top: 30px; padding-top: 0; border: none;">
                    <button class="edit-submit-button" type="submit" style="background-color: #dc2626; color: white;">
                        Ya, Hapus Website Saya
                        <span>🗑️</span>
                    </button>
                </div>
            </section>
        </form>

    </div>
</div>

@endsection