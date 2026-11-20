@extends('member.layouts.app')

@section('content')
@php
    $berakhir = $langgananTerakhir->tanggal_berakhir ?? null;
@endphp

<section class="hero">
    <div class="hero__eyebrow">Member area</div>
    <h1>Halo, {{ $member->nama }}</h1>
    <p>
        @if (! $langgananTerakhir)
            Kamu belum punya langganan. Daftar paket dulu di resepsionis.
        @elseif ($member->aktif)
            Keanggotaan kamu aktif. Sisa {{ $member->sisa_hari }} hari lagi.
        @else
            Keanggotaan kamu sudah berakhir pada {{ tanggal_id($berakhir) }}. Perpanjang di resepsionis.
        @endif
    </p>

    <div class="hero__meta">
        <div>
            <small>Kode member</small>
            <strong style="font-family: ui-monospace, monospace; letter-spacing: .08em;">{{ $member->kode_member }}</strong>
        </div>
        <div>
            <small>Status</small>
            <strong>
                @if (! $langgananTerakhir)
                    Belum aktif
                @elseif ($member->aktif)
                    Aktif
                @else
                    Expired
                @endif
            </strong>
        </div>
        <div>
            <small>{{ $member->aktif ? 'Berakhir' : 'Telah berakhir' }}</small>
            <strong>{{ $berakhir ? tanggal_id($berakhir) : '-' }}</strong>
        </div>
        <div class="hero__action">
            <a href="{{ route('member.kartu') }}" class="btn btn-accent">
                <i class="bi bi-qr-code"></i> Tunjukkan Kartu
            </a>
        </div>
    </div>
</section>

<div class="mini-grid">
    <div class="mini">
        <div class="mini__label"><i class="bi bi-hourglass-split"></i> Sisa hari</div>
        <div class="mini__value" style="color: {{ $member->aktif ? 'var(--ok)' : 'var(--danger)' }};">
            {{ $member->sisa_hari }}<span style="font-size: 13px; font-weight: 600; color: var(--muted);"> hari</span>
        </div>
        <div class="mini__hint">{{ $langgananTerakhir ? 'dari paket ' . ($langgananTerakhir->paket->nama ?? '-') : 'belum ada langganan' }}</div>
    </div>
    <div class="mini">
        <div class="mini__label"><i class="bi bi-door-open"></i> Total kunjungan</div>
        <div class="mini__value">{{ $jumlahKunjungan }}<span style="font-size: 13px; font-weight: 600; color: var(--muted);"> kali</span></div>
        <div class="mini__hint">sejak pertama terdaftar</div>
    </div>
    <div class="mini">
        <div class="mini__label"><i class="bi bi-calendar-check"></i> Terakhir datang</div>
        <div class="mini__value" style="font-size: 17px;">
            {{ $kunjungan->first() ? $kunjungan->first()->waktu_kunjungan->format('d/m/Y') : '-' }}
        </div>
        <div class="mini__hint">
            {{ $kunjungan->first() ? 'pukul ' . $kunjungan->first()->waktu_kunjungan->format('H:i') : 'belum pernah check-in' }}
        </div>
    </div>
</div>

<div class="panel">
    <h3 class="panel__title" style="margin-bottom: 12px;">Paket yang sedang aktif</h3>
    @if ($langgananTerakhir)
    <dl class="dl">
        <div><dt>Paket</dt><dd>{{ $langgananTerakhir->paket->nama ?? '-' }}</dd></div>
        <div><dt>Durasi</dt><dd>{{ $langgananTerakhir->paket->durasi_hari ?? '-' }} hari</dd></div>
        <div><dt>Mulai</dt><dd>{{ tanggal_id($langgananTerakhir->tanggal_mulai) }}</dd></div>
        <div><dt>Berakhir</dt><dd>{{ tanggal_id($langgananTerakhir->tanggal_berakhir) }}</dd></div>
        <div><dt>Dibayar</dt><dd>{{ rupiah($langgananTerakhir->harga_bayar) }}</dd></div>
    </dl>
    @else
    <p style="color: var(--muted); font-size: 13px; margin: 0;">Belum ada data langganan.</p>
    @endif
</div>

<div class="panel panel--flush">
    <div class="panel__head panel__head--padded">
        <div>
            <h3 class="panel__title">Kunjungan terakhir</h3>
            <p class="panel__desc">Lima check-in terbaru kamu.</p>
        </div>
        <div class="panel__tools">
            <a href="{{ route('member.riwayat') }}" class="btn btn-ghost btn-sm">Lihat semua</a>
        </div>
    </div>
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Waktu</th>
                    <th>Hari</th>
                    <th class="num">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($kunjungan as $row)
                <tr>
                    <td>{{ $row->waktu_kunjungan->format('d/m/Y H:i') }}</td>
                    <td style="color: var(--muted);">{{ $row->waktu_kunjungan->translatedFormat('l') }}</td>
                    <td class="num"><span class="pill pill--ok">Hadir</span></td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="empty">Belum ada kunjungan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
