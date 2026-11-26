@extends('admin.layouts.app')

@section('content')
<div class="page-head">
    <div>
        <h1>{{ $judul }}</h1>
        <p>Ringkasan kondisi gym per {{ tanggal_id(now()) }}.</p>
    </div>
    <div style="display: flex; gap: 8px; flex-wrap: wrap;">
        <a href="{{ route('backend.kasir.index') }}" class="btn btn-accent">
            <i class="bi bi-cash-coin"></i> Kasir Latihan
        </a>
        <a href="{{ route('backend.langganan.create') }}" class="btn btn-ghost">
            <i class="bi bi-plus-lg"></i> Catat Langganan
        </a>
    </div>
</div>

<div class="stats">
    <div class="stat">
        <div class="stat__icon stat__icon--ok"><i class="bi bi-person-check"></i></div>
        <div>
            <div class="stat__label">Member Aktif</div>
            <div class="stat__value">{{ $memberAktif }}</div>
            <div class="stat__hint">dari {{ $memberTotal }} terdaftar</div>
        </div>
    </div>
    <div class="stat">
        <div class="stat__icon stat__icon--accent"><i class="bi bi-door-open"></i></div>
        <div>
            <div class="stat__label">Masuk hari ini</div>
            <div class="stat__value">{{ $kunjunganHariIni }}</div>
            <div class="stat__hint">{{ $kunjunganLatihanHariIni }} bayar per latihan</div>
        </div>
    </div>
    <div class="stat">
        <div class="stat__icon stat__icon--warn"><i class="bi bi-hourglass-split"></i></div>
        <div>
            <div class="stat__label">Habis dalam 7 hari</div>
            <div class="stat__value">{{ $akanExpired->count() }}</div>
            <div class="stat__hint">perlu diingatkan</div>
        </div>
    </div>
    <div class="stat">
        <div class="stat__icon"><i class="bi bi-cash-stack"></i></div>
        <div>
            <div class="stat__label">Pendapatan bulan ini</div>
            <div class="stat__value" style="font-size: 21px;">{{ rupiah($pendapatanBulanIni) }}</div>
            <div class="stat__hint">{{ rupiah($pendapatanLatihanBulanIni ?? 0) }} dari latihan</div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="panel panel--flush">
            <div class="panel__head panel__head--padded">
                <div>
                    <h3 class="panel__title">Segera habis</h3>
                    <p class="panel__desc">Masa aktif berakhir dalam 7 hari ke depan.</p>
                </div>
                <div class="panel__tools">
                    <a href="{{ route('backend.member.index') }}" class="btn btn-ghost btn-sm">Semua member</a>
                </div>
            </div>
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Member</th>
                            <th>Berakhir</th>
                            <th>Sisa</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($akanExpired as $row)
                        <tr>
                            <td>
                                <strong style="font-weight: 600;">{{ $row->nama }}</strong><br>
                                <span style="font-size: 12px; color: var(--muted);">{{ $row->kode_member }}</span>
                            </td>
                            <td>{{ tanggal_id($row->langgananTerakhir->tanggal_berakhir) }}</td>
                            <td><span class="pill pill--warn">{{ $row->sisa_hari }} hari</span></td>
                            <td class="num">
                                <a href="{{ route('backend.langganan.create', ['member_id' => $row->id]) }}" class="btn btn-ghost btn-xs">
                                    Perpanjang
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="empty">Tidak ada member yang mendekati expired.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="panel panel--flush">
            <div class="panel__head panel__head--padded">
                <div>
                    <h3 class="panel__title">Masuk terakhir</h3>
                    <p class="panel__desc">{{ $kunjunganTerakhir->count() }} check-in terbaru.</p>
                </div>
                <div class="panel__tools">
                    <a href="{{ route('backend.kunjungan.index') }}" class="btn btn-ink btn-sm">
                        <i class="bi bi-qr-code-scan"></i> Check-in
                    </a>
                </div>
            </div>
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Member</th>
                            <th class="num">Waktu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($kunjunganTerakhir as $row)
                        <tr>
                            <td>
                                <strong style="font-weight: 600;">{{ $row->member->nama ?? '-' }}</strong><br>
                                <span style="font-size: 12px; color: var(--muted);">{{ $row->member->kode_member ?? '-' }}</span>
                            </td>
                            <td class="num">{{ $row->waktu_kunjungan->format('d/m H:i') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="empty">Belum ada kunjungan tercatat.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
