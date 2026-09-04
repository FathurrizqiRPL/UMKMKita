@extends('layouts.app')

@section('content')

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
                    DASHBOARD PEMILIK UMKM
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


            @if($umkm)

                <div class="dashboard-hero-action">

                    <a
                        href="{{ route('umkm.show', $umkm->slug) }}"
                        target="_blank"
                        class="dashboard-primary-button"
                    >
                        Lihat Website
                        <span>↗</span>
                    </a>

                </div>

            @endif

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

                        <span class="status-online">
                            <i></i>
                            Online
                        </span>
                    </div>

                    <strong>Aktif</strong>

                    <small>
                        Website {{ $umkm->name }}
                    </small>

                </div>


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
                        <span>ALAMAT WEBSITE</span>
                    </div>

                    <strong class="stat-slug">
                        /{{ $umkm->slug }}
                    </strong>

                    <small>
                        Alamat website kamu
                    </small>

                </div>

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
                                umkmkita.local/{{ $umkm->slug }}
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


                    <div class="card-actions">

                        <a
                            href="{{ route('umkm.show', $umkm->slug) }}"
                            target="_blank"
                        >
                            Lihat Detail
                        </a>

                        <a href="{{ route('umkm.delete.website') }}" class="delete-umkm-link">
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
                                        <strong>
                                            {{ $item->name }}
                                        </strong>
                                    </div>

                                    <div class="item-price">
                                        {{ $item->price ? 'Rp ' . number_format($item->price, 0, ',', '.') : 'Hubungi' }}
                                    </div>

                                    <div class="item-action">
                                        <form action="{{ route('items.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah kamu yakin ingin menghapus {{ $item->name }}?');" style="display: inline-block; margin-left: 15px;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Hapus Item" style="background: transparent; border: none; color: #a1a1aa; cursor: pointer; font-size: 16px; font-weight: bold;">
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

                    <div class="quick-add">
                        <span class="quick-add-label">
                            TAMBAH ITEM
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
                                    placeholder="Nama produk / layanan"
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
                                placeholder="Deskripsi singkat (opsional)"
                            ></textarea>


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

            <section class="dashboard-bottom-cta">

                <div>

                    <span class="section-label">
                        WEBSITE KAMU SUDAH ONLINE
                    </span>

                    <h2>
                        Cerita usahamu sudah<br>
                        <em>bisa ditemukan orang.</em>
                    </h2>

                    <p>
                        Bagikan website UMKM kamu kepada pelanggan,
                        teman, dan media sosial.
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

        @endif

    </div>

</div>

@endsection