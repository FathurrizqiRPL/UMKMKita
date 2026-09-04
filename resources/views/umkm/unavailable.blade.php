<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Website Tidak Tersedia — UMKMKita</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background:
                radial-gradient(circle at top, rgba(88, 72, 232, 0.08), transparent 35%),
                #fafafe;
            color: #171b2d;
            font-family: "DM Sans", sans-serif;
        }

        .page-card {
            width: 100%;
            max-width: 650px;
            padding: 54px 44px;
            border: 1px solid #e8e7ef;
            border-radius: 24px;
            background: #ffffff;
            text-align: center;
            box-shadow: 0 24px 70px rgba(35, 30, 85, 0.08);
        }

        .logo {
            width: 64px;
            height: 64px;
            display: grid;
            place-items: center;
            margin: 0 auto 25px;
            border-radius: 18px;
            background: #5848e8;
            color: #ffffff;
            font-family: "Plus Jakarta Sans", sans-serif;
            font-size: 25px;
            font-weight: 800;
        }

        .label {
            display: inline-block;
            margin-bottom: 13px;
            color: #5848e8;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 0.17em;
        }

        h1 {
            margin: 0;
            font-family: "Plus Jakarta Sans", sans-serif;
            font-size: clamp(32px, 5vw, 44px);
            line-height: 1.14;
            letter-spacing: -1.7px;
        }

        h1 span {
            color: #5848e8;
        }

        .description {
            max-width: 510px;
            margin: 18px auto 0;
            color: #74798b;
            font-size: 15px;
            line-height: 1.75;
        }

        .umkm-name {
            color: #34384b;
            font-weight: 700;
        }

        .divider {
            width: 52px;
            height: 3px;
            margin: 29px auto;
            border-radius: 99px;
            background: #dedaff;
        }

        .help-text {
            margin: 0 0 19px;
            color: #74798b;
            font-size: 13px;
            line-height: 1.6;
        }

        .home-button {
            min-height: 49px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 0 20px;
            border-radius: 12px;
            background: #5848e8;
            color: #ffffff;
            font-size: 13px;
            font-weight: 800;
            text-decoration: none;
            transition: 0.2s ease;
        }

        .home-button:hover {
            background: #4637cc;
            transform: translateY(-1px);
        }

        .brand {
            margin-top: 35px;
            color: #a0a3af;
            font-size: 11px;
        }

        .brand strong {
            color: #5848e8;
        }

        @media (max-width: 600px) {
            .page-card {
                padding: 40px 23px;
            }
        }
    </style>
</head>

<body>

    <main class="page-card">

        <div class="logo">
            U
        </div>

        <span class="label">
            WEBSITE TIDAK TERSEDIA
        </span>

        <h1>
            Website ini sudah<br>
            <span>tidak tersedia.</span>
        </h1>

        <p class="description">
            Website UMKM
            <span class="umkm-name">{{ $deletedUmkm->name }}</span>
            telah dihapus oleh pemiliknya sehingga halaman ini sudah tidak dapat diakses.
        </p>

        <div class="divider"></div>

        <p class="help-text">
            Jangan khawatir, masih banyak usaha lokal lainnya yang bisa kamu temukan di UMKMKita.
        </p>

        <a href="{{ route('home') }}" class="home-button">
            Jelajahi UMKM Lainnya
            <span>→</span>
        </a>

        <div class="brand">
            <strong>UMKMKita</strong> · Digitalisasi UMKM Indonesia
        </div>

    </main>

</body>
</html>