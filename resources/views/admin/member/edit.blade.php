@extends('admin.layouts.app')

@section('content')
<div class="page-head">
    <div>
        <h1>{{ $judul }}</h1>
        <p>Kode member {{ $member->kode_member }} &middot; terdaftar {{ tanggal_id($member->tanggal_daftar) }}</p>
    </div>
    <div style="display: flex; gap: 8px; flex-wrap: wrap;">
        <a href="{{ route('backend.member.kartu', $member->id) }}" class="btn btn-accent">
            <i class="bi bi-printer"></i> Cetak Kartu
        </a>
        <a href="{{ route('backend.member.index') }}" class="btn btn-ghost">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<form action="{{ route('backend.member.update', $member->id) }}" method="post" enctype="multipart/form-data">
    @csrf
    @method('put')
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="panel">
                <h3 class="panel__title" style="margin-bottom: 14px;">Foto Member</h3>
                <div class="photo-pick">
                    <img class="foto-preview"
                         src="{{ $member->foto ? asset('storage/img-member/' . $member->foto) : asset('image/img-default.jpg') }}"
                         alt="{{ $member->nama }}">
                    <input type="file" name="foto" class="form-control @error('foto') is-invalid @enderror" accept="image/*" onchange="previewFoto()">
                    @error('foto')
                    <div class="field-error">{{ $message }}</div>
                    @enderror
                    <div style="font-size: 12px; color: var(--muted); margin-top: 8px;">
                        Kosongkan jika tidak ingin mengganti foto.
                    </div>
                </div>

                <hr style="border-color: var(--line); margin: 18px 0;">

                <label style="display: flex; gap: 9px; align-items: flex-start; cursor: pointer; margin: 0;">
                    <input type="checkbox" name="status" value="1" class="form-check-input" style="margin-top: 2px;"
                           {{ old('status', $akun->status ?? 1) ? 'checked' : '' }}>
                    <span>
                        <strong style="display: block; font-size: 13px;">Akun login aktif</strong>
                        <span style="font-size: 12px; color: var(--muted);">Jika dimatikan, member tidak bisa masuk.</span>
                    </span>
                </label>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="panel">
                <h3 class="panel__title" style="margin-bottom: 16px;">Data Diri</h3>

                <div class="field">
                    <label for="kode_member">Kode Member</label>
                    <input type="text" id="kode_member" value="{{ $member->kode_member }}" class="form-control" disabled>
                </div>

                <div class="field">
                    <label for="nama">Nama Lengkap</label>
                    <input type="text" id="nama" name="nama" value="{{ old('nama', $member->nama) }}"
                           class="form-control @error('nama') is-invalid @enderror">
                    @error('nama')
                    <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-grid">
                    <div class="field">
                        <label for="nik">NIK</label>
                        <input type="text" id="nik" name="nik" value="{{ old('nik', $member->nik) }}" inputmode="numeric"
                               class="form-control @error('nik') is-invalid @enderror">
                        @error('nik')
                        <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="field">
                        <label for="no_hp">No HP</label>
                        <input type="text" id="no_hp" name="no_hp" value="{{ old('no_hp', $member->no_hp) }}" inputmode="numeric"
                               class="form-control @error('no_hp') is-invalid @enderror">
                        @error('no_hp')
                        <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="field">
                    <label for="alamat">Alamat</label>
                    <textarea id="alamat" name="alamat" rows="2" class="form-control @error('alamat') is-invalid @enderror">{{ old('alamat', $member->alamat) }}</textarea>
                    @error('alamat')
                    <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-grid" style="margin-bottom: 0;">
                    <div class="field" style="margin-bottom: 0;">
                        <label for="tanggal_daftar">Tanggal Daftar</label>
                        <input type="date" id="tanggal_daftar" name="tanggal_daftar"
                               value="{{ old('tanggal_daftar', $member->tanggal_daftar->format('Y-m-d')) }}"
                               class="form-control @error('tanggal_daftar') is-invalid @enderror">
                        @error('tanggal_daftar')
                        <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="field" style="margin-bottom: 0;">
                        <label for="email">Email Akun <span style="font-weight: 400; color: var(--muted); font-size: 12px;">(opsional)</span></label>
                        <input type="email" id="email" name="email" value="{{ old('email', $akun->email ?? '') }}"
                               class="form-control @error('email') is-invalid @enderror">
                        @error('email')
                        <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                @unless ($akun)
                <div class="field" style="margin-top: 14px; margin-bottom: 0;">
                    <label for="password">Password Akun <span style="font-weight: 400; color: var(--muted); font-size: 12px;">(dibuat setelah email diisi)</span></label>
                    <input type="password" id="password" name="password"
                           class="form-control @error('password') is-invalid @enderror"
                           placeholder="Kosongkan untuk memakai member123">
                    @error('password')
                    <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>
                @endunless

                @unless ($akun)
                <p class="panel__desc" style="margin: 12px 0 0;">
                    Member ini belum punya akun login. Selama email kosong, dia tetap bisa masuk gym dengan kartu fisik.
                </p>
                @endunless
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-accent"><i class="bi bi-check-lg"></i> Simpan Perubahan</button>
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
