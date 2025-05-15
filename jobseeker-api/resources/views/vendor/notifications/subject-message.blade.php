<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekomendasi Lowongan Kerja</title>
    <style>
    .container {
        max-width: 600px;
        margin: 0 auto;
    }

    .card {
        background: #fff;
        margin-bottom: 10px;
        padding: 15px;
        border-radius: 8px;
    }

    .logo {
        max-width: 150px;
    }

    .job-listing {
        margin-bottom: 10px;
    }

    .job-title {
        color: #c0392b;
        font-weight: bold;
        font-size: 18px;
    }

    .company-name {
        font-weight: bold;
        color: #B2B2B2
    }

    .salary {
        color: #2c3e50;
    }

    .card-footer {
        background: #fff;
        margin-bottom: 10px;
        padding: 15px;
        border-radius: 8px;
    }

    .center-img {
        display: block;
        margin-left: auto;
        margin-right: auto;
        width: 80%;
        height: 80%;
    }
    </style>
</head>

<body style="background: #EEEEEE;">
    <div class="container">
        <div class="card" style="margin-bottom: 10px; border-top-left-radius: 0px; border-top-right-radius: 0px;">
            <img src="{{ asset('storage/' . $websiteLogo) }}" alt="Logo {{ $websiteName }}" height="40" width="40"
                style="display: inline; vertical-align: middle;">
            <h1 style="display: inline; vertical-align: middle;">{{ $websiteName }}</h1>
        </div>

        <div style="margin-bottom: 10px;">
            <div style="width: fit-content;" class="center-img">
                <img src="{{ asset('storage/' . $img) }}" alt="">
            </div>
        </div>

        <div class="card" style="border-top: 4px solid #0556f3;">
            <h1>Judul Konten</h1>
            <div style="text-align: justify;">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent feugiat
                vehicula sapien non lacinia.
                Duis ultricies, augue eu euismod vulputate, tortor ipsum imperdiet sem, eget eleifend ipsum nunc quis
                metus.</div>
        </div>

        <div class="card-footer" style="border-bottom-left-radius: 0px; border-bottom-right-radius: 0px;">
            <span>
                {{-- <a style="text-decoration:none;" href="<?php echo env('FRONTEND_URL'); ?>">Berhenti berlangganan</a> --}}
            </span>
            <span>
                <a style="text-decoration:none; margin: 0px 20px;"
                    href="<?php echo env('FRONTEND_URL') . '/policies'; ?>">Policies</a>
            </span>
            <span>
                <a style="text-decoration:none;" href="<?php echo env('FRONTEND_URL') . '/contact-us'; ?>">Hubungi
                    kami</a>
            </span>
        </div>
    </div>
</body>

</html>