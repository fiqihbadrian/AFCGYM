@php
    // Sidebar admin — urutan & pengelompokan mengikuti versi live:
    // Beranda berdiri sendiri (tanpa label), lalu grup Harian, Data, Laporan.
    // Tombol keluar ada di footer, bukan sebagai item menu.
    $menu = [
        ['label' => 'Beranda', 'route' => 'backend.beranda', 'icon' => 'grid-1x2'],

        ['group' => 'Harian'],
        ['label' => 'Check-in', 'route' => 'backend.kunjungan.index', 'icon' => 'qr-code-scan', 'match' => 'backend.kunjungan.*'],
        ['label' => 'Kasir', 'route' => 'backend.kasir.index', 'icon' => 'cash-coin', 'match' => 'backend.kasir.*'],
        ['label' => 'Pesanan Member', 'route' => 'backend.pesanan.index', 'icon' => 'bag-check', 'match' => 'backend.pesanan.*', 'badge' => $pesananMenunggu ?? 0],

        ['group' => 'Data'],
        ['label' => 'Member', 'route' => 'backend.member.index', 'icon' => 'people', 'match' => 'backend.member.*'],
        ['label' => 'Paket', 'route' => 'backend.paket.index', 'icon' => 'box-seam', 'match' => 'backend.paket.*'],
        ['label' => 'Langganan', 'route' => 'backend.langganan.index', 'icon' => 'receipt', 'match' => 'backend.langganan.*'],

        ['group' => 'Laporan'],
        ['label' => 'Pendapatan', 'route' => 'backend.laporan.index', 'icon' => 'bar-chart-line', 'match' => 'backend.laporan.*'],
    ];
@endphp

<aside class="sidebar" id="sidebar">
    <a href="{{ route('backend.beranda') }}" class="brand">
        <span class="brand__mark"><i class="bi bi-lightning-charge-fill" aria-hidden="true"></i></span>
        <span class="brand__name">AFC<span>Gym</span></span>
    </a>

    <nav>
        @foreach ($menu as $item)
            @isset($item['group'])
                <div class="nav-label">{{ $item['group'] }}</div>
            @else
                @php
                    $pola = $item['match'] ?? $item['route'];
                    $aktif = request()->routeIs($pola);
                @endphp
                {{-- aria-current hanya untuk menu aktif; string statis, aman pakai {!! !!} --}}
                <a href="{{ route($item['route']) }}" class="nav-item {{ $aktif ? 'is-active' : '' }}" {!! $aktif ? 'aria-current="page"' : '' !!}>
                    <i class="bi bi-{{ $item['icon'] }}" aria-hidden="true"></i>
                    <span>{{ $item['label'] }}</span>
                    @if (! empty($item['badge']))
                        <span class="nav-badge">{{ $item['badge'] }}</span>
                    @endif
                </a>
            @endisset
        @endforeach
    </nav>

    <div class="sidebar__foot">
        @if (Auth::user()->foto)
            <img src="{{ asset('storage/img-user/' . Auth::user()->foto) }}" alt="{{ Auth::user()->nama }}" class="avatar">
        @else
            <img src="{{ asset('image/img-default.jpg') }}" alt="{{ Auth::user()->nama }}" class="avatar">
        @endif
        <div class="sidebar__user">
            <strong>{{ Auth::user()->nama }}</strong>
            <span>Administrator</span>
        </div>
        <button type="button" class="sidebar__keluar" title="Keluar" aria-label="Keluar dari akun"
                onclick="document.getElementById('keluar-app').submit();">
            <i class="bi bi-box-arrow-right" aria-hidden="true"></i>
        </button>
    </div>
</aside>
