@extends('admin.layouts.app')

@section('content')
<div class="page-head">
    <div>
        <h1>{{ $judul }}</h1>
        <p>Pendapatan dan kunjungan pada rentang tanggal yang dipilih.</p>
    </div>
</div>

<div class="panel">
    <form action="{{ route('backend.laporan.index') }}" method="get">
        <div class="toolbar">
            <div class="field">
                <label for="dari">Dari tanggal</label>
                <input type="date" id="dari" name="dari" value="{{ $dari->format('Y-m-d') }}" class="form-control">
            </div>
            <div class="field">
                <label for="sampai">Sampai tanggal</label>
                <input type="date" id="sampai" name="sampai" value="{{ $sampai->format('Y-m-d') }}" class="form-control">
            </div>
            <button type="submit" class="btn btn-accent"><i class="bi bi-search"></i> Tampilkan</button>
            <a href="{{ route('backend.laporan.index') }}" class="btn btn-ghost">Bulan Ini</a>
        </div>
    </form>
</div>

<div class="stats">
    <div class="stat">
        <div class="stat__icon stat__icon--ok"><i class="bi bi-cash-coin"></i></div>
        <div>
            <div class="stat__label">Total Pendapatan</div>
            <div class="stat__value" style="font-size: 21px;">{{ rupiah($totalPendapatan) }}</div>
            <div class="stat__hint">{{ $pembayaran->count() }} transaksi</div>
        </div>
    </div>
    <div class="stat">
        <div class="stat__icon stat__icon--accent"><i class="bi bi-receipt"></i></div>
        <div>
            <div class="stat__label">Dari Paket</div>
            <div class="stat__value" style="font-size: 21px;">{{ rupiah($totalPaket) }}</div>
            <div class="stat__hint">langganan berdurasi</div>
        </div>
    </div>
    <div class="stat">
        <div class="stat__icon stat__icon--ok"><i class="bi bi-cash-stack"></i></div>
        <div>
            <div class="stat__label">Dari Latihan</div>
            <div class="stat__value" style="font-size: 21px;">{{ rupiah($totalLatihan) }}</div>
            <div class="stat__hint">{{ $kunjunganBerbayar }} kedatangan berbayar</div>
        </div>
    </div>
    <div class="stat">
        <div class="stat__icon stat__icon--accent"><i class="bi bi-door-open"></i></div>
        <div>
            <div class="stat__label">Jumlah Kunjungan</div>
            <div class="stat__value">{{ $jumlahKunjungan }}</div>
            <div class="stat__hint">check-in pada periode ini</div>
        </div>
    </div>
</div>

<div class="panel panel--flush">
    <div class="panel__head panel__head--padded">
        <div>
            <h3 class="panel__title">Rincian Pembayaran</h3>
            <p class="panel__desc">{{ tanggal_id($dari) }} &ndash; {{ tanggal_id($sampai) }}</p>
        </div>
    </div>
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Member</th>
                    <th>Keterangan</th>
                    <th class="num">Jumlah</th>
                    <th>Metode</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pembayaran as $row)
                @php
                    $namaMember = $row->langganan->member->nama ?? $row->kunjungan->member->nama ?? 'Tamu';
                    $kodeMember = $row->langganan->member->kode_member ?? $row->kunjungan->member->kode_member ?? '-';
                @endphp
                <tr>
                    <td>{{ tanggal_id($row->tanggal_bayar) }}</td>
                    <td>
                        <strong style="font-weight: 600;">{{ $namaMember }}</strong><br>
                        <span style="font-size: 12px; color: var(--muted);">{{ $kodeMember }}</span>
                    </td>
                    <td>
                        @if ($row->kunjungan_id)
                            <span class="pill">Latihan</span>
                            <span style="font-size: 12px; color: var(--muted);">
                                {{ $row->kunjungan?->waktu_kunjungan?->format('d/m H:i') }}
                            </span>
                        @else
                            <span class="pill pill--muted">Paket</span>
                            <span style="font-size: 12px; color: var(--muted);">{{ $row->langganan->paket->nama ?? '-' }}</span>
                        @endif
                    </td>
                    <td class="num">{{ rupiah($row->jumlah) }}</td>
                    <td><span class="pill pill--muted">{{ $row->metode }}</span></td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="empty">Tidak ada pembayaran pada periode ini.</td>
                </tr>
                @endforelse
            </tbody>
            @if ($pembayaran->count())
            <tfoot>
                <tr>
                    <th colspan="3">Total</th>
                    <th class="num">{{ rupiah($totalPendapatan) }}</th>
                    <th></th>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>

<div class="panel panel--flush">
    <div class="panel__head panel__head--padded">
        <div>
            <h3 class="panel__title">Member Expired</h3>
            <p class="panel__desc">{{ $memberExpired->count() }} member perlu ditawari perpanjangan.</p>
        </div>
        <div class="panel__tools">
            <a href="{{ route('backend.member.index') }}" class="btn btn-ghost btn-sm">Data Member</a>
        </div>
    </div>
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>No HP</th>
                    <th>Berakhir</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($memberExpired as $row)
                <tr>
                    <td><span class="pill pill--muted">{{ $row->kode_member }}</span></td>
                    <td>{{ $row->nama }}</td>
                    <td>{{ $row->no_hp }}</td>
                    <td>{{ tanggal_id($row->langgananTerakhir->tanggal_berakhir ?? null) }}</td>
                    <td class="num">
                        <a href="{{ route('backend.langganan.create', ['member_id' => $row->id]) }}" class="btn btn-ghost btn-xs">Perpanjang</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="empty">Tidak ada member expired.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
