@extends('admin.layouts.app')

@section('content')
<div class="page-head">
    <div>
        <h1>{{ $judul }}</h1>
        <p>Pesanan yang dibuat member sendiri dari halaman member. Konfirmasi setelah uang diterima.</p>
    </div>
</div>

{{-- ---------------------------------------------------------------- --}}
{{-- Pesanan yang belum dibayar                                       --}}
{{-- ---------------------------------------------------------------- --}}
<div class="panel panel--flush" style="margin-bottom: 22px;">
    <div class="panel__head panel__head--padded">
        <div>
            <h3 class="panel__title">Menunggu Konfirmasi</h3>
            <p class="panel__desc">{{ $menunggu->count() }} pesanan belum dibayar.</p>
        </div>
    </div>
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Member</th>
                    <th>Paket</th>
                    <th class="num">Jumlah</th>
                    <th style="width: 1%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($menunggu as $row)
                <tr>
                    <td style="white-space: nowrap;">
                        {{ $row->created_at->format('d/m/Y') }}<br>
                        <span style="font-size: 12px; color: var(--muted);">{{ $row->created_at->format('H:i') }}</span>
                    </td>
                    <td>
                        <strong style="font-weight: 600;">{{ $row->member->nama ?? '-' }}</strong><br>
                        <span style="font-size: 12px; color: var(--muted);">{{ $row->member->kode_member ?? '-' }}</span>
                    </td>
                    <td>{{ $row->paket->nama ?? '-' }}</td>
                    <td class="num">{{ rupiah($row->jumlah) }}</td>
                    <td>
                        <form method="POST" action="{{ route('backend.pesanan.proses', $row->id) }}"
                              style="display: flex; gap: 6px; align-items: center; justify-content: flex-end; flex-wrap: nowrap;">
                            @csrf
                            <select name="metode" class="form-select form-select-sm" style="width: 104px;" required>
                                <option value="cash" {{ old('metode') == 'cash' ? 'selected' : '' }}>Cash</option>
                                <option value="transfer" {{ old('metode') == 'transfer' ? 'selected' : '' }}>Transfer</option>
                                <option value="qris" {{ old('metode') == 'qris' ? 'selected' : '' }}>QRIS</option>
                            </select>
                            <input type="date" name="tanggal_bayar" value="{{ now()->toDateString() }}"
                                   class="form-control form-control-sm" style="width: 138px;" required>
                            <button type="submit" class="btn btn-accent btn-sm" title="Konfirmasi pembayaran">
                                <i class="bi bi-check2-circle"></i> Konfirmasi
                            </button>
                        </form>
                        <form method="POST" action="{{ route('backend.pesanan.destroy', $row->id) }}" style="text-align: right; margin-top: 6px;">
                            @csrf
                            @method('delete')
                            <button type="submit" class="btn btn-ghost btn-xs show_confirm"
                                    data-konf-delete="pesanan {{ $row->member->nama ?? '' }}">
                                <i class="bi bi-x-lg"></i> Batalkan
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="empty">Tidak ada pesanan menunggu.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ---------------------------------------------------------------- --}}
{{-- Riwayat pesanan yang sudah dikonfirmasi                          --}}
{{-- ---------------------------------------------------------------- --}}
<div class="panel panel--flush">
    <div class="panel__head panel__head--padded">
        <div>
            <h3 class="panel__title">Sudah Diproses</h3>
            <p class="panel__desc">30 pesanan terakhir.</p>
        </div>
    </div>
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Member</th>
                    <th>Paket</th>
                    <th class="num">Jumlah</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($riwayat as $row)
                <tr>
                    <td style="white-space: nowrap;">
                        {{ ($row->diproses_at ?? $row->created_at)->format('d/m/Y') }}<br>
                        <span style="font-size: 12px; color: var(--muted);">{{ ($row->diproses_at ?? $row->created_at)->format('H:i') }}</span>
                    </td>
                    <td>
                        <strong style="font-weight: 600;">{{ $row->member->nama ?? '-' }}</strong><br>
                        <span style="font-size: 12px; color: var(--muted);">{{ $row->member->kode_member ?? '-' }}</span>
                    </td>
                    <td>{{ $row->paket->nama ?? '-' }}</td>
                    <td class="num">{{ rupiah($row->jumlah) }}</td>
                    <td>
                        <span class="pill pill--ok">Dikonfirmasi</span><br>
                        <span style="font-size: 12px; color: var(--muted);">
                            {{ ucfirst($row->metode) }} &middot; {{ $row->pemroses->nama ?? '-' }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="empty">Belum ada riwayat.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
