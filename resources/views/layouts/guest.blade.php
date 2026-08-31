<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'UMKMKita') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap"
        rel="stylesheet"
    >
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
   <!-- <link rel="stylesheet" href="{{ asset('css/home.css') }}">
        <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}"> -->
</head>

<body class="auth-page">

    <main class="auth-wrapper">

        <a href="{{ route('home') }}" class="auth-brand">
            <span class="logo-mark">U</span>
            <span>
                UMKM<span class="logo-purple">Kita</span>
            </span>
        </a>

        <div class="auth-card">
            {{ $slot }}
        </div>

    </main>

</body>
</html>