@php
    // Sidebar khusus admin — navigasi member ada di member/layouts/partials/navbar
    $menu = [
        ['route' => 'backend.beranda', 'label' => 'Beranda', 'icon' => 'grid-1x2'],
        ['route' => 'backend.member.index', 'label' => 'Data Member', 'icon' => 'people', 'match' => 'backend.member.*'],
        ['route' => 'backend.paket.index', 'label' => 'Paket', 'icon' => 'box-seam', 'match' => 'backend.paket.*'],
        ['route' => 'backend.langganan.index', 'label' => 'Langganan', 'icon' => 'receipt', 'match' => 'backend.langganan.*'],
        ['route' => 'backend.kasir.index', 'label' => 'Kasir Latihan', 'icon' => 'cash-coin', 'match' => 'backend.kasir.*'],
        ['route' => 'backend.kunjungan.index', 'label' => 'Check-in', 'icon' => 'qr-code-scan', 'match' => 'backend.kunjungan.*'],
        ['route' => 'backend.laporan.index', 'label' => 'Laporan', 'icon' => 'bar-chart-line', 'match' => 'backend.laporan.*'],
    ];
@endphp

<aside class="sidebar" id="sidebar">
    <a href="{{ route('backend.beranda') }}" class="brand">
        <span class="brand__mark"><i class="bi bi-lightning-charge-fill"></i></span>
        <span>
            <span class="brand__name">AFC<span>Gym</span></span>
            <span class="brand__sub">Management System</span>
        </span>
    </a>

    <nav>
        <div class="nav-label">Menu</div>
        @foreach ($menu as $item)
            @php
                $pola = $item['match'] ?? $item['route'];
                $aktif = request()->routeIs($pola);
            @endphp
            <a href="{{ route($item['route']) }}" class="nav-item {{ $aktif ? 'is-active' : '' }}">
                <i class="bi bi-{{ $item['icon'] }}"></i>
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach

        <div class="nav-label">Akun</div>
        <a href="#" class="nav-item" onclick="event.preventDefault(); document.getElementById('keluar-app').submit();">
            <i class="bi bi-box-arrow-right"></i>
            <span>Keluar</span>
        </a>
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
    </div>
</aside>
