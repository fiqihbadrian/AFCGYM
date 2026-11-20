@php
    $menu = [
        ['route' => 'member.beranda', 'label' => 'Beranda', 'icon' => 'house'],
        ['route' => 'member.kartu', 'label' => 'Kartu Saya', 'icon' => 'credit-card-2-front'],
        ['route' => 'member.riwayat', 'label' => 'Riwayat', 'icon' => 'clock-history'],
    ];

    $akun = Auth::user();
    $memberLogin = $akun->member;
@endphp

<header class="mnav">
    <a href="{{ route('member.beranda') }}" class="mnav__brand">
        <span class="mnav__mark"><i class="bi bi-lightning-charge-fill"></i></span>
        <span class="mnav__name">AFC<span>Gym</span></span>
    </a>

    <nav class="mnav__links">
        @foreach ($menu as $item)
            <a href="{{ route($item['route']) }}"
               class="mnav__link {{ request()->routeIs($item['route']) ? 'is-active' : '' }}">
                <i class="bi bi-{{ $item['icon'] }}"></i>
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>

    <div class="mnav__user">
        <img src="{{ $memberLogin && $memberLogin->foto ? asset('storage/img-member/' . $memberLogin->foto) : asset('image/img-default.jpg') }}"
             alt="{{ $akun->nama }}" class="mnav__avatar">
        <div class="mnav__who">
            <strong>{{ $akun->nama }}</strong>
            <span>{{ $memberLogin->kode_member ?? 'Member' }}</span>
        </div>
        <button type="button" class="mnav__out" title="Keluar"
                onclick="event.preventDefault(); document.getElementById('keluar-app').submit();">
            <i class="bi bi-box-arrow-right"></i>
        </button>
    </div>
</header>
