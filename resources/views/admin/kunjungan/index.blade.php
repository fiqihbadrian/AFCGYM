@extends('admin.layouts.app')

@section('content')
<div class="page-head">
    <div>
        <h1>{{ $judul }}</h1>
        <p>Catat kehadiran member. Scan QR kartu member atau ketik kode manual.</p>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-5">
        <div class="panel">
            <h3 class="panel__title" style="margin-bottom: 6px;">Check-in</h3>
            <p class="panel__desc" style="margin-bottom: 16px;">
                Halaman ini untuk member yang <strong>punya paket aktif</strong> — masuk gratis.
                Yang belum berpaket otomatis diarahkan ke Kasir supaya pembayarannya tercatat.
            </p>
            <form action="{{ route('backend.kunjungan.store') }}" method="post">
                @csrf
                <div class="field">
                    <label for="kode_member">Kode Member</label>
                    <input type="text" id="kode_member" name="kode_member" value="{{ old('kode_member') }}"
                           class="form-control @error('kode_member') is-invalid @enderror"
                           placeholder="AFC-0001" autofocus autocomplete="off"
                           style="font-size: 17px; padding: 12px 14px; letter-spacing: .04em;">
                    @error('kode_member')
                    <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-accent w-100 justify-content-center">
                    <i class="bi bi-check2-circle"></i> Catat Kunjungan
                </button>
            </form>
        </div>

        <div class="panel">
            <h3 class="panel__title" style="margin-bottom: 10px;">Cara check-in</h3>
            <ol style="margin: 0; padding-left: 18px; font-size: 13px; color: var(--muted); line-height: 1.9;">
                <li>Member menyerahkan kartu fisiknya ke petugas.</li>
                <li>Petugas memindai QR pada kartu (isinya kode member) atau mengetik kodenya.</li>
                <li>Tekan Catat Kunjungan. Kalau member belum berpaket, layar pindah ke Kasir.</li>
            </ol>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="panel panel--flush">
            <div class="panel__head panel__head--padded">
                <div>
                    <h3 class="panel__title">50 Kunjungan Terakhir</h3>
                    <p class="panel__desc">Urut dari yang paling baru.</p>
                </div>
            </div>
            <div class="table-wrap" style="max-height: 560px; overflow-y: auto;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Waktu</th>
                            <th>Member</th>
                            <th>Dicatat oleh</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($kunjungan as $row)
                        <tr>
                            <td style="white-space: nowrap;">{{ $row->waktu_kunjungan->format('d/m/Y H:i') }}</td>
                            <td>
                                <strong style="font-weight: 600;">{{ $row->member->nama ?? '-' }}</strong><br>
                                <span style="font-size: 12px; color: var(--muted);">{{ $row->member->kode_member ?? '-' }}</span>
                            </td>
                            <td>{{ $row->pencatat->nama ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="empty">Belum ada kunjungan tercatat.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
