@extends('admin.layouts.app')

@section('content')
<div class="page-head">
    <div>
        <h1>{{ $judul }}</h1>
        <p>Data member baru sekaligus dibuatkan akun login untuk halaman member.</p>
    </div>
    <a href="{{ route('backend.member.index') }}" class="btn btn-ghost">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<form action="{{ route('backend.member.store') }}" method="post" enctype="multipart/form-data">
    @csrf
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="panel">
                <h3 class="panel__title" style="margin-bottom: 14px;">Foto Member</h3>
                <div class="photo-pick">
                    <img class="foto-preview" src="{{ asset('image/img-default.jpg') }}" alt="Preview foto">
                    <input type="file" name="foto" class="form-control @error('foto') is-invalid @enderror" accept="image/*" onchange="previewFoto()">
                    @error('foto')
                    <div class="field-error">{{ $message }}</div>
                    @enderror
                    <div class="hint" style="font-size: 12px; color: var(--muted); margin-top: 8px;">
                        Opsional. Format JPG/PNG, maksimal 2 MB.
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="panel">
                <h3 class="panel__title" style="margin-bottom: 16px;">Data Diri</h3>

                <div class="field">
                    <label for="nama">Nama Lengkap</label>
                    <input type="text" id="nama" name="nama" value="{{ old('nama') }}"
                           class="form-control @error('nama') is-invalid @enderror" placeholder="Nama member">
                    @error('nama')
                    <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-grid">
                    <div class="field">
                        <label for="nik">NIK</label>
                        <input type="text" id="nik" name="nik" value="{{ old('nik') }}" inputmode="numeric"
                               class="form-control @error('nik') is-invalid @enderror" placeholder="Opsional">
                        @error('nik')
                        <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="field">
                        <label for="no_hp">No HP</label>
                        <input type="text" id="no_hp" name="no_hp" value="{{ old('no_hp') }}" inputmode="numeric"
                               class="form-control @error('no_hp') is-invalid @enderror" placeholder="Opsional">
                        @error('no_hp')
                        <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="field">
                    <label for="alamat">Alamat</label>
                    <textarea id="alamat" name="alamat" rows="2" class="form-control @error('alamat') is-invalid @enderror" placeholder="Opsional">{{ old('alamat') }}</textarea>
                    @error('alamat')
                    <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="field" style="margin-bottom: 0;">
                    <label for="tanggal_daftar">Tanggal Daftar</label>
                    <input type="date" id="tanggal_daftar" name="tanggal_daftar" value="{{ old('tanggal_daftar', date('Y-m-d')) }}"
                           class="form-control @error('tanggal_daftar') is-invalid @enderror">
                    @error('tanggal_daftar')
                    <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="panel">
                <h3 class="panel__title">Akun Login <span style="font-weight: 400; color: var(--muted); font-size: 13px;">(opsional)</span></h3>
                <p class="panel__desc" style="margin-bottom: 16px;">
                    Kosongkan kalau member tidak memakai HP. Dia tetap bisa masuk gym dengan kartu yang dicetak kasir.
                    Isi hanya kalau member mau melihat riwayatnya sendiri lewat halaman member.
                </p>

                <div class="field">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                           class="form-control @error('email') is-invalid @enderror" placeholder="nama@email.com">
                    @error('email')
                    <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-grid" style="margin-bottom: 0;">
                    <div class="field" style="margin-bottom: 0;">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password"
                               class="form-control @error('password') is-invalid @enderror" placeholder="Minimal 4 karakter">
                        @error('password')
                        <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="field" style="margin-bottom: 0;">
                        <label for="password_confirmation">Konfirmasi Password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Ulangi password">
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-accent"><i class="bi bi-check-lg"></i> Simpan</button>
                <a href="{{ route('backend.member.index') }}" class="btn btn-ghost">Batal</a>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
    function previewFoto() {
        const file = document.querySelector('input[name="foto"]').files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = function (e) {
            document.querySelector('.foto-preview').src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
</script>
@endpush
