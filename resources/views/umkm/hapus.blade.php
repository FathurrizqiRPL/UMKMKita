@extends('layouts.app')

@section('content')

<div class="edit-page">

    <div class="edit-container">

        <a href="{{ route('dashboard') }}" class="edit-back-link">
            ← Batal & Kembali ke Dashboard
        </a>

        <div class="edit-heading">

            <span class="delete-eyebrow">
                DANGER ZONE
            </span>

            <h1>
                Hapus Website<br>
                <em>{{ $umkm->name }}.</em>
            </h1>

            <p>
                Tindakan ini akan menghapus website UMKM dan seluruh data yang ada di dalamnya secara permanen.
            </p>

        </div>

        <section class="delete-card">

            <div class="delete-warning-icon">
                !
            </div>

            <div class="delete-content">

                <span class="delete-label">
                    PERINGATAN
                </span>

                <h2>
                    Website ini akan dihapus permanen.
                </h2>

                <p>
                    Semua data dari <strong>{{ $umkm->name }}</strong>, termasuk profil usaha, logo, foto sampul, produk, layanan, dan foto item akan ikut dihapus.
                </p>

                <p class="delete-warning-text">
                    Tindakan ini tidak dapat dibatalkan.
                </p>

            </div>

        </section>

        <div class="delete-actions">

            <a href="{{ route('dashboard') }}" class="delete-cancel-button">
                Batal
            </a>

            <form action="{{ route('umkm.destroy.website') }}" method="POST" onsubmit="return confirm('Apakah kamu benar-benar yakin ingin menghapus website UMKM ini secara permanen?');">
                @csrf
                @method('DELETE')

                <button type="submit" class="delete-confirm-button">
                    Hapus Website Saya
                    <span>→</span>
                </button>
            </form>

        </div>

    </div>

</div>

@endsection