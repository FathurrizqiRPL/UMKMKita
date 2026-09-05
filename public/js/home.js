document.addEventListener("DOMContentLoaded", () => {

    /*
    |--------------------------------------------------------------------------
    | Navbar
    |--------------------------------------------------------------------------
    */

    const navbar = document.getElementById("navbar");

    window.addEventListener("scroll", () => {

        navbar.classList.toggle(
            "scrolled",
            window.scrollY > 20
        );

    });


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
    | Filter UMKM
    |--------------------------------------------------------------------------
    */

    const categoryButtons =
        document.querySelectorAll(".category");

    const umkmCards =
        document.querySelectorAll(".umkm-card");


    categoryButtons.forEach(button => {

        button.addEventListener("click", () => {

            const category =
                button.dataset.category;


            categoryButtons.forEach(btn => {
                btn.classList.remove("active");
            });

            button.classList.add("active");


            umkmCards.forEach(card => {

                const cardCategory =
                    card.dataset.category;


                if (
                    category === "all" ||
                    category === cardCategory
                ) {

                    card.classList.remove("hidden");

                } else {

                    card.classList.add("hidden");

                }

            });

        });

    });


    /*
    |--------------------------------------------------------------------------
    | Favorite
    |--------------------------------------------------------------------------
    */

    document
        .querySelectorAll(".favorite")
        .forEach(button => {

            button.addEventListener(
                "click",
                event => {

                    event.preventDefault();

                    button.classList.toggle("liked");

                    button.innerHTML =
                        button.classList.contains("liked")
                            ? "♥"
                            : "♡";

                }
            );

        });


    /*
    |--------------------------------------------------------------------------
    | Counter
    |--------------------------------------------------------------------------
    */

    const counters =
        document.querySelectorAll(".counter");


    const counterObserver =
        new IntersectionObserver(
            entries => {

                entries.forEach(entry => {

                    if (!entry.isIntersecting) {
                        return;
                    }


                    const counter =
                        entry.target;

                    const target =
                        Number(
                            counter.dataset.target
                        );

                    let start = 0;

                    const duration = 1200;

                    const startTime =
                        performance.now();


                    function animate(time) {

                        const progress =
                            Math.min(
                                (time - startTime) /
                                duration,
                                1
                            );


                        const eased =
                            1 - Math.pow(
                                1 - progress,
                                3
                            );


                        start =
                            Math.floor(
                                eased * target
                            );


                        counter.textContent = start;


                        if (progress < 1) {

                            requestAnimationFrame(
                                animate
                            );

                        } else {

                            counter.textContent =
                                target;

                        }

                    }


                    requestAnimationFrame(animate);

                    counterObserver.unobserve(counter);

                });

            },
            {
                threshold: 0.6
            }
        );


    counters.forEach(counter => {

        counterObserver.observe(counter);

    });


    /*
    |--------------------------------------------------------------------------
    | Scroll Reveal
    |--------------------------------------------------------------------------
    */

    const revealElements =
        document.querySelectorAll(
            ".section-header, " +
            ".step-card, " +
            ".umkm-card, " +
            ".why-content, " +
            ".why-visual, " +
            ".cta"
        );


    revealElements.forEach(element => {

        element.classList.add("reveal");

    });


    const revealObserver =
        new IntersectionObserver(
            entries => {

                entries.forEach(entry => {

                    if (
                        entry.isIntersecting
                    ) {

                        entry.target.classList.add(
                            "show"
                        );

                        revealObserver.unobserve(
                            entry.target
                        );

                    }

                });

            },
            {
                threshold: 0.12
            }
        );


    revealElements.forEach(element => {

        revealObserver.observe(element);

    });


    /*
    |--------------------------------------------------------------------------
    | Active Navigation
    |--------------------------------------------------------------------------
    */

    const sections =
        document.querySelectorAll(
            "section[id]"
        );

    const navLinks =
        document.querySelectorAll(
            ".nav-menu a"
        );


    window.addEventListener("scroll", () => {

        let current = "";


        sections.forEach(section => {

            const top =
                section.offsetTop - 150;

            const bottom =
                top + section.offsetHeight;


            if (
                window.scrollY >= top &&
                window.scrollY < bottom
            ) {

                current =
                    section.getAttribute("id");

            }

        });


        navLinks.forEach(link => {

            link.classList.remove("active");


            if (
                link.getAttribute("href") ===
                "#" + current
            ) {

                link.classList.add("active");

            }

        });

    });


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

            const opening =
                parseTime(
                    card.dataset.opening
                );

            const closing =
                parseTime(
                    card.dataset.closing
                );

            const open =
                isOpen(
                    nowMinutes,
                    opening,
                    closing
                );


            if (open === null) {

                badge.textContent = "Aktif";

                badge.className =
                    "umkm-status status-online";

            } else if (open) {

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
