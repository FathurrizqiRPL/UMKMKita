document.addEventListener("DOMContentLoaded", () => {

    /*
    |--------------------------------------------------------------------------
    | Navbar
    |--------------------------------------------------------------------------
    */

    const navbar =
        document.getElementById("navbar");

    if (navbar) {

        window.addEventListener("scroll", () => {

            navbar.classList.toggle(
                "scrolled",
                window.scrollY > 20
            );

        });

    }


    /*
    |--------------------------------------------------------------------------
    | Mobile Menu
    |--------------------------------------------------------------------------
    */

    const mobileMenuBtn =
        document.getElementById("mobileMenuBtn");

    const mobileMenu =
        document.getElementById("mobileMenu");

    if (mobileMenuBtn && mobileMenu) {

        mobileMenuBtn.addEventListener("click", () => {

            mobileMenu.classList.toggle("open");

            document.body.classList.toggle(
                "menu-open"
            );

        });


        mobileMenu.querySelectorAll("a")
            .forEach(link => {

                link.addEventListener("click", () => {

                    mobileMenu.classList.remove("open");

                    document.body.classList.remove(
                        "menu-open"
                    );

                });

            });

    }


    /*
    |--------------------------------------------------------------------------
    | Filter Chip (Kategori & Jenis Usaha)
    |--------------------------------------------------------------------------
    */

    const catalogForm =
        document.getElementById("catalogForm");

    const categoryInput =
        document.getElementById("categoryInput");

    const typeInput =
        document.getElementById("typeInput");

    if (catalogForm && categoryInput && typeInput) {

        document.querySelectorAll(".chip")
            .forEach(chip => {

                chip.addEventListener("click", () => {

                    const field = chip.dataset.field;

                    const value = chip.dataset.value;

                    if (field === "category") {

                        categoryInput.value = value;

                    } else if (field === "type") {

                        typeInput.value = value;

                    }

                    catalogForm.submit();

                });

            });

    }


    /*
    |--------------------------------------------------------------------------
    | Status Buka / Tutup UMKM
    |--------------------------------------------------------------------------
    */

    const statusBadges =
        document.querySelectorAll(".umkm-status");


    function parseTime(value) {

        if (!value) return null;

        const parts = String(value).split(":");

        if (parts.length < 2) return null;

        const hours = Number(parts[0]);

        const minutes = Number(parts[1]);

        if (
            Number.isNaN(hours) ||
            Number.isNaN(minutes)
        ) {
            return null;
        }

        return (hours * 60) + minutes;

    }


    function isOpen(nowMinutes, opening, closing) {

        if (
            opening === null ||
            closing === null
        ) {
            return null;
        }

        // Operasional melewati tengah malam
        if (closing < opening) {

            return nowMinutes >= opening ||
                nowMinutes < closing;

        }

        return nowMinutes >= opening &&
            nowMinutes < closing;

    }


    /*
    |--------------------------------------------------------------------------
    | Status Buka / Tutup UMKM Keliling
    |--------------------------------------------------------------------------
    */

    function locationIsOpen(nowMinutes, schedule) {

        for (const loc of schedule) {

            const opening = parseTime(loc.open);

            const closing = parseTime(loc.close);

            if (
                opening === null ||
                closing === null
            ) {
                continue;
            }

            const open = isOpen(
                nowMinutes,
                opening,
                closing
            );

            if (open) return true;

        }

        return false;

    }


    function updateStatusBadges() {

        if (!statusBadges.length) return;

        const date = new Date();

        const nowMinutes =
            (date.getHours() * 60) +
            date.getMinutes();


        statusBadges.forEach(badge => {

            const card =
                badge.closest(".umkm-card");

            if (!card) return;

            // CEK STATUS MANUAL TERLEBIH DAHULU
            const isManualClosed =
                card.dataset.manual === "1";

            if (isManualClosed) {

                badge.textContent = "Tutup";

                badge.className =
                    "umkm-status status-closed";

                return;

            }


            // Ambil jadwal lokasi dari atribut
            let locationSchedule = [];

            try {

                const raw =
                    card.getAttribute("data-schedule");

                if (raw) {

                    locationSchedule = JSON.parse(raw);

                }

            } catch (err) {

                locationSchedule = [];

            }


            // JAM UMKM (tetap)
            const opening =
                parseTime(
                    card.dataset.opening
                );

            const closing =
                parseTime(
                    card.dataset.closing
                );


            let isOpenNow = null;

            // 1) Jam utama UMKM
            if (opening !== null && closing !== null) {

                isOpenNow = isOpen(
                    nowMinutes,
                    opening,
                    closing
                );

            }

            // 2) Jam dari lokasi titik standby (keliling)
            if (
                isOpenNow === null &&
                locationSchedule.length
            ) {

                isOpenNow = locationIsOpen(
                    nowMinutes,
                    locationSchedule
                );

            }


            // Selalu tampilkan Buka / Tutup
            if (isOpenNow) {

                badge.textContent = "Buka";

                badge.className =
                    "umkm-status status-open";

            } else {

                badge.textContent = "Tutup";

                badge.className =
                    "umkm-status status-closed";

            }

        });

    }


    updateStatusBadges();

    setInterval(updateStatusBadges, 60000);

});