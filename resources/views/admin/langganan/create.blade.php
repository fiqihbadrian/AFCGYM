@extends('admin.layouts.app')

@section('content')
<div class="page-head">
    <div>
        <h1>{{ $judul }}</h1>
        <p>Masa aktif dihitung otomatis dari durasi paket yang dipilih.</p>
    </div>
    <a href="{{ route('backend.langganan.index') }}" class="btn btn-ghost">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<form action="{{ route('backend.langganan.store') }}" method="post">
    @csrf
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="panel">
                <h3 class="panel__title" style="margin-bottom: 16px;">Transaksi Langganan</h3>

                <div class="field">
                    <label for="member_id">Member</label>
                    <select id="member_id" name="member_id" class="form-select @error('member_id') is-invalid @enderror">
                        <option value="">Pilih member</option>
                        @foreach ($member as $row)
                            <option value="{{ $row->id }}" {{ old('member_id', $memberTerpilih) == $row->id ? 'selected' : '' }}>
                                {{ $row->kode_member }} — {{ $row->nama }} ({{ $row->aktif ? 'aktif' : ($row->langgananTerakhir ? 'expired' : 'belum aktif') }})
                            </option>
                        @endforeach
                    </select>
                    @error('member_id')
                    <div class="field-error">{{ $message }}</div>
                    @enderror
                    <div class="hint" style="font-size: 12px; color: var(--muted); margin-top: 6px;">
                        Member expired tetap bisa diperpanjang.
                    </div>
                </div>

                <div class="field">
                    <label for="paket_id">Paket</label>
                    <select id="paket_id" name="paket_id" class="form-select @error('paket_id') is-invalid @enderror">
                        <option value="">Pilih paket</option>
                        @foreach ($paket as $row)
                            <option value="{{ $row->id }}" data-harga="{{ $row->harga }}" data-durasi="{{ $row->durasi_hari }}" data-nama="{{ $row->nama }}"
                                {{ old('paket_id') == $row->id ? 'selected' : '' }}>
                                {{ $row->nama }} — {{ $row->durasi_hari }} hari — {{ rupiah($row->harga) }}
                            </option>
                        @endforeach
                    </select>
                    @error('paket_id')
                    <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-grid">
                    <div class="field">
                        <label for="metode">Metode Pembayaran</label>
                        <select id="metode" name="metode" class="form-select @error('metode') is-invalid @enderror">
                            <option value="Tunai" {{ old('metode') == 'Tunai' ? 'selected' : '' }}>Tunai</option>
                            <option value="Transfer" {{ old('metode') == 'Transfer' ? 'selected' : '' }}>Transfer</option>
                            <option value="QRIS" {{ old('metode') == 'QRIS' ? 'selected' : '' }}>QRIS</option>
                            <option value="Kartu" {{ old('metode') == 'Kartu' ? 'selected' : '' }}>Kartu Debit/Kredit</option>
                        </select>
                        @error('metode')
                        <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="field">
                        <label for="tanggal_bayar">Tanggal Bayar</label>
                        <input type="date" id="tanggal_bayar" name="tanggal_bayar" value="{{ old('tanggal_bayar', date('Y-m-d')) }}"
                               class="form-control @error('tanggal_bayar') is-invalid @enderror">
                        @error('tanggal_bayar')
                        <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="field" style="margin-bottom: 0;">
                    <label for="keterangan">Keterangan</label>
                    <input type="text" id="keterangan" name="keterangan" value="{{ old('keterangan') }}"
                           class="form-control @error('keterangan') is-invalid @enderror" placeholder="Opsional">
                    @error('keterangan')
                    <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="alert-box alert-box--info" style="margin-top: 18px; margin-bottom: 0;">
                    <i class="bi bi-info-circle"></i>
                    <span>Jika member masih aktif, langganan baru menyambung dari tanggal berakhir sebelumnya — bukan dari hari ini.</span>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-accent"><i class="bi bi-check-lg"></i> Simpan Pembayaran</button>
                    <a href="{{ route('backend.langganan.index') }}" class="btn btn-ghost">Batal</a>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="panel">
                <h3 class="panel__title" style="margin-bottom: 12px;">Ringkasan</h3>
                <dl class="dl">
                    <div><dt>Paket</dt><dd id="rNama">-</dd></div>
                    <div><dt>Durasi</dt><dd id="rDurasi">-</dd></div>
                    <div><dt>Total Bayar</dt><dd id="rHarga" style="font-weight: 700;">-</dd></div>
                </dl>
                <p style="font-size: 12px; color: var(--muted); margin: 14px 0 0;">
                    Nominal diambil dari harga paket. Ubah harga di menu Paket bila perlu.
                </p>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
    function ringkasan() {
        const sel = document.getElementById('paket_id');
        const opt = sel.options[sel.selectedIndex];
        const harga = opt.getAttribute('data-harga');
        const durasi = opt.getAttribute('data-durasi');
        const nama = opt.getAttribute('data-nama');

        if (!harga) {
            document.getElementById('rNama').textContent = '-';
            document.getElementById('rDurasi').textContent = '-';
            document.getElementById('rHarga').textContent = '-';
            return;
        }

        document.getElementById('rNama').textContent = nama;
        document.getElementById('rDurasi').textContent = durasi + ' hari';
        document.getElementById('rHarga').textContent = 'Rp ' + Number(harga).toLocaleString('id-ID');
    }

    document.getElementById('paket_id').addEventListener('change', ringkasan);
    ringkasan();
</script>
@endpush
