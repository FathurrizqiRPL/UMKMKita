@props([
    'href' => null,
    'iconOnly' => false
])

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => 'brand-logo']) }}>
@else
    <div {{ $attributes->merge(['class' => 'brand-logo']) }}>
@endif

    <span class="brand-logo-mark" aria-hidden="true">
        <svg viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg">
            <rect width="64" height="64" rx="16" fill="#5848e8"/>
            <path d="M17 24h30l-4-8H21l-4 8Z" fill="#a99cff"/>
            <path d="M27 16h10l2 8H25l2-8Z" fill="#ffffff"/>
            <path d="M19 25v19c0 5 4 9 9 9h8c5 0 9-4 9-9V25h-7v18c0 2-1 3-3 3h-6c-2 0-3-1-3-3V25h-7Z" fill="#ffffff"/>
            <path d="M31 38c-5-1-8-4-8-9 5 0 9 3 9 8v5h-1v-4Z" fill="#56d6a0"/>
            <path d="M33 38c5-1 8-4 8-9-5 0-9 3-9 8v5h1v-4Z" fill="#56d6a0"/>
        </svg>
    </span>

    @unless ($iconOnly)
        <span class="brand-logo-text">
            UMKM<span>Kita</span>
        </span>
    @endunless

@if ($href)
    </a>
@else
    </div>
@endif
