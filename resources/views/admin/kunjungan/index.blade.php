@extends('admin.layouts.app')

@section('content')
<div class="page-head">
    <div>
        <h1>{{ $judul }}</h1>
        <p>Scan kartu, lihat status, baru catat.</p>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-5">
        <div class="panel">
            <form action="{{ route('backend.kunjungan.cek') }}" method="post" id="form-cek">
                @csrf

                <div class="scan-tabs" role="tablist">
                    <button type="button" class="scan-tab is-on" data-mode="kamera">
                        <i class="bi bi-camera-video"></i> Kamera
                    </button>
                    <button type="button" class="scan-tab" data-mode="alat">
                        <i class="bi bi-upc-scan"></i> Scanner
                    </button>
                    <button type="button" class="scan-tab" data-mode="manual">
                        <i class="bi bi-keyboard"></i> Ketik
                    </button>
                </div>

                {{-- Tab 1: kamera + jsQR --}}
                <div class="scan-pane" data-pane="kamera">
                    <div class="scan-cam">
                        <video id="video" playsinline muted></video>
                        <div class="scan-cam__frame"></div>
                        <div class="scan-cam__status" id="scan-status">Menyiapkan kamera…</div>
                    </div>
                    <canvas id="scan-canvas" class="d-none"></canvas>
                </div>

                {{-- Tab 2: barcode scanner (keyboard wedge) --}}
                <div class="scan-pane d-none" data-pane="alat">
                    <p class="scan-note">
                        Arahkan alat scanner ke QR pada kartu. Karakter yang masuk langsung
                        dicek otomatis begitu alat mengirim Enter.
                    </p>
                </div>

                {{-- Tab 3: ketik manual --}}
                <div class="scan-pane d-none" data-pane="manual">
                    <p class="scan-note">
                        Isi kode yang tertera di bawah QR kartu, lalu tekan Cek status.
                    </p>
                </div>

                <div class="field">
                    <label for="kode_member">Kode kartu</label>
                    <input type="text" id="kode_member" name="kode_member" value="{{ old('kode_member') }}"
                           class="form-control @error('kode_member') is-invalid @enderror"
                           placeholder="AFC-0001" autocomplete="off" autocapitalize="off" spellcheck="false"
                           style="font-size: 17px; padding: 12px 14px; letter-spacing: .05em;">
                    @error('kode_member')
                    <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-ink w-100 justify-content-center">
                    <i class="bi bi-search"></i> Cek status
                </button>

                <p class="scan-hint">Arahkan QR ke kotak. Terbaca langsung dicek, belum dicatat.</p>
            </form>
        </div>

        {{-- Hasil cek status: konfirmasi sebelum dicatat --}}
        @if ($hasilCek)
        @php $m = $hasilCek['member']; @endphp
        <div class="panel">
            @if (! $hasilCek['aktif'])
                <div class="cek-card cek-card--warn">
                    <div class="cek-card__who">{{ $m->nama }}</div>
                    <div class="cek-card__meta">
                        {{ $m->kode_member }} &middot; tidak punya paket aktif<br>
                        Check-in gratis hanya untuk member berpaket. Lanjutkan ke Kasir supaya
                        pembayaran per latihan tercatat.
                    </div>
                    <div class="cek-card__actions">
                        <a href="{{ route('backend.kasir.index', ['member_id' => $m->id]) }}" class="btn btn-accent">
                            <i class="bi bi-cash-coin"></i> Lanjut ke Kasir
                        </a>
                    </div>
                </div>
            @elseif ($hasilCek['sudah_masuk'])
                <div class="cek-card cek-card--warn">
                    <div class="cek-card__who">{{ $m->nama }}</div>
                    <div class="cek-card__meta">
                        {{ $m->kode_member }} &middot; sudah tercatat masuk hari ini<br>
                        Paket {{ $hasilCek['paket'] ?? '-' }} &middot; sisa {{ $hasilCek['sisa_hari'] }} hari
                    </div>
                    <div class="cek-card__actions">
                        <form method="POST" action="{{ route('backend.kunjungan.store') }}">
                            @csrf
                            <input type="hidden" name="kode_member" value="{{ $m->kode_member }}">
                            <button type="submit" class="btn btn-ink">
                                <i class="bi bi-arrow-repeat"></i> Tetap catat lagi
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <div class="cek-card cek-card--ok">
                    <div class="cek-card__who">{{ $m->nama }}</div>
                    <div class="cek-card__meta">
                        {{ $m->kode_member }} &middot;
                        <span class="pill pill--ok">Aktif</span> sisa {{ $hasilCek['sisa_hari'] }} hari<br>
                        Paket {{ $hasilCek['paket'] ?? '-' }} &middot; masuk gratis
                    </div>
                    <div class="cek-card__actions">
                        <form method="POST" action="{{ route('backend.kunjungan.store') }}">
                            @csrf
                            <input type="hidden" name="kode_member" value="{{ $m->kode_member }}">
                            <button type="submit" class="btn btn-accent">
                                <i class="bi bi-check2-circle"></i> Catat kunjungan
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
        @endif

        <div class="panel">
            <h3 class="panel__title" style="margin-bottom: 10px;">Cara check-in</h3>
            <ol style="margin: 0; padding-left: 18px; font-size: 13px; color: var(--muted); line-height: 1.9;">
                <li>Member menyerahkan kartu fisiknya ke petugas.</li>
                <li>Petugas memindai QR pada kartu (isinya kode member) atau mengetik kodenya.</li>
                <li>Tekan Cek status untuk melihat masa aktif.</li>
                <li>Kalau member belum berpaket, layar pindah ke Kasir.</li>
            </ol>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="panel" style="margin-bottom: 22px;">
            <div style="display: flex; align-items: center; gap: 14px;">
                <div style="font-size: 30px; font-weight: 800; letter-spacing: -.03em; line-height: 1;">
                    {{ $masukHariIni }}
                </div>
                <div style="font-size: 12.5px; color: var(--muted); line-height: 1.5;">
                    orang sudah masuk hari ini
                </div>
            </div>
        </div>

        <div class="panel panel--flush">
            <div class="panel__head panel__head--padded">
                <div>
                    <h3 class="panel__title">Kunjungan terakhir</h3>
                    <p class="panel__desc">50 catatan terbaru.</p>
                </div>
            </div>
            <div class="table-wrap" style="max-height: 560px; overflow-y: auto;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Waktu</th>
                            <th>Member</th>
                            <th>Jenis</th>
                            <th>Petugas</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($kunjungan as $row)
                        <tr>
                            <td style="white-space: nowrap;">{{ $row->waktu_kunjungan->format('d/m H:i') }}</td>
                            <td>
                                <strong style="font-weight: 600;">{{ $row->member->nama ?? '-' }}</strong><br>
                                <span style="font-size: 12px; color: var(--muted);">{{ $row->member->kode_member ?? '-' }}</span>
                            </td>
                            <td>
                                @if ($row->jenis === 'latihan')
                                    <span class="pill pill--warn">latihan</span>
                                @else
                                    <span class="pill pill--ok">paket</span>
                                @endif
                            </td>
                            <td>{{ $row->pencatat->nama ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="empty">Belum ada kunjungan tercatat.</td>
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
<script src="{{ asset('vendor/jsQR.min.js') }}"></script>
<script>
(function () {
    const slider  = document.querySelectorAll('.scan-tab');
    const panes   = document.querySelectorAll('.scan-pane');
    const input   = document.getElementById('kode_member');
    const form    = document.getElementById('form-cek');
    const video   = document.getElementById('video');
    const canvas  = document.getElementById('scan-canvas');
    const status  = document.getElementById('scan-status');

    let stream   = null;
    let jalan    = false;
    let sudahKirim = false;
    let mode     = 'kamera';

    function pesan(teks) {
        if (status) status.textContent = teks;
    }

    function hentikanKamera() {
        jalan = false;
        if (stream) {
            stream.getTracks().forEach(t => t.stop());
            stream = null;
        }
        if (video) video.srcObject = null;
    }

    async function mulaiKamera() {
        if (jalan || !video) return;

        if (typeof jsQR !== 'function') {
            pesan('Library pemindai tidak termuat.');
            return;
        }
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            pesan('Browser ini tidak mendukung kamera. Pakai tab Ketik.');
            return;
        }

        pesan('Meminta izin kamera…');

        try {
            stream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: { ideal: 'environment' }, width: { ideal: 1280 } },
                audio: false
            });
        } catch (err) {
            pesan('Kamera tidak bisa dibuka. Pakai tab Scanner atau Ketik.');
            return;
        }

        video.srcObject = stream;
        video.setAttribute('playsinline', 'true');

        try { await video.play(); } catch (err) { /* diabaikan */ }

        jalan = true;
        pesan('Arahkan QR ke dalam kotak…');
        requestAnimationFrame(tick);
    }

    function tick() {
        if (!jalan) return;

        if (video.readyState === video.HAVE_ENOUGH_DATA && video.videoWidth) {
            const w = video.videoWidth;
            const h = video.videoHeight;

            canvas.width  = w;
            canvas.height = h;

            const ctx = canvas.getContext('2d', { willReadFrequently: true });
            ctx.drawImage(video, 0, 0, w, h);

            const data = ctx.getImageData(0, 0, w, h);
            const hasil = jsQR(data.data, w, h, { inversionAttempts: 'dontInvert' });

            if (hasil && hasil.data) {
                terima(hasil.data);
                return;
            }
        }

        requestAnimationFrame(tick);
    }

    /**
     * QR bisa berisi kode member saja (AFC-0001) atau URL kartu.
     * Ambil kode yang cocok dengan pola AFC-0000.
     */
    function bersihkan(teks) {
        const cocok = String(teks).toUpperCase().match(/AFC-\d{1,6}/);
        return cocok ? cocok[0] : String(teks).trim();
    }

    function terima(teks) {
        if (sudahKirim) return;
        sudahKirim = true;

        const kode = bersihkan(teks);
        input.value = kode;
        pesan('Terbaca: ' + kode + ' — memeriksa…');

        hentikanKamera();

        // Beri jeda singkat supaya pesan terbaca sebelum halaman berpindah
        setTimeout(() => form.submit(), 280);
    }

    function pilihMode(baru) {
        mode = baru;

        slider.forEach(t => t.classList.toggle('is-on', t.dataset.mode === baru));
        panes.forEach(p => p.classList.toggle('d-none', p.dataset.pane !== baru));

        if (baru === 'kamera') {
            mulaiKamera();
        } else {
            hentikanKamera();
            if (baru === 'alat') {
                input.focus();
            }
        }
    }

    slider.forEach(t => t.addEventListener('click', () => pilihMode(t.dataset.mode)));

    // Scanner alat (keyboard wedge) mengirim Enter di akhir kode
    input.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' && mode === 'alat') {
            e.preventDefault();
            if (input.value.trim() !== '') form.submit();
        }
    });

    // Kamera otomatis jalan kalau didukung; kalau tidak, jatuh ke tab Ketik
    pilihMode('kamera');

    window.addEventListener('beforeunload', hentikanKamera);
})();
</script>
@endpush
