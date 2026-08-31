<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $umkm->name }} — UMKMKita</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
    <style>
        *{box-sizing:border-box}body{margin:0;background:#fafaff;color:#15182b;font-family:"DM Sans",sans-serif}
        .site{max-width:1180px;margin:auto;padding:28px 24px 80px}
        .nav{display:flex;align-items:center;justify-content:space-between;padding:12px 0 32px}
        .brand{font-family:"Plus Jakarta Sans";font-weight:800;font-size:24px}.brand b{color:#5848e8}
        .nav a{color:#5848e8;text-decoration:none;font-weight:700}
        .hero{min-height:420px;border:1px solid #e6e6f0;border-radius:30px;padding:60px;display:flex;align-items:end;overflow:hidden;background:#f0edff url('{{ $umkm->cover ? asset('storage/'.$umkm->cover) : '' }}') center/cover no-repeat}
        .hero-card{background:rgba(255,255,255,.94);backdrop-filter:blur(10px);max-width:650px;padding:38px;border-radius:24px}
        .eyebrow{font-size:11px;letter-spacing:.18em;font-weight:800;color:#5848e8}.hero h1{font-family:"Plus Jakarta Sans";font-size:clamp(42px,7vw,78px);line-height:.98;margin:15px 0}.hero p{font-size:18px;line-height:1.7;color:#666b80}
        .items{padding-top:70px}.items h2{font-family:"Plus Jakarta Sans";font-size:38px;margin:0 0 28px}.grid{display:grid;grid-template-columns:repeat(3,1fr);gap:20px}
        .card{background:white;border:1px solid #e7e7ef;border-radius:22px;overflow:hidden}.card img{width:100%;height:210px;object-fit:cover;background:#eee}.card-body{padding:24px}.tag{font-size:11px;color:#5848e8;font-weight:800;letter-spacing:.12em}.card h3{font-family:"Plus Jakarta Sans";font-size:20px;margin:10px 0}.card p{color:#73778a;line-height:1.6}.price{font-weight:800;font-size:18px}
        .contact{margin-top:70px;padding:40px;border-radius:26px;background:#17192b;color:white;display:flex;justify-content:space-between;align-items:center;gap:30px}.contact h2{font-family:"Plus Jakarta Sans";font-size:32px;margin:0}.contact p{color:#bfc2d0}.contact a{display:inline-block;background:#5848e8;color:white;padding:14px 20px;border-radius:12px;text-decoration:none;font-weight:700}
        .umkm-address-link {
            display: inline-flex;
            align-items: flex-start;
            gap: 8px;
            color: #5848e8;
            font-weight: 700;
            line-height: 1.6;
            text-decoration: none;
        }

        .umkm-address-link:hover {
            text-decoration: underline;
        }

        .umkm-landmark {
            margin: 6px 0 18px;
            color: #777b8d;
            font-size: 14px;
        }
        @media(max-width:800px){.hero{padding:25px;min-height:500px}.hero-card{padding:25px}.grid{grid-template-columns:1fr}.contact{display:block}.contact a{margin-top:15px}}
    </style>
</head>
<body>
<div class="site">
    <nav class="nav">
        <div class="brand">UMKM<span>Kita</span></div>
        <a href="{{ route('home') }}">Dibuat dengan UMKMKita →</a>
    </nav>

    <section class="hero">
        <div class="hero-card">
            <span class="eyebrow">{{ strtoupper($umkm->category) }}</span>
            <h1>{{ $umkm->name }}</h1>
            <p>{{ $umkm->description ?: 'Selamat datang di website resmi usaha kami.' }}</p>
        </div>
    </section>

    <section class="items">
        <span class="eyebrow">YANG KAMI TAWARKAN</span>
        <h2>{{ $umkm->category === 'Jasa' || $umkm->category === 'Kecantikan' || $umkm->category === 'Otomotif' ? 'Layanan Kami' : 'Produk Kami' }}</h2>

        <div class="grid">
            @forelse($umkm->items as $item)
                <article class="card">
                    @if($item->image)
                        <img src="{{ asset('storage/'.$item->image) }}" alt="{{ $item->name }}">
                    @else
                        <div style="height:210px;background:#f0edff;"></div>
                    @endif
                    <div class="card-body">
                        <span class="tag">{{ $item->type === 'service' ? 'LAYANAN' : 'PRODUK' }}</span>
                        <h3>{{ $item->name }}</h3>
                        <p>{{ $item->description }}</p>
                        @if($item->price !== null)
                            <div class="price">Rp {{ number_format($item->price, 0, ',', '.') }}</div>
                        @endif
                        @if($item->duration)
                            <p>Durasi: {{ $item->duration }}</p>
                        @endif
                    </div>
                </article>
            @empty
                <p>Belum ada produk atau layanan yang ditambahkan.</p>
            @endforelse
        </div>
    </section>

            @if($umkm->latitude && $umkm->longitude)
                    <section style="margin-top:70px;">
                        <span class="eyebrow">LOKASI</span>
                        <h2 style="font-family:'Plus Jakarta Sans';font-size:32px;margin:10px 0 20px;">Kunjungi Kami</h2>
                        @if($umkm->address)
                <a href="https://www.google.com/maps/search/?api=1&query={{ $umkm->latitude }},{{ $umkm->longitude }}" target="_blank" rel="noopener noreferrer" class="umkm-address-link">
                    <span>📍</span>
                    <span>{{ $umkm->address }}</span>
                    <span>↗</span>
                </a>

                @if($umkm->landmark)
                    <p class="umkm-landmark">
                        Patokan: {{ $umkm->landmark }}
                    </p>
                @endif
            @endif
            <div style="border-radius:22px;overflow:hidden;border:1px solid #e7e7ef;position:relative;">
                <div id="show-map" style="width:100%;height:340px;"></div>
                <a href="https://www.openstreetmap.org/?mlat={{ $umkm->latitude }}&mlon={{ $umkm->longitude }}#map=17/{{ $umkm->latitude }}/{{ $umkm->longitude }}"
                   target="_blank"
                   style="position:absolute;top:16px;left:16px;z-index:1000;background:white;color:#5848e8;font-weight:700;padding:10px 16px;border-radius:12px;text-decoration:none;box-shadow:0 4px 12px rgba(0,0,0,.12);">
                    Buka di Maps ↗
                </a>
            </div>
        </section>
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
        <script>
            var showMap = L.map('show-map', { scrollWheelZoom: false }).setView([{{ $umkm->latitude }}, {{ $umkm->longitude }}], 16);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(showMap);
            L.marker([{{ $umkm->latitude }}, {{ $umkm->longitude }}]).addTo(showMap);
        </script>
    @endif

    @if($umkm->phone)
        <section class="contact">
            <div>
                <h2>Tertarik dengan {{ $umkm->name }}?</h2>
                <p>Hubungi kami untuk informasi lebih lanjut.</p>
            </div>
            <a href="https://wa.me/{{ preg_replace('/\D+/', '', $umkm->phone) }}" target="_blank">Hubungi via WhatsApp →</a>
        </section>
    @endif
</div>
</body>
</html>
