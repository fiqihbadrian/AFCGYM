@extends('admin.layouts.app')

@section('content')
<div class="page-head">
    <div>
        <h1>{{ $judul }}</h1>
        <p>{{ $langganan->member->nama ?? '-' }} &middot; {{ $langganan->paket->nama ?? '-' }}</p>
    </div>
    <a href="{{ route('backend.langganan.index') }}" class="btn btn-ghost">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="panel">
            <h3 class="panel__title" style="margin-bottom: 10px;">Data Langganan</h3>
            <dl class="dl">
                <div><dt>Member</dt><dd>{{ $langganan->member->nama ?? '-' }}</dd></div>
                <div><dt>Kode Member</dt><dd>{{ $langganan->member->kode_member ?? '-' }}</dd></div>
                <div><dt>Paket</dt><dd>{{ $langganan->paket->nama ?? '-' }}</dd></div>
                <div><dt>Mulai</dt><dd>{{ tanggal_id($langganan->tanggal_mulai) }}</dd></div>
                <div><dt>Berakhir</dt><dd>{{ tanggal_id($langganan->tanggal_berakhir) }}</dd></div>
                <div><dt>Harga</dt><dd style="font-weight: 700;">{{ rupiah($langganan->harga_bayar) }}</dd></div>
                <div><dt>Dicatat oleh</dt><dd>{{ $langganan->pembuat->nama ?? '-' }}</dd></div>
            </dl>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="panel panel--flush">
            <div class="panel__head panel__head--padded">
                <div>
                    <h3 class="panel__title">Pembayaran</h3>
                    <p class="panel__desc">{{ $langganan->pembayaran->count() }} catatan pembayaran.</p>
                </div>
            </div>
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th class="num">Jumlah</th>
                            <th>Metode</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($langganan->pembayaran as $row)
                        <tr>
                            <td>{{ tanggal_id($row->tanggal_bayar) }}</td>
                            <td class="num">{{ rupiah($row->jumlah) }}</td>
                            <td><span class="pill pill--muted">{{ $row->metode }}</span></td>
                            <td>{{ $row->keterangan ?: '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="empty">Belum ada pembayaran.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
