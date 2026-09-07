<div class="mobile-location-card" data-location-index="{{ $index }}">
    <div class="mobile-location-header">
        <div>
            <span class="mobile-location-number">
                TITIK STANDBY {{ $index + 1 }}
            </span>

            <h3>
                Lokasi {{ $index + 1 }}
            </h3>
        </div>

        <button type="button" class="remove-mobile-location">
            Hapus
        </button>
    </div>

    <div class="form-group">
        <label>
            Cari Lokasi
        </label>

        <div class="mobile-map-search">
            <input type="text" class="mobile-search-input" placeholder="Cari jalan, tempat, atau wilayah...">

            <button type="button" class="mobile-search-button">
                Cari
            </button>
        </div>
    </div>

    <div class="mobile-map" id="mobile-map-{{ $index }}"></div>

    <div class="form-group">
        <label>
            Alamat
        </label>

        <input type="text" class="mobile-address" name="locations[{{ $index }}][address]" value="{{ old("locations.$index.address") }}" placeholder="Alamat akan terisi setelah memilih titik di peta" readonly>

        <input type="hidden" class="mobile-latitude" name="locations[{{ $index }}][latitude]" value="{{ old("locations.$index.latitude") }}">

        <input type="hidden" class="mobile-longitude" name="locations[{{ $index }}][longitude]" value="{{ old("locations.$index.longitude") }}">
    </div>

    <div class="form-group">
        <label>
            Patokan / Keterangan Rute
        </label>

        <input type="text" name="locations[{{ $index }}][landmark]" value="{{ old("locations.$index.landmark") }}" placeholder="Contoh: Depan Alfamart, lewat Jalan Melati">
    </div>

    <div class="mobile-time-grid">
        <div class="form-group">
            <label>
                Mulai Berjualan
            </label>

            <input type="time" name="locations[{{ $index }}][start_time]" value="{{ old("locations.$index.start_time") }}">
        </div>

        <div class="form-group">
            <label>
                Selesai
            </label>

            <input type="time" name="locations[{{ $index }}][end_time]" value="{{ old("locations.$index.end_time") }}">
        </div>
    </div>
</div>