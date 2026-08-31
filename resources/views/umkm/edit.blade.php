@extends('layouts.app')

@section('content')

<div class="edit-page">

    <div class="edit-container">

        <a href="{{ route('dashboard') }}" class="edit-back-link">
            ← Kembali ke dashboard
        </a>

        <div class="edit-heading">
            <h1>
                Edit informasi<br>
                <em>{{ $umkm->name }}.</em>
            </h1>

            <p>
                Perbarui informasi yang ditampilkan di website publik kamu.
            </p>

        </div>


        @if($errors->any())

            <div class="edit-alert-error">

                <ul>

                    @foreach($errors->all() as $error)

                        <li>
                            {{ $error }}
                        </li>

                    @endforeach

                </ul>

            </div>

        @endif


        <form action="{{ route('umkm.update') }}" method="POST" enctype="multipart/form-data" class="edit-form">

            @csrf
            @method('PUT')


            {{-- INFORMASI DASAR --}}

            <section class="edit-card">

                <div class="edit-card-title">

                    <span>
                        INFORMASI DASAR
                    </span>

                    <h2>
                        Tentang usaha kamu
                    </h2>

                </div>


                <label class="edit-field">

                    <span>
                        Nama UMKM
                    </span>

                    <input
                        type="text"
                        name="name"
                        value="{{ old('name', $umkm->name) }}"
                        placeholder="Nama usaha kamu"
                        required
                    >

                </label>


                <label class="edit-field">

                    <span>
                        Jenis UMKM
                    </span>

                    <select name="category" required>

                        @foreach(['Kuliner','Fashion','Jasa','Kerajinan','Kecantikan','Otomotif','Lainnya'] as $category)

                            <option
                                value="{{ $category }}"
                                @selected(old('category', $umkm->category) === $category)
                            >
                                {{ $category }}
                            </option>

                        @endforeach

                    </select>

                </label>


                <label class="edit-field">

                    <span>
                        Deskripsi
                    </span>

                    <textarea
                        name="description"
                        rows="5"
                        placeholder="Ceritakan sedikit tentang usaha kamu..."
                    >{{ old('description', $umkm->description) }}</textarea>

                </label>

            </section>


            {{-- KONTAK --}}

            <section class="edit-card">

                <div class="edit-card-title">

                    <span>
                        KONTAK & LOKASI
                    </span>

                    <h2>
                        Cara pelanggan menghubungi kamu
                    </h2>

                </div>


                <div class="edit-two-col">

                    <label class="edit-field">

                        <span>
                            No. WhatsApp / Telepon
                        </span>

                        <input
                            type="text"
                            name="phone"
                            value="{{ old('phone', $umkm->phone) }}"
                            placeholder="08xxxxxxxxxx"
                        >

                    </label>

                    <label class="edit-field">
                        <span>Alamat dari Maps</span>

                        <input type="text" name="address" id="address" value="{{ old('address', $umkm->address) }}" placeholder="Pilih lokasi melalui peta di bawah" readonly>
                    </label>
                    
                    <label class="edit-field">
                        <span>Patokan <small style="font-weight:400;color:#8a8d9c;">(opsional)</small></span>

                        <input type="text" name="landmark" value="{{ old('landmark', $umkm->landmark) }}" placeholder="Contoh: Sebelah Indomaret, depan masjid">
                    </label>

                </div>
            @include('umkm.partials.map-picker', [
                'addressFieldId' => 'address',
                'latitude' => old('latitude', $umkm->latitude),
                'longitude' => old('longitude', $umkm->longitude),
            ])
            </section>


            {{-- GAMBAR --}}

            <section class="edit-card">

                <div class="edit-card-title">

                    <span>
                        TAMPILAN WEBSITE
                    </span>

                    <h2>
                        Logo & foto sampul
                    </h2>

                </div>


                <div class="edit-two-col">

                    <label class="edit-file">

                        <span>
                            Ganti Logo
                        </span>

                        <input
                            type="file"
                            name="logo"
                            accept="image/*"
                        >

                    </label>


                    <label class="edit-file">

                        <span>
                            Ganti Foto Sampul
                        </span>

                        <input
                            type="file"
                            name="cover"
                            accept="image/*"
                        >

                    </label>

                </div>

            </section>


            {{-- SUBMIT --}}

            <div class="edit-submit">

                <button
                    class="edit-submit-button"
                    type="submit"
                >
                    Simpan Perubahan
                    <span>✓</span>
                </button>

            </div>

        </form>

    </div>

</div>

@endsection