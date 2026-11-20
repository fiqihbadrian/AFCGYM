@extends('member.layouts.app')

@section('content')
<div class="page-head">
    <div>
        <h1>{{ $judul }}</h1>
        <p>Tunjukkan QR ini ke petugas saat datang.</p>
    </div>
</div>

<div class="row">
    <div class="col-lg-6 offset-lg-3">
        <div class="member-card">
            <div class="member-card__top">
                <div>
                    <div class="member-card__brand">AFC<span>Gym</span></div>
                    <div class="member-card__kind">Member Card</div>
                </div>
                <div style="font-size: 26px; opacity: .55;"><i class="bi bi-lightning-charge-fill"></i></div>
            </div>

            <div class="member-card__qr" id="qrcode"></div>

            <h2 class="member-card__name">{{ $member->nama }}</h2>
            <div class="member-card__code">{{ $member->kode_member }}</div>

            <div class="member-card__grid">
                <div>
                    <small>Status</small>
                    <strong>{{ $member->aktif ? 'Aktif' : 'Tidak Aktif' }}</strong>
                </div>
                <div>
                    <small>Berlaku sampai</small>
                    <strong>{{ $member->langgananTerakhir ? tanggal_id($member->langgananTerakhir->tanggal_berakhir) : '-' }}</strong>
                </div>
                <div>
                    <small>Sisa hari</small>
                    <strong>{{ $member->sisa_hari }} hari</strong>
                </div>
                <div>
                    <small>Paket</small>
                    <strong>{{ $member->langgananTerakhir->paket->nama ?? '-' }}</strong>
                </div>
            </div>

            <div class="member-card__note">QR berisi kode member. Jangan bagikan ke orang lain.</div>
        </div>

        <div class="panel" style="margin-top: 20px;">
            <p style="font-size: 13px; color: var(--muted); margin: 0;">
                <i class="bi bi-lightbulb"></i>
                Tidak punya HP? Kasir bisa mencetakkan kartu fisik berisi kode
                <strong style="color: var(--ink); font-family: ui-monospace, monospace;">{{ $member->kode_member }}</strong>.
                Cukup tunjukkan kartunya saat datang.
            </p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('vendor/qrcode.min.js') }}"></script>
<script>
    new QRCode(document.getElementById('qrcode'), {
        text: @json($member->kode_member),
        width: 150,
        height: 150,
        correctLevel: QRCode.CorrectLevel.M
    });
</script>
@endpush
