<!DOCTYPE html>
<html lang="id">

  <head>
    <meta charset="UTF-8">
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0"
    >
    <title>Password Reset Success</title>
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

      <div
        class="card"
        style="text-align: center;"
      >
        <h1 style="header">
          Password berhasil diubah!
        </h1>
        <p>
          Password akun Anda telah berhasil diubah. <br />
          Jika Anda tidak merasa melakukan perubahan ini, segera hubungi kami.
        </p>
        <span><a
            class="btn-upload-resume"
            href="<?= env('FRONTEND_URL') ?>"
          >Login</a></span>
      </div>

      <div
        class="card-footer"
        style="border-bottom-left-radius: 0px; border-bottom-right-radius: 0px;"
      >
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
