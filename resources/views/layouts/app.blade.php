<!DOCTYPE html>
<html lang="id">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @php
        $pageTitle = match (true) {
            request()->routeIs('dashboard') => 'Dashboard — UMKMKita',
            request()->routeIs('umkm.create') => 'Buat Website — UMKMKita',
            request()->routeIs('umkm.edit') => 'Edit Website — UMKMKita',
            request()->routeIs('umkm.edit.semua.produk') => 'Edit Produk — UMKMKita',
            request()->routeIs('umkm.delete.website') => 'Hapus Website — UMKMKita',
            request()->routeIs('profile.edit') => 'Profil — UMKMKita',
            default => 'UMKMKita',
        };
    @endphp

    <title>{{ $pageTitle }}</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/umkm-edit.css') }}?v={{ filemtime(public_path('css/umkm-edit.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/create.css') }}?v={{ filemtime(public_path('css/create.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/brand.css') }}">
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
</head>
<body>
<header class="topbar">
    <x-brand-logo :href="route('home')" />
    <nav>
        @if(auth()->user()->umkm)
            <a href="{{ route('umkm.show', auth()->user()->umkm->slug) }}" target="_blank">
                Lihat Website
            </a>
        @endif

       <x-profile-menu />

    </nav>
</header>

@yield('content')

<script src="{{ asset('js/profile.js') }}"></script>
</body>
</html>
