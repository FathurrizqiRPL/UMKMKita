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

        {{-- Form action diarahkan ke rute update massal --}}
        <form action="{{ route('umkm.update.semua.produk') }}" method="POST" enctype="multipart/form-data" class="edit-form">
            @csrf
            @method('PUT')

            {{-- Looping semua produk/layanan --}}
            @forelse($items as $item)
                <section class="edit-card">
                    <div class="edit-card-title">
                        <span>ITEM #{{ $loop->iteration }}</span>
                        <h2>{{ $item->name }}</h2>
                    </div>

                    <div class="edit-two-col">
                        <label class="edit-field">
                            <span>Tipe Item</span>
                            {{-- Perhatikan penamaan name="items[id][field]" --}}
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