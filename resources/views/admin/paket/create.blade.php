@extends('admin.layouts.app')

@section('content')
<div class="page-head">
    <div>
        <h1>{{ $judul }}</h1>
        <p>Paket berdurasi dipakai saat mencatat langganan. Paket latihan dipakai saat menagih per kedatangan.</p>
    </div>
    <a href="{{ route('backend.paket.index') }}" class="btn btn-ghost">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<form action="{{ route('backend.paket.store') }}" method="post">
    @csrf
    <div class="panel" style="max-width: 760px;">
        <h3 class="panel__title" style="margin-bottom: 16px;">Informasi Paket</h3>

        <div class="field">
            <label for="nama">Nama Paket</label>
            <input type="text" id="nama" name="nama" value="{{ old('nama') }}"
                   class="form-control @error('nama') is-invalid @enderror" placeholder="Contoh: Bulanan Reguler">
            @error('nama')
            <div class="field-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="field">
            <label for="jenis">Jenis Paket</label>
            <select id="jenis" name="jenis" class="form-select @error('jenis') is-invalid @enderror">
                <option value="paket" {{ old('jenis') === 'paket' ? 'selected' : '' }}>Berlangganan (punya durasi hari)</option>
                <option value="latihan" {{ old('jenis') === 'latihan' ? 'selected' : '' }}>Latihan (bayar per kedatangan)</option>
            </select>
            <div class="field-hint">Paket latihan tidak punya durasi — berlaku sampai gym tutup.</div>
            @error('jenis')
            <div class="field-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-grid">
            <div class="field" id="field-durasi">
                <label for="durasi_hari">Durasi (hari)</label>
                <input type="number" id="durasi_hari" name="durasi_hari" value="{{ old('durasi_hari') }}" min="1"
                       class="form-control @error('durasi_hari') is-invalid @enderror" placeholder="30">
                @error('durasi_hari')
                <div class="field-error">{{ $message }}</div>
                @enderror
            </div>
            <div class="field">
                <label for="harga">Harga (Rp)</label>
                <input type="number" id="harga" name="harga" value="{{ old('harga') }}" min="0"
                       class="form-control @error('harga') is-invalid @enderror" placeholder="150000">
                @error('harga')
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

        <div class="form-actions">
            <button type="submit" class="btn btn-accent"><i class="bi bi-check-lg"></i> Simpan</button>
            <a href="{{ route('backend.paket.index') }}" class="btn btn-ghost">Batal</a>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
(function () {
    var jenis = document.getElementById('jenis');
    var durasi = document.getElementById('field-durasi');
    if (!jenis || !durasi) return;
    function atur() {
        var latihan = jenis.value === 'latihan';
        durasi.style.display = latihan ? 'none' : '';
    }
    jenis.addEventListener('change', atur);
    atur();
})();
</script>
@endpush
