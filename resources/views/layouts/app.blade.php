<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Dashboard — UMKMKita' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/umkm-edit.css') }}?v={{ filemtime(public_path('css/umkm-edit.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/create.css') }}?v={{ filemtime(public_path('css/create.css')) }}">
</head>
<body>
<header class="topbar">
    <a class="brand" href="{{ route('home') }}">
        <span>U</span>
        UMKM<b>Kita</b>
    </a>
    <nav>
        @if(auth()->user()->umkm)

            <a href="{{ route('umkm.show', auth()->user()->umkm->slug) }}" target="_blank">
                Lihat Website
            </a>

            <a href="{{ route('dashboard') }}">
                Dashboard UMKM
            </a>
        @endif
        <form method="POST" action="{{ route('logout') }}" style="display:inline">
            @csrf
            <button class="logout-btn" type="submit">
                Logout
            </button>
        </form>
    </nav>
</header>

@yield('content')
</body>
</html>
