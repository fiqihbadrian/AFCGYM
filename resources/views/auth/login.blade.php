<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk | AFCGym</title>
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('image/logo.png') }}">
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/icons/bootstrap-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
</head>

<body>
    <div class="auth">
        <section class="auth__brand">
            <div class="brand" style="padding: 0;">
                <span class="brand__mark"><i class="bi bi-lightning-charge-fill"></i></span>
                <span>
                    <span class="brand__name">AFC<span>Gym</span></span>
                    <span class="brand__sub">Management System</span>
                </span>
            </div>

            <h1 class="auth__title">Kelola keanggotaan gym<br>tanpa <em>catatan kertas</em>.</h1>
            <p class="auth__lead">
                Satu tempat untuk data member, paket, perpanjangan langganan, pembayaran, dan kehadiran harian.
            </p>

            <ul class="auth__list">
                <li><i class="bi bi-check2-circle"></i> Status aktif dihitung otomatis dari tanggal berakhir</li>
                <li><i class="bi bi-check2-circle"></i> Perpanjangan menyambung dari masa aktif sebelumnya</li>
                <li><i class="bi bi-check2-circle"></i> Check-in lewat scan QR kartu member</li>
                <li><i class="bi bi-check2-circle"></i> Laporan pendapatan per periode</li>
            </ul>
        </section>

        <section class="auth__form">
            <div class="auth__box">
                <h2>Masuk ke akun</h2>
                <p>Admin dan member memakai halaman masuk yang sama.</p>

                @if (session()->has('error'))
                <div class="alert-box alert-box--danger">
                    <i class="bi bi-exclamation-octagon"></i>
                    <span>{{ session('error') }}</span>
                </div>
                @endif

                <form action="{{ route('backend.login.aksi') }}" method="post">
                    @csrf

                    <div class="field">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}"
                               class="form-control @error('email') is-invalid @enderror"
                               placeholder="nama@email.com" autocomplete="username" autofocus>
                        @error('email')
                        <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password"
                               class="form-control @error('password') is-invalid @enderror"
                               placeholder="Masukkan password" autocomplete="current-password">
                        @error('password')
                        <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-accent w-100 justify-content-center">
                        Masuk <i class="bi bi-arrow-right"></i>
                    </button>
                </form>

                @if (config('app.debug'))
                <div class="auth__hint">
                    <strong style="color: #414b5a;">Akun demo</strong><br>
                    Admin &mdash; <code>admin@afcgym.test</code> / <code>admin123</code><br>
                    Member &mdash; <code>budi.santoso@afcgym.test</code> / <code>member123</code>
                </div>
                @endif
            </div>
        </section>
    </div>
</body>

</html>
