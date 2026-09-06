<div class="profile-menu" data-profile-menu>
    <button type="button" class="profile-trigger" data-profile-trigger>
        @if(auth()->user()->profile_photo)
            <img src="{{ asset('storage/' . auth()->user()->profile_photo) }}"
                alt="{{ auth()->user()->name }}" class="profile-avatar-image">
        @else
            <span class="profile-avatar" aria-hidden="true">
                <svg viewBox="0 0 24 24">
                    <circle cx="12" cy="8" r="3.5"></circle>
                    <path d="M5 20c.5-4 3.2-6 7-6s6.5 2 7 6"></path>
                </svg>
            </span>
        @endif

        <span class="profile-name">{{ auth()->user()->name }}</span>
        <span class="profile-chevron"></span>
    </button>

    <div class="profile-dropdown" data-profile-dropdown hidden>
        <div class="profile-dropdown-user">
            @if(auth()->user()->profile_photo)
                <img src="{{ asset('storage/' . auth()->user()->profile_photo) }}"
                    alt="{{ auth()->user()->name }}" class="profile-dropdown-avatar">
            @else
                <span class="profile-dropdown-avatar profile-dropdown-initial">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </span>
            @endif

            <div class="profile-dropdown-identity">
                <strong>{{ auth()->user()->name }}</strong>
                <span>{{ auth()->user()->email }}</span>
            </div>
        </div>

        <div class="profile-dropdown-divider"></div>

        <a href="{{ route('dashboard') }}" class="profile-dropdown-item">
            <span class="profile-item-icon">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <rect x="4" y="4" width="6" height="6" rx="1"></rect>
                    <rect x="14" y="4" width="6" height="6" rx="1"></rect>
                    <rect x="4" y="14" width="6" height="6" rx="1"></rect>
                    <rect x="14" y="14" width="6" height="6" rx="1"></rect>
                </svg>
            </span>

            <span class="profile-item-content">
                <strong>Dashboard</strong>
                <small>Kelola UMKM kamu</small>
            </span>

            <span class="profile-item-chevron"></span>
        </a>

        <a href="{{ route('profile.edit') }}" class="profile-dropdown-item">
            <span class="profile-item-icon">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <circle cx="12" cy="8" r="4"></circle>
                    <path d="M4 21c0-4 3.6-7 8-7s8 3 8 7"></path>
                </svg>
            </span>

            <span class="profile-item-content">
                <strong>Profil Saya</strong>
                <small>Kelola informasi akun</small>
            </span>

            <span class="profile-item-chevron"></span>
        </a>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="profile-dropdown-item profile-logout">
                <span class="profile-item-icon">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M10 5H6a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h4"></path>
                        <path d="M14 8l4 4-4 4"></path>
                        <path d="M9 12h9"></path>
                    </svg>
                </span>

                <span class="profile-item-content">
                    <strong>Logout</strong>
                    <small>Keluar dari akun</small>
                </span>
            </button>
        </form>
    </div>
</div>
