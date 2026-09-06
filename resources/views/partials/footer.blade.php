<footer>

    <div class="container">

        <div class="footer-container">

            <div class="footer-brand">

                <x-brand-logo :href="route('home')" class="footer-logo" />

                <p>
                    Membantu UMKM Indonesia membangun
                    kehadiran digital dengan lebih mudah.
                </p>

            </div>

            <div class="footer-about">

                <strong class="footer-title">
                    Tentang UMKMKita
                </strong>

                <p>
                    Platform digital untuk UMKM Indonesia — membangun
                    website usaha, menampilkan katalog produk, dan memunculkan
                    lokasi bisnis di Radar UMKM agar lebih mudah ditemukan
                    pembeli di sekitarnya.
                </p>

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

                <a href="#faq">
                    FAQ
                </a>

                <a href="{{ route('register') }}">
                    Buat Website
                </a>

                <a href="{{ route('dashboard') }}">
                    Kelola UMKM
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
                    Syarat & Ketentuan
                </a>

            </div>

        </div>

    </div>

    <div class="container footer-faq" id="faq">

        <div class="footer-faq-heading">

            <h3>
                Pertanyaan yang sering diajukan
            </h3>

            <p class="footer-faq-desc">
                Jawaban singkat untuk hal-hal yang
                paling umum ditanyakan soal UMKMKita.
            </p>

        </div>

        <div class="footer-faq-list">

            <details class="footer-faq-item">

                <summary>
                    Bagaimana cara mendaftarkan UMKM saya?
                </summary>

                <p>
                    Buat akun gratis terlebih dahulu, lalu buka halaman
                    "Buat Website". Lengkapi nama usaha, kategori, tipe usaha,
                    dan lokasi, kemudian simpan. Website UMKM langsung aktif
                    dengan link khusus milikmu.
                </p>

            </details>

            <details class="footer-faq-item">

                <summary>
                    Apakah mendaftarkan UMKM di UMKMKita gratis?
                </summary>

                <p>
                    Ya. Membuat akun dan membangun website UMKM tidak dipungut
                    biaya. Cukup isi formulir "Buat Website" dan website kamu
                    langsung terbit.
                </p>

            </details>

            <details class="footer-faq-item">

                <summary>
                    Usaha saya berkeliling, apakah tetap bisa didaftarkan?
                </summary>

                <p>
                    Bisa. Pilih tipe "Keliling" saat mengisi formulir, lalu
                    tambahkan titik-titik standby beserta jam operasional di
                    setiap titik. Titik tersebut yang ditampilkan di Radar UMKM.
                </p>

            </details>

            <details class="footer-faq-item">

                <summary>
                    Bagaimana cara mengubah informasi UMKM?
                </summary>

                <p>
                    Masuk ke akunmu, buka Dashboard, lalu pilih menu perubahan
                    data. Dari sana kamu bisa memperbarui informasi usaha,
                    foto, produk, hingga status operasional.
                </p>

            </details>

            <details class="footer-faq-item">

                <summary>
                    Bagaimana cara menandai usaha sedang tutup?
                </summary>

                <p>
                    Dari Dashboard, gunakan tombol status operasional untuk
                    menandai usaha tutup sementara. Badge "Tutup" otomatis
                    tampil di halaman website UMKM-mu.
                </p>

            </details>

        </div>

    </div>

    <div class="footer-bottom">

        <div class="container footer-bottom-inner">

            <span>
                © {{ date('Y') }} UMKMKita. Semua hak dilindungi.
            </span>

            <span class="footer-made">
                Dibuat untuk UMKM Indonesia
            </span>

        </div>

    </div>

</footer>