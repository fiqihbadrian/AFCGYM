@extends('admin.layouts.app')

@section('content')
<div class="page-head">
    <div>
        <h1>{{ $judul }}</h1>
        <p>{{ $member->count() }} member terdaftar. Status dihitung dari tanggal berakhir langganan terakhir.</p>
    </div>
    <a href="{{ route('backend.member.create') }}" class="btn btn-accent">
        <i class="bi bi-person-plus"></i> Tambah Member
    </a>
</div>

<div class="panel panel--flush">
    <div class="table-wrap">
        <table id="zero_config" class="table">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>No HP</th>
                    <th>Masa Aktif</th>
                    <th>Status</th>
                    <th style="width: 1%;"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($member as $row)
                <tr>
                    <td><span class="pill pill--muted">{{ $row->kode_member }}</span></td>
                    <td>
                        <strong style="font-weight: 600;">{{ $row->nama }}</strong><br>
                        <span style="font-size: 12px; color: var(--muted);">{{ $row->nik ?: 'NIK belum diisi' }}</span>
                    </td>
                    <td>{{ $row->no_hp }}</td>
                    <td>
                        @if ($row->langgananTerakhir)
                            {{ tanggal_id($row->langgananTerakhir->tanggal_berakhir) }}
                        @else
                            <span style="color: var(--muted);">Belum berlangganan</span>
                        @endif
                    </td>
                    <td>
                        @if ($row->aktif)
                            <span class="pill pill--ok">Aktif</span>
                        @elseif ($row->langgananTerakhir)
                            <span class="pill pill--danger">Expired</span>
                        @else
                            <span class="pill pill--muted">Belum Aktif</span>
                        @endif
                    </td>
                    <td>
                        <div style="display: flex; gap: 6px; justify-content: flex-end;">
                            <a href="{{ route('backend.member.show', $row->id) }}" class="btn btn-ghost btn-icon" title="Detail">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('backend.member.edit', $row->id) }}" class="btn btn-ghost btn-icon" title="Ubah">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form method="POST" action="{{ route('backend.member.destroy', $row->id) }}" style="display: inline;">
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
