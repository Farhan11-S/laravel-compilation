<!DOCTYPE html>
<html lang="id">

  <head>
    <meta charset="UTF-8">
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0"
    >
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
    </style>
  </head>

  <body style="background: #EEEEEE;">
    <div class="container">
      <div
        class="card"
        style="margin-bottom: 10px; border-top-left-radius: 0px; border-top-right-radius: 0px;"
      >
        <img
          src="{{ asset('storage/' . $websiteLogo) }}"
          alt="Logo {{ $websiteName }}"
          height="40"
          width="40"
          style="display: inline; vertical-align: middle;"
        >
        <h1 style="display: inline; vertical-align: middle;">{{ $websiteName }}</h1>
      </div>

      <div class="card">
        <h2>Hai {{ $user->name }}, kami punya rekomendasi lowongan kerja baru untuk Anda</h2>
        <div>Kami ingin membantumu menemukan lowongan kerja yang tepat, dan posisi berikut mungkin cocok untukmu.</div>
        <div>Kami menyarankan lowongan kerja ini berdasarkan profilmu serta lowongan dan lamaran kerja yang pernah
          dilihat.</div>
      </div>

      @foreach ($jobs as $job)
        <div class="job-listing">
          <div class="card">
            @if (!empty($job->company->logo))
              <img
                src="{{ asset('storage/' . $job->company->logo) }}"
                alt="Logo {{ $job->company?->name }}"
              >
            @endif
            <div class="job-title"job>{{ $job->job_title }}</div>
            @if (!empty($job->company))
              <p class="company-name">{{ $job->company->name }}</p>
            @endif
            <p>{{ $job->location }}</p>

            <?php
            $currency = !empty($job->currency_code) ? strtoupper($job->currency_code) : 'Rp';
            $hyphen = !empty($job->minimum_wage) && !empty($job->maximum_wage) ? '-' : '';
            $perMonth = !empty($job->minimum_wage) || !empty($job->maximum_wage) ? 'per month' : '';
            ?>
            <p class="salary">
              {{ !empty($job->minimum_wage) ? $currency . ' ' . number_format($job->minimum_wage, 2) : '' }}
              {{ $hyphen }}
              {{ !empty($job->maximum_wage) ? $currency . ' ' . number_format($job->maximum_wage, 2) : '' }}
              {{ $perMonth }}
            </p>
          </div>
        </div>
      @endforeach

      <div style="background: #fff; padding: 1px; border-radius: 8px">
        {{-- Action Button --}}
        @isset($actionText)
          <?php
          $color = match ($level) {
              'success', 'error' => $level,
              default => 'primary',
          };
          ?>
          <x-mail::button
            :url="$actionUrl"
            :color="$color"
          >
            {{ $actionText }}
          </x-mail::button>
        @endisset
      </div>

      @if ($user->candidates()->exists())
        <div
          class="card"
          style="margin: 10px 0; color: #606676; font-size: 14px;"
        >
          Rekomendasi ini didasarkan pada aktivitasmu sebelumnya di {{ $websiteName }}.
        </div>
      @endif

      <div
        class="card"
        style="background: #2d3748; color: #fff; margin-top: 10px;"
      >
        Saran karier yang dibuat khusus untuk Anda
        <a
          style="color: #fff;"
          href="<?php echo env('FRONTEND_URL'); ?>"
        >Jelajahi sekarang</a>
      </div>

      <div
        class="card-footer"
        style="border-bottom-left-radius: 0px; border-bottom-right-radius: 0px;"
      >
        <span>
          {{-- <a style="text-decoration:none;" href="<?php echo env('FRONTEND_URL'); ?>">Berhenti berlangganan</a> --}}
        </span>
        <span>
          <a
            style="text-decoration:none; margin: 0px 20px;"
            href="<?php echo env('FRONTEND_URL') . '/policies'; ?>"
          >Policies</a>
        </span>
        <span>
          <a
            style="text-decoration:none;"
            href="<?php echo env('FRONTEND_URL') . '/contact-us'; ?>"
          >Hubungi kami</a>
        </span>
      </div>
    </div>
  </body>

</html>
