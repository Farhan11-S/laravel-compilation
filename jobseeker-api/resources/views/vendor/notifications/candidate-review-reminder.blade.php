<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">
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

        .card-footer {
            background: #fff;
            padding: 15px;
            border-radius: 8px;
        }

        .btn-upload-resume {
            background: #2d3748;
            color: #fff;
            text-decoration: none;
            padding: 8px 10px;
            border-radius: 4px;
        }
    </style>
</head>

<body style="background: #EEEEEE;">
    <div class="container">
        <h1
            class="card"
            style="margin-bottom: 10px; border-top-left-radius: 0px; border-top-right-radius: 0px;">
            Rheinjobs
        </h1>

        <div class="card" style="text-align: center;">
            Kami ingin mengingatkan Anda bahwa ada beberapa kandidat yang belum direview untuk posisi yang Anda buka. Mohon luangkan waktu untuk meninjau lamaran mereka dan memberikan feedback.
            Anda dapat melihat daftar kandidat yang belum direview melalui dashboard rekrutmen:
            @isset($actionText)
            <?php
            $color = match ($level) {
                'success', 'error' => $level,
                default => 'primary',
            };
            ?>
            <x-mail::button
                :url="$actionUrl"
                :color="$color">
                {{ $actionText }}
            </x-mail::button>
            @endisset
            Review tepat waktu membantu memastikan proses rekrutmen berjalan lancar dan tidak melewatkan kandidat potensial.
        </div>
        <div class="card-footer" style="border-bottom-left-radius: 0px; border-bottom-right-radius: 0px;">
            <span>
                {{-- <a style="text-decoration:none;" href="<?php echo env('FRONTEND_URL'); ?>">Berhenti berlangganan</a> --}}
            </span>
            <span>
                <a
                    style="text-decoration:none; margin: 0px 20px;"
                    href="<?php echo env('FRONTEND_URL') . '/policies'; ?>">Policies</a>
            </span>
            <span>
                <a
                    style="text-decoration:none;"
                    href="<?php echo env('FRONTEND_URL') . '/contact-us'; ?>">Hubungi kami</a>
            </span>
        </div>
    </div>
</body>

</html>