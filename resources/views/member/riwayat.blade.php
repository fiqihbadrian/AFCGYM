@extends('member.layouts.app')

@section('content')
<div class="page-head">
    <div>
        <h1>{{ $judul }}</h1>
        <p>Semua langganan dan kunjungan kamu di AFCGym.</p>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="panel panel--flush">
            <div class="panel__head panel__head--padded">
                <div>
                    <h3 class="panel__title">Riwayat Langganan</h3>
                    <p class="panel__desc">{{ $langganan->count() }} transaksi.</p>
                </div>
            </div>
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Paket</th>
                            <th>Periode</th>
                            <th class="num">Bayar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($langganan as $row)
                        <tr>
                            <td>{{ $row->paket->nama ?? '-' }}</td>
                            <td>
                                {{ tanggal_id($row->tanggal_mulai) }}<br>
                                <span style="font-size: 12px; color: var(--muted);">sampai {{ tanggal_id($row->tanggal_berakhir) }}</span>
                            </td>
                            <td class="num">{{ rupiah($row->harga_bayar) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="empty">Belum ada langganan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="panel panel--flush">
            <div class="panel__head panel__head--padded">
                <div>
                    <h3 class="panel__title">Riwayat Kunjungan</h3>
                    <p class="panel__desc">{{ $kunjungan->count() }} kunjungan tercatat.</p>
                </div>
            </div>
            <div class="table-wrap" style="max-height: 520px; overflow-y: auto;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Waktu</th>
                            <th class="num">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($kunjungan as $row)
                        <tr>
                            <td>{{ $row->waktu_kunjungan->format('d/m/Y H:i') }}</td>
                            <td class="num"><span class="pill pill--ok">Hadir</span></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="empty">Belum ada kunjungan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
