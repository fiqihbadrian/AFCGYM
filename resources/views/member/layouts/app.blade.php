<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $judul ?? 'Member Area' }} | AFCGym</title>
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('image/logo.png') }}">
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/icons/bootstrap-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
</head>

<body class="member-body">
    @include('member.layouts.partials.navbar')

    <main class="member-main">
        @yield('content')
    </main>

    <footer class="member-foot">
        AFCGym &copy; {{ date('Y') }} &middot; Sistem Informasi Manajemen Gym
    </footer>

    <script src="{{ asset('vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('sweetalert/sweetalert2.all.min.js') }}"></script>

    <form id="keluar-app" action="{{ route('backend.logout') }}" method="POST" class="d-none">
        @csrf
    </form>

    @if (session('success'))
    <script>
        Swal.fire({ icon: 'success', title: 'Berhasil', text: @json(session('success')), confirmButtonColor: '#ff6a1a' });
    </script>
    @endif

    @if (session('error'))
    <script>
        Swal.fire({ icon: 'error', title: 'Gagal', text: @json(session('error')), confirmButtonColor: '#ff6a1a' });
    </script>
    @endif

    @stack('scripts')
</body>

</html>
