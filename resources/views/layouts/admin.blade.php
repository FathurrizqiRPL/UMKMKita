<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Admin — UMKMKita' }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>

<body>
    <div class="admin-shell">
        <aside class="admin-sidebar">
            <a href="{{ route('admin.dashboard') }}" class="admin-brand">
                <span>U</span>
                <strong>UMKM<b>Kita</b></strong>
            </a>

            <div class="admin-badge">ADMIN PANEL</div>

            <nav class="admin-nav">
                <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Dashboard</a>
                <a href="{{ route('admin.umkms') }}" class="{{ request()->routeIs('admin.umkms*') ? 'active' : '' }}">Kelola UMKM</a>
                <a href="{{ route('admin.users') }}" class="{{ request()->routeIs('admin.users*') ? 'active' : '' }}">Pengguna</a>
                <a href="{{ route('home') }}" target="_blank">Lihat Website ↗</a>
            </nav>

            <div class="admin-user">
                <span>{{ auth()->user()->name }}</span>
                <small>{{ auth()->user()->email }}</small>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit">Keluar</button>
                </form>
            </div>
        </aside>

        <main class="admin-main">
            @if(session('success'))
                <div class="admin-alert">{{ session('success') }}</div>
            @endif

            @yield('content')
        </main>
    </div>
</body>
</html>