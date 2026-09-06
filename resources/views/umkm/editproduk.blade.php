@extends('layouts.app')

@section('content')

<div class="edit-page">
    <div class="edit-container">

        <a href="{{ route('dashboard') }}" class="edit-back-link">
            Kembali ke dashboard
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
                <section class="product-edit-card" data-product-card>
                    <button type="button" class="product-edit-summary" data-product-toggle>
                        <div class="product-edit-preview">
                            @if($item->image)
                                <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}">
                            @else
                                <div class="product-image-placeholder">
                                    <span>◫</span>
                                    <small>Belum ada foto</small>
                                </div>
                            @endif
                        </div>

                        <div class="product-edit-info">
                            <span class="product-edit-number">ITEM #{{ $loop->iteration }}</span>
                            <h2>{{ $item->name }}</h2>

                            <div class="product-edit-meta">
                                <span>{{ $item->type === 'service' ? 'Layanan' : 'Produk' }}</span>

                                @if($item->price !== null)
                                    <span>Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                                @endif

                                @if($item->type === 'service' && $item->duration)
                                    <span>{{ $item->duration }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="product-edit-action">
                            <span class="product-edit-label">Edit</span>
                            <span class="product-edit-chevron"></span>
                        </div>
                    </button>

                    <div class="product-edit-body" data-product-body>
                        <div class="product-edit-body-inner">

                            <div class="product-current-image">
                                <span class="product-current-label">Preview Foto</span>

                                <div class="product-current-preview">
                                    @if($item->image)
                                        <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}">
                                    @else
                                        <div class="product-current-placeholder">
                                            <span>◫</span>
                                            <small>Belum ada gambar</small>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="edit-two-col">
                                <label class="edit-field">
                                    <span>Tipe Item</span>

                                    <select name="items[{{ $item->id }}][type]" class="item-type-select" required>
                                        <option value="product" @selected(old('items.'.$item->id.'.type', $item->type) === 'product')>
                                            Produk
                                        </option>

                                        <option value="service" @selected(old('items.'.$item->id.'.type', $item->type) === 'service')>
                                            Layanan
                                        </option>
                                    </select>
                                </label>

                                <label class="edit-field">
                                    <span>Nama Produk / Layanan</span>

                                    <input type="text"
                                        name="items[{{ $item->id }}][name]"
                                        value="{{ old('items.'.$item->id.'.name', $item->name) }}"
                                        required>
                                </label>
                            </div>

                            <div class="edit-two-col">
                                <label class="edit-field">
                                    <span>Harga (Rp)</span>

                                    <input type="number"
                                        name="items[{{ $item->id }}][price]"
                                        value="{{ old('items.'.$item->id.'.price', $item->price) }}">
                                </label>

                                <label class="edit-field item-duration-field">
                                    <span>Durasi (Opsional)</span>

                                    <input type="text"
                                        name="items[{{ $item->id }}][duration]"
                                        value="{{ old('items.'.$item->id.'.duration', $item->duration) }}"
                                        placeholder="Contoh: 60 menit">
                                </label>
                            </div>

                            <label class="edit-field">
                                <span>Deskripsi singkat</span>

                                <textarea name="items[{{ $item->id }}][description]" rows="3">{{ old('items.'.$item->id.'.description', $item->description) }}</textarea>
                            </label>

                            <label class="favorite-toggle">
                                <input type="checkbox"
                                    name="items[{{ $item->id }}][is_favorite]"
                                    value="1"
                                    @checked(old('items.'.$item->id.'.is_favorite', $item->is_favorite))>

                                <span class="favorite-toggle-box"></span>

                                <span class="favorite-toggle-text">
                                    <strong>Jadikan Menu Favorit</strong>
                                    <small>Tandai produk atau layanan ini sebagai pilihan utama.</small>
                                </span>
                            </label>

                            <label class="edit-file">
                                <span>Ganti Foto (Opsional)</span>

                                <input type="file"
                                    name="items[{{ $item->id }}][image]"
                                    accept="image/*"
                                    data-image-input>
                            </label>

                        </div>
                    </div>
                </section>
            @empty
                <div class="edit-card">
                    <p class="edit-empty">Belum ada produk atau layanan untuk diedit.</p>
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

<script>
document.querySelectorAll('[data-product-card]').forEach(function (card) {
    const toggle = card.querySelector('[data-product-toggle]');
    const body = card.querySelector('[data-product-body]');
    const imageInput = card.querySelector('[data-image-input]');
    const typeSelect = card.querySelector('.item-type-select');
    const durationField = card.querySelector('.item-duration-field');
    const durationInput = durationField?.querySelector('input');
    const summaryPreview = card.querySelector('.product-edit-preview');
    const currentPreview = card.querySelector('.product-current-preview');

    function resizeCard() {
        if (card.classList.contains('open')) {
            body.style.maxHeight = body.scrollHeight + 'px';
        }
    }

    function updateDurationField() {
        const isService = typeSelect.value === 'service';

        durationField.style.display = isService ? '' : 'none';

        if (!isService && durationInput) {
            durationInput.value = '';
        }

        resizeCard();
    }

    toggle.addEventListener('click', function () {
        const open = card.classList.toggle('open');
        body.style.maxHeight = open ? body.scrollHeight + 'px' : '0';
    });

    typeSelect.addEventListener('change', updateDurationField);
    updateDurationField();

    imageInput.addEventListener('change', function (event) {
        const file = event.target.files[0];
        if (!file) return;

        const imageUrl = URL.createObjectURL(file);

        summaryPreview.innerHTML = `<img src="${imageUrl}" alt="Preview foto baru">`;
        currentPreview.innerHTML = `<img src="${imageUrl}" alt="Preview foto baru">`;

        resizeCard();
    });
});
</script>

@endsection
