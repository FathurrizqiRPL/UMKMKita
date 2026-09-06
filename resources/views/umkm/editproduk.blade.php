@extends('layouts.app')

@section('content')

<div class="edit-page">
    <div class="edit-container">

        <a href="{{ route('dashboard') }}" class="edit-back-link">
            ← Kembali ke dashboard
        </a>

        <div class="edit-heading">
            <span class="edit-eyebrow">PENGATURAN KONTEN</span>
            <h1>Edit Produk & Layanan</h1>
            <p>Perbarui informasi semua produk atau layanan yang sudah ada.</p>
        </div>

        @if($errors->any())
            <div class="edit-alert-error">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('umkm.update.semua.produk') }}" method="POST" enctype="multipart/form-data" class="edit-form">
            @csrf
            @method('PUT')

            @forelse($items as $item)
                <section class="edit-card">
                    <div class="edit-card-title">
                        <span>ITEM #{{ $loop->iteration }}</span>
                        <h2>{{ $item->name }}</h2>
                    </div>

                    <div class="edit-two-col">
                        <label class="edit-field">
                            <span>Tipe Item</span>
                            <select name="items[{{ $item->id }}][type]" required>
                                <option value="product" @selected(old('items.'.$item->id.'.type', $item->type) === 'product')>Produk</option>
                                <option value="service" @selected(old('items.'.$item->id.'.type', $item->type) === 'service')>Layanan</option>
                            </select>
                        </label>

                        <label class="edit-field">
                            <span>Nama Produk / Layanan</span>
                            <input 
                                type="text" 
                                name="items[{{ $item->id }}][name]" 
                                value="{{ old('items.'.$item->id.'.name', $item->name) }}" 
                                required
                            >
                        </label>
                    </div>

                    <div class="edit-two-col">
                        <label class="edit-field">
                            <span>Harga (Rp)</span>
                            <input 
                                type="number" 
                                name="items[{{ $item->id }}][price]" 
                                value="{{ old('items.'.$item->id.'.price', $item->price) }}"
                            >
                        </label>

                        <label class="edit-field">
                            <span>Durasi (Opsional)</span>
                            <input 
                                type="text" 
                                name="items[{{ $item->id }}][duration]" 
                                value="{{ old('items.'.$item->id.'.duration', $item->duration) }}"
                            >
                        </label>
                    </div>

                    <label class="edit-field">
                        <span>Deskripsi singkat</span>
                        <textarea 
                            name="items[{{ $item->id }}][description]" 
                            rows="3"
                        >{{ old('items.'.$item->id.'.description', $item->description) }}</textarea>
                    </label>

                    {{-- Pilihan Status Favorit & Terlaris --}}
                    <div style="background: #f8f9fc; border: 1px solid #e7e7ef; padding: 14px 18px; border-radius: 14px; margin: 16px 0; display: inline-flex; align-items: center; gap: 10px; cursor: pointer; transition: all 0.2s ease;"
     onmouseover="this.style.borderColor='#5848e8'; this.style.background='#f3f2ff';"
     onmouseout="this.style.borderColor='#e7e7ef'; this.style.background='#f8f9fc';"
     onclick="const cb = this.querySelector('input[type=&quot;checkbox&quot;]'); cb.checked = !cb.checked;"
>
    <input 
        type="checkbox" 
        name="items[{{ $item->id }}][is_favorite]" 
        value="1" 
        @checked(old('items.'.$item->id.'.is_favorite', $item->is_favorite)) 
        style="width: 18px; height: 18px; accent-color: #5848e8; cursor: pointer;"
        onclick="event.stopPropagation();"
    >
    <span style="font-weight: 700; font-size: 14px; color: #333748; user-select: none;">
        ⭐ Jadikan Menu Favorit
    </span>
</div>

                    <label class="edit-file">
                        <span>Ganti Foto (Opsional)</span>
                        <input 
                            type="file" 
                            name="items[{{ $item->id }}][image]" 
                            accept="image/*"
                        >
                    </label>
                </section>
            @empty
                <div class="edit-card">
                    <p style="text-align: center; padding: 20px;">Belum ada produk atau layanan untuk diedit.</p>
                </div>
            @endforelse

            @if($items->count() > 0)
                <div class="edit-submit">
                    <button class="edit-submit-button" type="submit">
                        Simpan Semua Perubahan
                        <span>✓</span>
                    </button>
                </div>
            @endif

        </form>
    </div>
</div>

@endsection