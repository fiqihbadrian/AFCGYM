@extends('admin.layouts.app')

@section('content')
<div class="page-head">
    <div>
        <h1>{{ $judul }}</h1>
        <p>Cari nama member, terima uangnya, lalu catat masuk. Untuk tamu yang belum terdaftar, pakai tombol Daftarkan Tamu.</p>
    </div>
</div>

@if (session('error'))
<div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="row g-4">
    <div class="col-lg-5">
        <div class="panel">
            <h3 class="panel__title" style="margin-bottom: 6px;">Cari member</h3>
            <p class="panel__desc" style="margin-bottom: 14px;">Ketik sebagian nama, kode kartu, atau no HP.</p>
            <form action="{{ route('backend.kasir.index') }}" method="get">
                <div class="field">
                    <input type="text" name="q" value="{{ $cari }}" class="form-control"
                           placeholder="contoh: budi" autofocus autocomplete="off"
                           style="font-size: 17px; padding: 12px 14px;">
                </div>
                <button type="submit" class="btn btn-accent w-100 justify-content-center">
                    <i class="bi bi-search"></i> Cari
                </button>
            </form>

            @if ($cari !== '' && $hasil->isEmpty())
            <div class="empty" style="margin-top: 16px;">
                Tidak ada member bernama "{{ $cari }}".
            </div>
            @endif

            @if ($hasil->isNotEmpty())
            <div style="margin-top: 18px; display: grid; gap: 8px;">
                @foreach ($hasil as $row)
                <a href="{{ route('backend.kasir.index', ['member_id' => $row->id]) }}"
                   class="kasir-pick {{ $terpilih && $terpilih->id === $row->id ? 'kasir-pick--on' : '' }}">
                    <span class="kasir-pick__nama">{{ $row->nama }}</span>
                    <span class="kasir-pick__meta">{{ $row->kode_member }}</span>
                    <span class="pill {{ $row->aktif ? 'pill--ok' : 'pill--muted' }}">
                        {{ $row->aktif ? 'Paket aktif' : 'Belum berpaket' }}
                    </span>
                </a>
                @endforeach
            </div>
            @endif
        </div>

        <div class="panel">
            <h3 class="panel__title" style="margin-bottom: 6px;">Tamu belum terdaftar</h3>
            <p class="panel__desc" style="margin-bottom: 14px;">Cukup nama. No HP boleh dikosongkan.</p>
            <form action="{{ route('backend.kasir.tamu') }}" method="post">
                @csrf
                <div class="field">
                    <label for="nama_tamu">Nama Tamu</label>
                    <input type="text" id="nama_tamu" name="nama" class="form-control @error('nama') is-invalid @enderror"
                           value="{{ old('nama', $cari ?: '') }}" autocomplete="off">
                    @error('nama')
                    <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="field">
                    <label for="no_hp_tamu">No HP <span style="color: var(--muted); font-weight: 400;">opsional</span></label>
                    <input type="text" id="no_hp_tamu" name="no_hp" class="form-control" autocomplete="off">
                </div>
                <button type="submit" class="btn btn-ghost w-100 justify-content-center">
                    <i class="bi bi-person-plus"></i> Daftarkan Tamu
                </button>
            </form>
        </div>
    </div>

    <div class="col-lg-7">
        @if ($terpilih)
        <div class="panel">
            <h3 class="panel__title" style="margin-bottom: 4px;">2. {{ $terpilih->nama }}</h3>
            <p class="panel__desc" style="margin-bottom: 16px;">
                {{ $terpilih->kode_member }}
                @if ($terpilih->no_hp) &middot; {{ $terpilih->no_hp }} @endif
            </p>

            @if ($terpilih->aktif)
                <div class="kasir-free">
                    <div style="font-size: 34px;"><i class="bi bi-check-circle-fill"></i></div>
                    <div>
                        <strong>Punya paket aktif</strong>
                        <p>Masuk gratis. Sisa {{ $terpilih->sisa_hari }} hari —
                           {{ $terpilih->langgananTerakhir->paket->nama ?? '-' }}
                           s/d {{ tanggal_id($terpilih->langgananTerakhir->tanggal_berakhir) }}</p>
                    </div>
                </div>
                <form action="{{ route('backend.kasir.store') }}" method="post" style="margin-top: 16px;">
                    @csrf
                    <input type="hidden" name="member_id" value="{{ $terpilih->id }}">
                    <input type="hidden" name="metode" value="Tunai">
                    <button type="submit" class="btn btn-accent w-100 justify-content-center" style="padding: 16px; font-size: 16px;">
                        <i class="bi bi-box-arrow-in-right"></i> Catat Masuk — Gratis
                    </button>
                </form>
            @elseif ($terpilih->sudah_bayar_hari_ini)
                <div class="kasir-free">
                    <div style="font-size: 34px;"><i class="bi bi-check-circle-fill"></i></div>
                    <div>
                        <strong>Sudah bayar latihan hari ini</strong>
                        <p>Tidak perlu ditagih lagi. Silakan masuk.</p>
                    </div>
                </div>
                <form action="{{ route('backend.kasir.store') }}" method="post" style="margin-top: 16px;">
                    @csrf
                    <input type="hidden" name="member_id" value="{{ $terpilih->id }}">
                    <input type="hidden" name="metode" value="Tunai">
                    <button type="submit" class="btn btn-accent w-100 justify-content-center" style="padding: 16px; font-size: 16px;">
                        <i class="bi bi-box-arrow-in-right"></i> Catat Masuk
                    </button>
                </form>
            @else
                <form action="{{ route('backend.kasir.store') }}" method="post" id="form-latihan">
                    @csrf
                    <input type="hidden" name="member_id" value="{{ $terpilih->id }}">

                    @if ($paketLatihan->isEmpty())
                        <div class="alert alert-warning">
                            Belum ada paket jenis <strong>latihan</strong>. Tambahkan dulu di menu Paket.
                        </div>
                    @else
                    <div class="field">
                        <label>Tarif Latihan</label>
                        <select name="paket_id" id="paket_id" class="form-select" style="font-size: 16px; padding: 11px 13px;">
                            @foreach ($paketLatihan as $p)
                            <option value="{{ $p->id }}" data-harga="{{ (int) $p->harga }}">
                                {{ $p->nama }} — {{ rupiah($p->harga) }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="kasir-total">
                        <span>Harus dibayar</span>
                        <strong id="label-tarif">{{ rupiah($paketLatihan->first()->harga) }}</strong>
                    </div>

                    <div class="field">
                        <label for="uang_bayar">Uang Diterima</label>
                        <input type="number" name="uang_bayar" id="uang_bayar" class="form-control"
                               min="0" step="500" placeholder="contoh: 50000"
                               style="font-size: 20px; padding: 12px 14px; letter-spacing: .02em;">
                        <div class="field-hint">Kosongkan kalau uang pas.</div>
                    </div>

                    <div class="kasir-change" id="box-kembalian" style="display: none;">
                        <span>Kembalian</span>
                        <strong id="label-kembalian">Rp 0</strong>
                    </div>

                    <div class="field">
                        <label for="metode">Metode</label>
                        <select name="metode" id="metode" class="form-select">
                            @foreach (['Tunai', 'QRIS', 'Transfer'] as $m)
                            <option value="{{ $m }}">{{ $m }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="btn btn-accent w-100 justify-content-center"
                            style="padding: 18px; font-size: 17px; margin-top: 6px;">
                        <i class="bi bi-cash-coin"></i> Bayar &amp; Masuk
                    </button>

                    <p style="font-size: 12px; color: var(--muted); margin: 12px 0 0; text-align: center;">
                        Berlaku sampai gym tutup, tanpa batas jam. Datang lagi hari ini tidak ditagih ulang.
                    </p>
                    @endif
                </form>

                @if ($paketPaket->isNotEmpty())
                <div class="kasir-upsell">
                    <i class="bi bi-lightbulb"></i>
                    <div>
                        Mau lebih hemat?
                        <a href="{{ route('backend.langganan.create', ['member_id' => $terpilih->id]) }}">
                            Tawarkan paket {{ $paketPaket->first()->nama }} ({{ rupiah($paketPaket->first()->harga) }})
                        </a>
                    </div>
                </div>
                @endif
            @endif
        </div>
        @else
        <div class="panel">
            <div class="empty" style="padding: 48px 16px;">
                <div style="font-size: 32px; margin-bottom: 10px; opacity: .35;"><i class="bi bi-person-badge"></i></div>
                Belum ada yang dipilih. Cari nama di sebelah kiri untuk mulai.
            </div>
        </div>
        @endif

        <div class="panel panel--flush">
            <div class="panel__head panel__head--padded">
                <div>
                    <h3 class="panel__title">Masuk hari ini</h3>
                    <p class="panel__desc">{{ $kunjunganHariIni->count() }} orang tercatat.</p>
                </div>
            </div>
            <div class="table-wrap" style="max-height: 380px; overflow-y: auto;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Jam</th>
                            <th>Nama</th>
                            <th>Bayar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($kunjunganHariIni as $row)
                        <tr>
                            <td style="white-space: nowrap;">{{ $row->waktu_kunjungan->format('H:i') }}</td>
                            <td>
                                <strong style="font-weight: 600;">{{ $row->member->nama ?? '-' }}</strong><br>
                                <span style="font-size: 12px; color: var(--muted);">{{ $row->member->kode_member ?? '-' }}</span>
                            </td>
                            <td style="white-space: nowrap;">
                                @if ((float) $row->tarif > 0)
                                    {{ rupiah($row->tarif) }}
                                @else
                                    <span class="pill pill--ok">{{ $row->jenis === 'latihan' ? 'Sudah bayar' : 'Paket' }}</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="empty">Belum ada kunjungan hari ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    var paket  = document.getElementById('paket_id');
    var uang   = document.getElementById('uang_bayar');
    var boxKem = document.getElementById('box-kembalian');
    var lTarif = document.getElementById('label-tarif');
    var lKem   = document.getElementById('label-kembalian');

    function rupiah(n) {
        return 'Rp ' + Math.round(n).toLocaleString('id-ID');
    }

    function tarif() {
        var o = paket && paket.options[paket.selectedIndex];
        return o ? parseInt(o.getAttribute('data-harga'), 10) : 0;
    }

    function hitung() {
        var t = tarif();
        lTarif.textContent = rupiah(t);

        var u = parseFloat(uang.value);
        if (isNaN(u) || uang.value === '') {
            boxKem.style.display = 'none';
            return;
        }

        boxKem.style.display = 'flex';
        var kembali = u - t;
        if (kembali < 0) {
            lKem.textContent = 'Kurang ' + rupiah(-kembali);
            lKem.style.color = '#d9534f';
        } else {
            lKem.textContent = rupiah(kembali);
            lKem.style.color = '';
        }
    }

    if (paket) paket.addEventListener('change', hitung);
    if (uang)  uang.addEventListener('input', hitung);
    hitung();
})();
</script>
@endpush
