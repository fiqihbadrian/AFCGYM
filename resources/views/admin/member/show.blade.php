@extends('admin.layouts.app')

@section('content')
<div class="page-head">
    <div>
        <h1>{{ $judul }}</h1>
        <p>Kode {{ $member->kode_member }} &middot; terdaftar {{ tanggal_id($member->tanggal_daftar) }}</p>
    </div>
    <div style="display: flex; gap: 8px; flex-wrap: wrap;">
        <a href="{{ route('backend.member.kartu', $member->id) }}" class="btn btn-accent">
            <i class="bi bi-printer"></i> Cetak Kartu
        </a>
        <a href="{{ route('backend.member.edit', $member->id) }}" class="btn btn-ghost">
            <i class="bi bi-pencil"></i> Ubah
        </a>
        <a href="{{ route('backend.member.index') }}" class="btn btn-ink">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="panel">
            <div class="profile">
                <img src="{{ $member->foto ? asset('storage/img-member/' . $member->foto) : asset('image/img-default.jpg') }}"
                     alt="{{ $member->nama }}">
                <h3>{{ $member->nama }}</h3>
                <div class="kode">{{ $member->kode_member }}</div>
                <div class="ring {{ $member->aktif ? 'pill--ok' : 'pill--danger' }}">
                    <i class="bi bi-{{ $member->aktif ? 'patch-check' : 'x-circle' }}"></i>
                    {{ $member->aktif ? 'Member Aktif' : 'Tidak Aktif' }}
                </div>
                <p style="color: var(--muted); font-size: 13px; margin: 6px 0 16px;">
                    Sisa masa aktif <strong style="color: var(--ink);">{{ $member->sisa_hari }} hari</strong>
                </p>
                <a href="{{ route('backend.langganan.create', ['member_id' => $member->id]) }}" class="btn btn-accent w-100 justify-content-center">
                    <i class="bi bi-arrow-repeat"></i> Catat Perpanjangan
                </a>
                <a href="{{ route('backend.kasir.index', ['member_id' => $member->id]) }}" class="btn btn-ghost w-100 justify-content-center" style="margin-top: 8px;">
                    <i class="bi bi-cash-coin"></i> Bayar Latihan
                </a>
            </div>
        </div>

        <div class="panel">
            <h3 class="panel__title" style="margin-bottom: 10px;">Kontak</h3>
            <dl class="dl">
                <div><dt>NIK</dt><dd>{{ $member->nik ?: '-' }}</dd></div>
                <div><dt>No HP</dt><dd>{{ $member->no_hp }}</dd></div>
                <div><dt>Alamat</dt><dd>{{ $member->alamat ?: '-' }}</dd></div>
                <div><dt>Akun login</dt><dd>{{ $akun->email ?? '-' }}</dd></div>
            </dl>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="panel panel--flush">
            <div class="panel__head panel__head--padded">
                <div>
                    <h3 class="panel__title">Riwayat Langganan</h3>
                    <p class="panel__desc">{{ $member->langganan->count() }} transaksi tercatat.</p>
                </div>
            </div>
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Paket</th>
                            <th>Mulai</th>
                            <th>Berakhir</th>
                            <th class="num">Bayar</th>
                            <th>Metode</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($member->langganan->sortByDesc('tanggal_mulai') as $row)
                        <tr>
                            <td>
                                <strong style="font-weight: 600;">{{ $row->paket->nama ?? '-' }}</strong><br>
                                <span style="font-size: 12px; color: var(--muted);">{{ $row->paket->durasi_hari ?? 0 }} hari</span>
                            </td>
                            <td>{{ tanggal_id($row->tanggal_mulai) }}</td>
                            <td>{{ tanggal_id($row->tanggal_berakhir) }}</td>
                            <td class="num">{{ rupiah($row->harga_bayar) }}</td>
                            <td>
                                @if ($row->pembayaran->first())
                                <span class="pill pill--muted">{{ $row->pembayaran->first()->metode }}</span>
                                @else
                                -
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="empty">Belum ada langganan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
