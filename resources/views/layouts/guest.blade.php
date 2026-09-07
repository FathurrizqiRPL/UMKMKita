<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

   @php
    $pageTitle = match (true) {
        request()->routeIs('login') => 'Masuk — UMKMKita',
        request()->routeIs('register') => 'Daftar — UMKMKita',
        request()->routeIs('password.confirm') => 'Konfirmasi Password — UMKMKita',
        request()->routeIs('verification.notice') => 'Verifikasi Email — UMKMKita',
        default => 'UMKMKita',
    };
    @endphp

    <title>{{ $pageTitle }}</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    <link rel="stylesheet" href="{{ asset('css/brand.css') }}">
</head>

<body class="auth-page">
    <main class="auth-wrapper">
        <x-brand-logo :href="route('home')" />

        <div class="auth-card">
            {{ $slot }}
        </div>
    </main>
</body>
</html>
