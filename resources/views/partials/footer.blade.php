<footer>

    <div class="container">

        <div class="footer-container">

            <div class="footer-brand">

                <x-brand-logo :href="route('home')" class="footer-logo" />

                <p>
                    Membantu UMKM Indonesia membangun
                    kehadiran digital dengan lebih mudah.
                </p>

                <div class="footer-socials">

                    <a href="#" aria-label="Instagram">
                        <svg viewBox="0 0 24 24" width="17" height="17" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="18" height="18" rx="5"></rect>
                            <circle cx="12" cy="12" r="4"></circle>
                            <circle cx="17.2" cy="6.8" r="1" fill="currentColor" stroke="none"></circle>
                        </svg>
                    </a>

                    <a href="#" aria-label="Facebook">
                        <svg viewBox="0 0 24 24" width="17" height="17" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path>
                        </svg>
                    </a>

                    <a href="#" aria-label="X / Twitter">
                        <svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor">
                            <path d="M18.9 2.5h3.4l-7.4 8.5 8.7 10.5h-6.8l-5.3-6.2-6.1 6.2H.9l7.9-9.1L.5 2.5h7l4.8 5.7 5.6-5.7zm-1.2 17h1.9L6.9 4.6H4.9l12.8 14.9z"></path>
                        </svg>
                    </a>

                    <a href="#" aria-label="YouTube">
                        <svg viewBox="0 0 24 24" width="17" height="17" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M2.5 12s0-4.5 1.5-6a3.5 3.5 0 0 1 2-1C9.6 4.4 12 4.5 12 4.5s2.4-.1 6 .5a3.5 3.5 0 0 1 2 1c1.5 1.5 1.5 6 1.5 6s0 4.5-1.5 6a3.5 3.5 0 0 1-2 1c-3.6-.6-6-.5-6-.5s-2.4-.1-6 .5a3.5 3.5 0 0 1-2-1c-1.5-1.6-1.5-6-1.5-6z"></path>
                            <path d="m10 9.5 4.5 2.5L10 14.5z"></path>
                        </svg>
                    </a>

                </div>

            </div>

            <div class="footer-news">

                <strong class="footer-title">
                    Ragam UMKM & Tips Bisnis
                </strong>

                <p>
                    Update usaha lokal baru dan ide bisnis
                    langsung di emailmu.
                </p>

                <form class="footer-news-form" id="footerNewsForm" method="GET" action="#">

                    <input type="email" name="email" placeholder="Alamat email kamu" required autocomplete="off">

                    <button type="submit" aria-label="Berlangganan">
                        →
                    </button>

                </form>

                <span class="footer-news-note">
                    Gratis, tanpa spam, berhenti kapan saja.
                </span>

            </div>

            <div class="footer-column">

                <strong>
                    Platform
                </strong>

                <a href="{{ route('home') }}">
                    Beranda
                </a>

                <a href="{{ route('home') . '#cara-kerja' }}">
                    Cara Kerja
                </a>

                <a href="{{ route('umkm.index') }}">
                    Jelajahi UMKM
                </a>

                <a href="{{ route('radar') }}">
                    Radar UMKM
                </a>

            </div>

            <div class="footer-column">

                <strong>
                    Bantuan
                </strong>

                <a href="#">
                    Panduan
                </a>

                <a href="#">
                    FAQ
                </a>

                <a href="#">
                    Kontak
                </a>

            </div>

            <div class="footer-column">

                <strong>
                    Legal
                </strong>

                <a href="#">
                    Kebijakan Privasi
                </a>

                <a href="#">
                    Ketentuan
                </a>

            </div>

        </div>

    </div>

    <div class="footer-bottom">

        <div class="container footer-bottom-inner">

            <span>
                © {{ date('Y') }} UMKMKita. Semua hak dilindungi.
            </span>

            <div class="footer-bottom-links">

                <a href="#">
                    Kebijakan Privasi
                </a>

                <a href="#">
                    Ketentuan
                </a>

            </div>

            <span class="footer-made">
                Dibuat untuk UMKM Indonesia
            </span>

        </div>

    </div>

</footer>

<script>
    document.addEventListener("DOMContentLoaded", () => {

        const form = document.getElementById("footerNewsForm");

        if (!form) return;

        form.addEventListener("submit", (event) => {

            event.preventDefault();

            const input = form.querySelector("input");

            if (!input.value.trim()) return;

            const note = form.parentElement.querySelector(".footer-news-note");

            if (note) note.textContent = "Terima kasih sudah berlangganan.";

            input.value = "";

        });

    });
</script>