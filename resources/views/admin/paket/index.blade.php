@extends('admin.layouts.app')

@section('content')
<div class="page-head">
    <div>
        <h1>{{ $judul }}</h1>
        <p>Daftar paket yang bisa dijual ke member. Paket nonaktif tidak muncul saat mencatat langganan.</p>
    </div>
    <a href="{{ route('backend.paket.create') }}" class="btn btn-accent">
        <i class="bi bi-plus-lg"></i> Tambah Paket
    </a>
</div>

<div class="panel panel--flush">
    <div class="table-wrap">
        <table id="zero_config" class="table">
            <thead>
                <tr>
                    <th>Nama Paket</th>
                    <th>Jenis</th>
                    <th>Durasi</th>
                    <th class="num">Harga</th>
                    <th>Status</th>
                    <th style="width: 1%;"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($paket as $row)
                <tr>
                    <td>
                        <strong style="font-weight: 600;">{{ $row->nama }}</strong><br>
                        <span style="font-size: 12px; color: var(--muted);">{{ $row->keterangan ?: 'Tanpa keterangan' }}</span>
                    </td>
                    <td>
                        @if ($row->is_latihan)
                        <span class="pill">Latihan</span>
                        @else
                        <span class="pill pill--muted">Berlangganan</span>
                        @endif
                    </td>
                    <td>{{ $row->is_latihan ? '—' : $row->durasi_hari . ' hari' }}</td>
                    <td class="num">{{ rupiah($row->harga) }}</td>
                    <td>
                        @if ($row->status == 1)
                        <span class="pill pill--ok">Aktif</span>
                        @else
                        <span class="pill pill--muted">Nonaktif</span>
                        @endif
                    </td>
                    <td>
                        <div style="display: flex; gap: 6px; justify-content: flex-end;">
                            <a href="{{ route('backend.paket.edit', $row->id) }}" class="btn btn-ghost btn-icon" title="Ubah">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form method="POST" action="{{ route('backend.paket.destroy', $row->id) }}" style="display: inline;">
                                @method('delete')
                                @csrf
                                <button type="submit" class="btn btn-danger-soft btn-icon show_confirm" data-konf-delete="{{ $row->nama }}" title="Hapus">
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
