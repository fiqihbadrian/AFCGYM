<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Kartu {{ $member->nama }} | AFCGym</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        background: #f2f3f5;
        color: #14181f;
        padding: 28px 16px;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 18px;
    }

    .bar {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        justify-content: center;
    }

    .bar a, .bar button {
        font: inherit;
        font-size: 14px;
        font-weight: 600;
        padding: 10px 18px;
        border-radius: 8px;
        border: 1px solid #d9dce1;
        background: #fff;
        color: #14181f;
        text-decoration: none;
        cursor: pointer;
    }

    .bar .utama {
        background: #ff6a1a;
        border-color: #ff6a1a;
        color: #fff;
    }

    .kartu {
        width: 340px;
        border-radius: 16px;
        background: #1b1f27;
        color: #fff;
        padding: 22px 22px 18px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, .18);
    }

    .kartu__top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 18px;
    }

    .kartu__brand {
        font-size: 19px;
        font-weight: 800;
        letter-spacing: -.02em;
    }

    .kartu__brand span { color: #ff6a1a; }

    .kartu__kind {
        font-size: 10px;
        letter-spacing: .16em;
        text-transform: uppercase;
        color: #8b93a1;
        margin-top: 2px;
    }

    .kartu__qr {
        background: #fff;
        border-radius: 10px;
        padding: 10px;
        width: 132px;
        height: 132px;
        margin: 0 auto 16px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .kartu__nama {
        font-size: 21px;
        font-weight: 700;
        line-height: 1.2;
        text-align: center;
        letter-spacing: -.01em;
    }

    .kartu__kode {
        text-align: center;
        font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
        font-size: 24px;
        font-weight: 700;
        letter-spacing: .09em;
        color: #ff6a1a;
        margin: 4px 0 16px;
    }

    .kartu__grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px 14px;
        border-top: 1px solid rgba(255, 255, 255, .13);
        padding-top: 14px;
    }

    .kartu__grid small {
        display: block;
        font-size: 9px;
        letter-spacing: .1em;
        text-transform: uppercase;
        color: #8b93a1;
        margin-bottom: 2px;
    }

    .kartu__grid strong {
        font-size: 13px;
        font-weight: 600;
    }

    .kartu__catatan {
        margin-top: 16px;
        padding-top: 12px;
        border-top: 1px solid rgba(255, 255, 255, .13);
        font-size: 10.5px;
        line-height: 1.5;
        color: #8b93a1;
        text-align: center;
    }

    .petunjuk {
        max-width: 340px;
        font-size: 12.5px;
        line-height: 1.6;
        color: #5b6270;
        text-align: center;
    }

    @media print {
        @page { size: 90mm 60mm; margin: 0; }

        body {
            background: #fff;
            padding: 0;
            display: block;
        }

        .bar, .petunjuk { display: none !important; }

        .kartu {
            width: 90mm;
            border-radius: 0;
            box-shadow: none;
            padding: 6mm 6mm 4mm;
        }

        .kartu__qr { width: 30mm; height: 30mm; padding: 1.5mm; }
        .kartu__nama { font-size: 14pt; }
        .kartu__kode { font-size: 17pt; }
        .kartu__brand { font-size: 13pt; }
        .kartu__grid strong { font-size: 9pt; }
        .kartu__grid small { font-size: 6pt; }
        .kartu__catatan { font-size: 6.5pt; }
    }
</style>
</head>
<body>

<div class="bar">
    <button class="utama" onclick="window.print()">Cetak Kartu</button>
    <a href="{{ route('backend.member.show', $member->id) }}">Kembali ke Detail</a>
</div>

<div class="kartu">
    <div class="kartu__top">
        <div>
            <div class="kartu__brand">AFC<span>Gym</span></div>
            <div class="kartu__kind">Member Card</div>
        </div>
        <div style="font-size: 20px; opacity: .5;">&#9889;</div>
    </div>

    <div class="kartu__qr" id="qrcode"></div>

    <div class="kartu__nama">{{ $member->nama }}</div>
    <div class="kartu__kode">{{ $member->kode_member }}</div>

    <div class="kartu__grid">
        <div>
            <small>Status</small>
            <strong>{{ $member->aktif ? 'Aktif' : 'Belum Berpaket' }}</strong>
        </div>
        <div>
            <small>Paket</small>
            <strong>{{ $member->langgananTerakhir->paket->nama ?? '-' }}</strong>
        </div>
        <div>
            <small>Berlaku Sampai</small>
            <strong>{{ $member->langgananTerakhir ? tanggal_id($member->langgananTerakhir->tanggal_berakhir) : '-' }}</strong>
        </div>
        <div>
            <small>Sisa Hari</small>
            <strong>{{ $member->sisa_hari }} hari</strong>
        </div>
    </div>

    <div class="kartu__catatan">
        Serahkan kartu ini ke petugas saat datang.
    </div>
</div>

<p class="petunjuk">
    Kartu ini untuk member yang tidak memakai HP. Kalau hilang, buka detail member lalu cetak ulang.
</p>

<script src="{{ asset('vendor/qrcode.min.js') }}"></script>
<script>
    new QRCode(document.getElementById('qrcode'), {
        text: @json($member->kode_member),
        width: 112,
        height: 112,
        correctLevel: QRCode.CorrectLevel.H
    });
</script>
</body>
</html>
