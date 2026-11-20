<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $judul ?? 'AFCGym' }} | AFCGym</title>
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('image/logo.png') }}">
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/icons/bootstrap-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/datatables/datatables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
</head>

<body>
    @include('admin.layouts.sidebar')

    <div class="overlay" id="overlay"></div>

    <div class="main">
        <header class="topbar">
            <button class="burger" id="burger" type="button" aria-label="Buka menu">
                <i class="bi bi-list"></i>
            </button>
            <div>
                <h2 class="topbar__title">{{ $judul ?? 'AFCGym' }}</h2>
                <div class="topbar__crumb">AFCGym / {{ $judul ?? 'Beranda' }}</div>
            </div>
            <div class="topbar__right">
                <span class="topbar__date">{{ tanggal_id(now()) }}</span>
                <a href="{{ route('backend.kunjungan.index') }}" class="icon-btn" title="Check-in member">
                    <i class="bi bi-qr-code-scan"></i>
                </a>
            </div>
        </header>

        <div class="content">
            @yield('content')
        </div>

        <div class="page-foot">AFCGym &copy; {{ date('Y') }} &middot; Sistem Informasi Manajemen Gym</div>
    </div>

    <script src="{{ asset('vendor/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/datatables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('sweetalert/sweetalert2.all.min.js') }}"></script>
    <script>
        // Drawer sidebar untuk layar kecil
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');

        function tutupMenu() {
            sidebar.classList.remove('is-open');
            overlay.classList.remove('is-open');
        }

        document.getElementById('burger').addEventListener('click', function () {
            sidebar.classList.toggle('is-open');
            overlay.classList.toggle('is-open');
        });

        overlay.addEventListener('click', tutupMenu);

        if (document.getElementById('zero_config')) {
            new DataTable('#zero_config', {
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50],
                language: {
                    search: 'Cari:',
                    lengthMenu: 'Tampilkan _MENU_ data',
                    info: 'Menampilkan _START_–_END_ dari _TOTAL_ data',
                    infoEmpty: 'Belum ada data',
                    infoFiltered: '(disaring dari _MAX_ data)',
                    zeroRecords: 'Data tidak ditemukan',
                    emptyTable: 'Belum ada data',
                    paginate: { first: 'Awal', last: 'Akhir', next: 'Berikutnya', previous: 'Sebelumnya' }
                }
            });
        }
    </script>

    <form id="keluar-app" action="{{ route('backend.logout') }}" method="POST" class="d-none">
        @csrf
    </form>

    @if (session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: @json(session('success')),
            confirmButtonColor: '#ff6a1a'
        });
    </script>
    @endif

    @if (session('error'))
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: @json(session('error')),
            confirmButtonColor: '#ff6a1a'
        });
    </script>
    @endif

    <script>
        $('.show_confirm').click(function (event) {
            var form = $(this).closest('form');
            var konfdelete = $(this).data('konf-delete');
            event.preventDefault();
            Swal.fire({
                title: 'Hapus data ini?',
                html: 'Data <strong>' + konfdelete + '</strong> akan dihapus permanen.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#c92a2a',
                cancelButtonColor: '#7a8699',
                confirmButtonText: 'Ya, hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Terhapus',
                        text: 'Data berhasil dihapus.',
                        icon: 'success',
                        confirmButtonColor: '#ff6a1a',
                        showConfirmButton: false,
                        timer: 900
                    }).then(() => form.trigger('submit'));
                }
            });
        });
    </script>

    @stack('scripts')
</body>

</html>
