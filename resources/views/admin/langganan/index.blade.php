@extends('admin.layouts.app')

@section('content')
<div class="page-head">
    <div>
        <h1>{{ $judul }}</h1>
        <p>Semua transaksi langganan beserta pembayarannya.</p>
    </div>
    <a href="{{ route('backend.langganan.create') }}" class="btn btn-accent">
        <i class="bi bi-plus-lg"></i> Tambah Langganan
    </a>
</div>

<div class="panel panel--flush">
    <div class="table-wrap">
        <table id="zero_config" class="table">
            <thead>
                <tr>
                    <th>Member</th>
                    <th>Paket</th>
                    <th>Periode</th>
                    <th class="num">Harga</th>
                    <th>Status</th>
                    <th style="width: 1%;"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($langganan as $row)
                <tr>
                    <td>
                        <strong style="font-weight: 600;">{{ $row->member->nama ?? '-' }}</strong><br>
                        <span style="font-size: 12px; color: var(--muted);">{{ $row->member->kode_member ?? '-' }}</span>
                    </td>
                    <td>{{ $row->paket->nama ?? '-' }}</td>
                    <td>
                        {{ tanggal_id($row->tanggal_mulai) }}<br>
                        <span style="font-size: 12px; color: var(--muted);">sampai {{ tanggal_id($row->tanggal_berakhir) }}</span>
                    </td>
                    <td class="num">{{ rupiah($row->harga_bayar) }}</td>
                    <td>
                        @if ($row->status == 1)
                        <span class="pill pill--ok">Aktif</span>
                        @else
                        <span class="pill pill--muted">Dibatalkan</span>
                        @endif
                    </td>
                    <td>
                        <div style="display: flex; gap: 6px; justify-content: flex-end;">
                            <a href="{{ route('backend.langganan.show', $row->id) }}" class="btn btn-ghost btn-icon" title="Detail">
                                <i class="bi bi-eye"></i>
                            </a>
                            <form method="POST" action="{{ route('backend.langganan.destroy', $row->id) }}" style="display: inline;">
                                @method('delete')
                                @csrf
                                <button type="submit" class="btn btn-danger-soft btn-icon show_confirm" data-konf-delete="langganan {{ $row->member->nama ?? '' }}" title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
