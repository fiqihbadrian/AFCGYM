@extends('member.layouts.app')

@section('content')
@php
    $berlangganan = $paket->where('jenis', \App\Models\Paket::JENIS_PAKET);
    $latihan      = $paket->where('jenis', \App\Models\Paket::JENIS_LATIHAN);
    $paketAktif   = $member->langgananTerakhir;
@endphp

<div class="page-head">
    <div>
        <h1>Paket &amp; Latihan</h1>
        <p>Pilih paket bulanan atau bayar per latihan. Pesanan masuk ke kasir, bayar di tempat, petugas yang mengonfirmasi.</p>
    </div>
</div>

@if ($paketAktif && $member->aktif)
<div class="alert-box alert-box--ok" style="margin-bottom: 20px;">
    <i class="bi bi-patch-check"></i>
    <span>Paket kamu masih aktif &mdash; sisa {{ $member->sisa_hari }} hari, masuk latihan gratis tanpa bayar per kedatangan.</span>
</div>
@endif

{{-- ---------------------------------------------------------------- --}}
{{-- Paket berlangganan                                               --}}
{{-- ---------------------------------------------------------------- --}}
<div class="panel" style="margin-bottom: 20px;">
    <div class="panel__head">
        <div>
            <h3 class="panel__title">Paket Berlangganan</h3>
            <p class="panel__desc">Bayar sekali, bebas masuk selama masa aktif. Perpanjangan menyambung dari tanggal berakhir.</p>
        </div>
    </div>

    <div class="paket-list">
        @forelse ($berlangganan as $row)
        <div class="paket-item">
            <div class="paket-item__main">
                <strong>{{ $row->nama }}</strong>
                <span class="paket-item__meta">
                    {{ $row->durasi_hari }} hari
                    @if ($row->keterangan)
                        &middot; {{ $row->keterangan }}
                    @endif
                </span>
            </div>
            <div class="paket-item__side">
                <div class="paket-item__harga">{{ rupiah($row->harga) }}</div>
                <form action="{{ route('member.paket.pesan') }}" method="post">
                    @csrf
                    <input type="hidden" name="paket_id" value="{{ $row->id }}">
                    <input type="hidden" name="metode" value="Tunai">
                    <button type="submit" class="btn btn-accent btn-sm">
                        <i class="bi bi-bag-plus"></i> Pesan
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="empty">Belum ada paket berlangganan yang dibuka.</div>
        @endforelse
    </div>
</div>

{{-- ---------------------------------------------------------------- --}}
{{-- Bayar per latihan                                                --}}
{{-- ---------------------------------------------------------------- --}}
<div class="panel" style="margin-bottom: 20px;">
    <div class="panel__head">
        <div>
            <h3 class="panel__title">Bayar Per Latihan</h3>
            <p class="panel__desc">Tanpa paket, bayar per kedatangan. Berlaku sampai gym tutup, datang lagi hari yang sama tidak ditagih ulang.</p>
        </div>
    </div>

    @if ($paketAktif && $member->aktif)
    <div class="alert-box alert-box--warn" style="margin-bottom: 14px;">
        <i class="bi bi-info-circle"></i>
        <span>Paket kamu aktif, jadi latihan tetap gratis. Opsi ini tidak perlu dipakai.</span>
    </div>
    @endif

    <div class="paket-list">
        @forelse ($latihan as $row)
        <div class="paket-item">
            <div class="paket-item__main">
                <strong>{{ $row->nama }}</strong>
                <span class="paket-item__meta">{{ $row->keterangan ?: 'Sekali masuk' }}</span>
            </div>
            <div class="paket-item__side">
                <div class="paket-item__harga">{{ rupiah($row->harga) }}</div>
                <span class="pill pill--muted">Bayar di kasir</span>
            </div>
        </div>
        @empty
        <div class="empty">Belum ada tarif latihan.</div>
        @endforelse
    </div>
</div>

{{-- ---------------------------------------------------------------- --}}
{{-- Pesanan saya                                                     --}}
{{-- ---------------------------------------------------------------- --}}
<div class="panel panel--flush">
    <div class="panel__head panel__head--padded">
        <div>
            <h3 class="panel__title">Pesanan Saya</h3>
            <p class="panel__desc">Menunggu sampai petugas kasir mengonfirmasi pembayaran.</p>
        </div>
    </div>
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Paket</th>
                    <th class="num">Jumlah</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pesananSaya as $row)
                <tr>
                    <td style="white-space: nowrap;">{{ $row->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $row->paket->nama ?? '-' }}</td>
                    <td class="num">{{ rupiah($row->jumlah) }}</td>
                    <td>
                        @if ($row->status === \App\Models\Pesanan::STATUS_DIPROSES)
                            <span class="pill pill--ok">Sudah diproses</span>
                        @else
                            <span class="pill pill--warn">Menunggu konfirmasi</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="empty">Belum ada pesanan. Pilih paket di atas.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
