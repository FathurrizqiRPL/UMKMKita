@extends('layouts.app')

@section('content')
<style>
    .btn-copy {
        background: #f0edff;
        color: #5848e8;
        border: none;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s ease;

    }
    @media (min-width: 1024px) {
    .dashboard-stats {
        display: grid !important;
        grid-template-columns: repeat(4, 1fr) !important;
    }
}
    .btn-copy:hover {
        background: #5848e8;
        color: #ffffff;
    }
</style>

<div class="dashboard-page">

    <div class="dashboard-container">

        {{-- SUCCESS MESSAGE --}}
        @if(session('success'))
            <div class="dashboard-alert">
                <span class="dashboard-alert-icon">✓</span>
                <div>
                    <strong>Berhasil</strong>
                    <p>{{ session('success') }}</p>
                </div>
            </div>
        @endif


        {{-- =========================================
            HEADER
        ========================================== --}}

        <section class="dashboard-hero">

            <div class="dashboard-hero-content">

                <span class="section-label">
                   
                </span>

                <h1>
                    Halo, {{ auth()->user()->name }}.
                    <br>
                    <em>Siap bikin usahamu lebih dikenal?</em>
                </h1>

                <p>
                    Kelola informasi bisnis, produk, layanan, dan website
                    UMKM kamu dari satu tempat.
                </p>

            </div>


            

        </section>


        {{-- =========================================
            BELUM PUNYA UMKM
        ========================================== --}}

        @if(!$umkm)

            <section class="dashboard-empty">

                <div class="empty-decoration">
                    <span>U</span>
                </div>

                <span class="section-label">
                    MULAI DARI SINI
                </span>

                <h2>
                    Usahamu belum punya<br>
                    <em>website sendiri.</em>
                </h2>

                <p>
                    Buat website UMKM secara gratis, tampilkan informasi
                    bisnis kamu, tambahkan produk atau layanan, lalu
                    bagikan kepada pelanggan.
                </p>

                <a
                    href="{{ route('umkm.create') }}"
                    class="dashboard-primary-button"
                >
                    Buat Website UMKM
                    <span>→</span>
                </a>

            </section>


        {{-- =========================================
            SUDAH PUNYA UMKM
        ========================================== --}}

        @else

            {{-- =====================================
                STATS
            ====================================== --}}

            <section class="dashboard-stats">

<div class="dashboard-stat">
    <div class="stat-top">
        <span>STATUS WEBSITE</span>
        <span class="{{ $umkm->status === 'active' ? 'status-online' : 'status-suspended' }}">
            <i></i>
            {{ $umkm->status === 'active' ? 'Aktif' : 'Ditangguhkan' }}
        </span>
    </div>

    <strong style="display: block; margin-top: 8px;">
        {{ $umkm->status === 'active' ? 'Aktif' : ' Ditangguhkan' }}
    </strong>
</div>

                {{-- Container 1: Status Operasional Toko --}}
<div class="dashboard-stat">
    <div class="stat-top">
        <span>STATUS OPERASIONAL</span>
        <span class="{{ $umkm->is_manual_closed ? 'status-suspended' : 'status-online' }}">
            <i></i>
            {{ $umkm->is_manual_closed ? 'Tutup Manual' : 'Online / Otomatis' }}
        </span>
    </div>

    <strong>{{ $umkm->is_manual_closed ? 'Tutup' : 'Buka' }}</strong>

    <div style="margin-top: 10px;">
        <form action="{{ route('umkm.toggle-status') }}" method="POST">
            @csrf
            <button type="submit" class="btn-copy" style="cursor: pointer;">
                {{ $umkm->is_manual_closed ? 'Buka Toko Kembali' : 'Tutup Toko Sementara' }}
            </button>
        </form>
    </div>
</div>

{{-- Container 2: Status Akun / Website --}}



                <div class="dashboard-stat">

                    <div class="stat-top">
                        <span>PRODUK / LAYANAN</span>
                    </div>

                    <strong>
                        {{ $umkm->items->count() }}
                    </strong>

                    <small>
                        Item ditampilkan
                    </small>

                </div>

        <div class="dashboard-stat">
            <div class="stat-top">
                <span>JUMLAH DISUKAI</span>
            </div>

            <strong>
                {{ $umkm->likes_count ?? 0 }}
            </strong>

            <small>
                Orang menyukai website kamu
            </small>
        </div>


                @php
                    $websiteUrl = route('umkm.show', $umkm->slug);
                @endphp

                
            </section>


            {{-- =====================================
                MAIN CONTENT
            ====================================== --}}

            <section class="dashboard-main-grid">


                {{-- =================================
                    WEBSITE PREVIEW
                ================================== --}}

                <div class="dashboard-card website-card">

                    <div class="card-header">

                        <div>

                            <span class="section-label">
                                WEBSITE KAMU
                            </span>

                            <h2>
                                {{ $umkm->name }}
                            </h2>

                        </div>

                        <a
                            href="{{ route('umkm.edit') }}"
                            class="card-edit-link"
                        >
                            Edit
                        </a>

                    </div>


                    <p class="card-description">

                        {{ $umkm->description ?: 'Tambahkan deskripsi agar pengunjung lebih mengenal usaha kamu.' }}

                    </p>


                    {{-- WEBSITE MOCKUP --}}

                    <div class="website-mockup">

                        <div class="mockup-browser">

                            <div class="browser-left">

                                <span></span>
                                <span></span>
                                <span></span>

                            </div>

                            <div class="browser-url">
                                {{ $websiteUrl }}
                            </div>

                        </div>


                        <div class="mockup-content">

                            <div class="mockup-label">
                                SELAMAT DATANG
                            </div>

                            <h3>
                                {{ $umkm->name }}
                            </h3>

                            <p>
                                {{ $umkm->category }}
                            </p>


                            <div class="mockup-products">

                                @for($i = 0; $i < 3; $i++)

                                    <div class="mockup-product">
                                        <div></div>
                                    </div>

                                @endfor

                            </div>

                        </div>

                    </div>


                    <div class="card-actions" style="display: flex; gap: 12px; align-items: center; margin-top: 20px;">

    {{-- Tombol Utama: Lihat Detail --}}
    <a
        href="{{ route('umkm.show', $umkm->slug) }}"
        target="_blank"
        style="
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            background: #5848e8;
            color: #ffffff;
            padding: 10px 18px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 14px;
            text-decoration: none;
            box-shadow: 0 4px 12px rgba(88, 72, 232, 0.2);
            transition: background 0.2s, transform 0.1s;
        "
        onmouseover="this.style.background='#4838d8'"
        onmouseout="this.style.background='#5848e8'"
    >
        Lihat Detail <span>↗</span>
    </a>

    {{-- Tombol Bahaya: Hapus Website --}}
    <a 
        href="{{ route('umkm.delete.website') }}" 
        class="delete-umkm-link"
        style="
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #fff0f3;
            color: #ff3366;
            padding: 10px 18px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 14px;
            text-decoration: none;
            border: 1px solid rgba(255, 51, 102, 0.2);
            transition: background 0.2s;
        "
        onmouseover="this.style.background='#ffe0e6'"
        onmouseout="this.style.background='#fff0f3'"
    >
        Hapus Website
    </a>

</div>
                    

                </div>



                {{-- =================================
                    PRODUK / LAYANAN
                ================================== --}}

                <div class="dashboard-card items-card">

                    <div class="card-header">

                        <div>

                            <span class="section-label">
                                KONTEN WEBSITE
                            </span>

                            <h2>
                                Produk & Layanan
                            </h2>

                        </div>

                        <a href="{{ route('umkm.edit.semua.produk') }}" class="card-edit-link">Edit</a>
                    </div>


                    <p class="card-description">
                        Tambahkan apa yang kamu jual atau layanan
                        yang kamu tawarkan.
                    </p>


                    {{-- ITEM LIST --}}

                                            <div class="dashboard-item-list">
                            @forelse($umkm->items->take(5) as $item)
                                <div class="dashboard-item">
    <div class="item-icon">
        @if($item->type === 'service')
            ✦
        @else
            □
        @endif
    </div>

    <div class="item-info">
        <span>
            {{ $item->type === 'service' ? 'LAYANAN' : 'PRODUK' }}
        </span>
        <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
            <strong>
                {{ $item->name }}
            </strong>
            {{-- Badge Status Unggulan --}}
            @if($item->is_favorite)
                <span style="background: #fff8e6; color: #b7791f; font-size: 10px; font-weight: 800; padding: 2px 6px; border-radius: 4px; border: 1px solid #feebc8;">⭐ FAVORIT</span>
            @endif
           
        </div>
    </div>

    <div class="item-price">
        {{ $item->price ? 'Rp ' . number_format($item->price, 0, ',', '.') : 'Hubungi' }}
    </div>

    <div class="item-action">
        <form
            action="{{ route('items.destroy', $item->id) }}"
            method="POST"
            class="item-delete-form"
            onsubmit="return confirm('Apakah kamu yakin ingin menghapus {{ $item->name }}?');"
        >
            @csrf
            @method('DELETE')

            <button type="submit" class="item-delete-button" title="Hapus Item">
                ✖
            </button>
        </form>
    </div>
</div>
                            @empty
                                <div class="items-empty">
                                    <div>+</div>
                                    <p>Belum ada produk atau layanan.</p>
                                    <small>Tambahkan item pertama kamu di bawah.</small>
                                </div>
                            @endforelse
                        </div>


                    {{-- QUICK ADD --}}

                    {{-- QUICK ADD / TAMBAH MENU MANUAL --}}
<div class="quick-add">
    <span class="quick-add-label">
        TAMBAH MENU / LAYANAN
    </span>

    <form
        action="{{ route('items.store') }}"
        method="POST"
        enctype="multipart/form-data"
    >
        @csrf

                            <div class="quick-form-grid">

                                <select name="type" required>

                                    <option value="product">
                                        Produk
                                    </option>

                                    <option value="service">
                                        Layanan
                                    </option>

                                </select>


            <input
                type="text"
                name="name"
                value="{{ old('name') }}"
                placeholder="Nama menu / produk / layanan"
                required
            />
        </div>

                            <div class="quick-form-grid">

                                <input
                                    type="number"
                                    name="price"
                                    placeholder="Harga"
                                />

                                <input
                                    type="text"
                                    name="duration"
                                    placeholder="Durasi (opsional)"
                                />

                            </div>


        <textarea
            name="description"
            placeholder="Deskripsi singkat menu (opsional)"
        >{{ old('description') }}</textarea>

        <!-- PILIHAN TANDA / BADGE UNGGULAN -->
        <div style="background: #f8f9fc; border: 1px solid #e7e7ef; padding: 14px 18px; border-radius: 14px; margin: 16px 0; display: inline-flex; align-items: center; gap: 10px; cursor: pointer; transition: all 0.2s ease;"
     onmouseover="this.style.borderColor='#5848e8'; this.style.background='#f3f2ff';"
     onmouseout="this.style.borderColor='#e7e7ef'; this.style.background='#f8f9fc';"
     onclick="const cb = this.querySelector('input[type=&quot;checkbox&quot;]'); cb.checked = !cb.checked;"
>
    <input 
        type="checkbox" 
        name="is_favorite" 
        value="1" 
        style="width: 18px; height: 18px; accent-color: #5848e8; cursor: pointer;"
    >
    <span style="font-weight: 700; font-size: 14px; color: #333748; user-select: none;">
        ⭐ Jadikan Menu Favorit
    </span>
</div>

                            <div class="quick-form-bottom">

                                <label class="file-input">

                                    <span>+</span>

                                    <span>
                                        Tambahkan foto
                                    </span>

                                    <input
                                        type="file"
                                        name="image"
                                        accept="image/*"
                                    />

                                </label>


                                <button
                                    type="submit"
                                    class="dashboard-secondary-button"
                                >
                                    Tambah Item
                                    <span>→</span>
                                </button>

                            </div>

                        </form>

                    </div>

                </div>

            </section>


            {{-- =====================================
                BOTTOM CTA
            ====================================== --}}

            <section class="dashboard-bottom-cta" style="display: flex; justify-content: space-between; align-items: center; gap: 30px; flex-wrap: wrap;">
    
    <div>
        <span class="section-label">
            WEBSITE KAMU SUDAH ONLINE
        </span>

        <h2>
            Cerita usahamu sudah<br>
            <em>bisa ditemukan orang.</em>
        </h2>

        <p>
            Salin dan bagikan alamat website UMKM kamu kepada pelanggan, teman, dan media sosial.
        </p>
    </div>


                <a
                    href="{{ route('umkm.show', $umkm->slug) }}"
                    target="_blank"
                    class="cta-white-button"
                >
                    Buka Website
                    <span>↗</span>
                </a>

</section>

{{-- Script Pendukung untuk Fungsi Tombol Salin --}}
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const copyBtn = document.getElementById("bottomCopyBtn");
        const urlInput = document.getElementById("bottomWebsiteUrl");

        if (copyBtn && urlInput) {
            copyBtn.addEventListener("click", () => {
                urlInput.select();
                urlInput.setSelectionRange(0, 99999); // Untuk perangkat mobile

                navigator.clipboard.writeText(urlInput.value).then(() => {
                    const originalText = copyBtn.textContent;
                    copyBtn.textContent = "Berhasil Disalin! ✓";
                    copyBtn.style.background = "#10b981"; // Warna hijau sukses

                    setTimeout(() => {
                        copyBtn.textContent = originalText;
                        copyBtn.style.background = "#5848e8";
                    }, 2000);
                }).catch(err => {
                    console.error("Gagal menyalin teks: ", err);
                });
            });
        }
    });
</script>

        @endif

    </div>

</div>

<script>
    function copyUrl(url, button) {
        navigator.clipboard.writeText(url).then(() => {
            const originalText = button.innerText;
            button.innerText = 'Tersalin! ✓';
            button.style.backgroundColor = '#10b981';
            button.style.color = 'white';

            setTimeout(() => {
                button.innerText = originalText;
                button.style.backgroundColor = '';
                button.style.color = '';
            }, 2000);
        }).catch(err => {
            alert('Gagal menyalin link.');
            console.error(err);
        });
    }
</script>

<script>
    document.querySelectorAll('.copy-url-button').forEach(function (button) {
        button.addEventListener('click', async function () {
            const originalText = button.textContent;

            try {
                await navigator.clipboard.writeText(button.dataset.url);

                button.textContent = 'Tersalin ✓';
                button.classList.add('copied');

                setTimeout(function () {
                    button.textContent = originalText;
                    button.classList.remove('copied');
                }, 1800);
            } catch (error) {
                console.error('Gagal menyalin URL:', error);
            }
        });
    });

    document.querySelectorAll('.website-url-input').forEach(function (input) {
        input.addEventListener('click', function () {
            input.select();
        });
    });

    const quickItemType = document.getElementById('quickItemType');
    const quickDurationField = document.getElementById('quickDurationField');

    function updateQuickDuration() {
        quickDurationField.hidden = quickItemType.value !== 'service';
    }

    quickItemType.addEventListener('change', updateQuickDuration);
    updateQuickDuration();

    const quickItemImage = document.getElementById('quickItemImage');
    const quickPhotoPreview = document.getElementById('quickPhotoPreview');
    const quickPhotoImage = document.getElementById('quickPhotoImage');
    const quickPhotoName = document.getElementById('quickPhotoName');
    const quickPhotoRemove = document.getElementById('quickPhotoRemove');

    let quickPhotoUrl = null;

    quickItemImage?.addEventListener('change', () => {
        const file = quickItemImage.files[0];
        if (!file) return;

        if (quickPhotoUrl) URL.revokeObjectURL(quickPhotoUrl);

        quickPhotoUrl = URL.createObjectURL(file);
        quickPhotoImage.src = quickPhotoUrl;
        quickPhotoName.textContent = file.name;
        quickPhotoPreview.hidden = false;
    });

    quickPhotoRemove?.addEventListener('click', () => {
        quickItemImage.value = '';
        quickPhotoPreview.hidden = true;
        quickPhotoImage.src = '';
        quickPhotoName.textContent = '';

        if (quickPhotoUrl) {
            URL.revokeObjectURL(quickPhotoUrl);
            quickPhotoUrl = null;
        }
    });
</script>

@endsection
