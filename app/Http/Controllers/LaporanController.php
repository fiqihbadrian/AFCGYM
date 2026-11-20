<?php

namespace App\Http\Controllers;

use App\Models\Kunjungan;
use App\Models\Langganan;
use App\Models\Member;
use App\Models\Pembayaran;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $dari = $request->dari ? \Carbon\Carbon::parse($request->dari)->startOfDay() : now()->startOfMonth();
        $sampai = $request->sampai ? \Carbon\Carbon::parse($request->sampai)->endOfDay() : now()->endOfMonth();

        $pembayaran = Pembayaran::with(['langganan.member', 'langganan.paket', 'kunjungan.member'])
            ->whereBetween('tanggal_bayar', [$dari, $sampai])
            ->orderByDesc('tanggal_bayar')
            ->get();

        $totalPendapatan = $pembayaran->sum('jumlah');

        // Pendapatan dipisah: paket berdurasi vs bayar per latihan
        $totalPaket   = $pembayaran->whereNull('kunjungan_id')->sum('jumlah');
        $totalLatihan = $pembayaran->whereNotNull('kunjungan_id')->sum('jumlah');

        $kunjunganBerbayar = Kunjungan::where('jenis', 'latihan')
            ->whereBetween('waktu_kunjungan', [$dari, $sampai])
            ->count();

        $jumlahKunjungan = Kunjungan::whereBetween('waktu_kunjungan', [$dari, $sampai])->count();

        // Member yang masa aktifnya sudah lewat
        $memberExpired = Member::with('langgananTerakhir')
            ->whereHas('langgananTerakhir', function ($query) {
                $query->whereDate('tanggal_berakhir', '<', now()->startOfDay());
            })
            ->get();

        return view('admin.laporan.index', [
            'judul'            => 'Laporan',
            'pembayaran'       => $pembayaran,
            'totalPendapatan'  => $totalPendapatan,
            'totalPaket'       => $totalPaket,
            'totalLatihan'     => $totalLatihan,
            'jumlahKunjungan'  => $jumlahKunjungan,
            'kunjunganBerbayar'=> $kunjunganBerbayar,
            'memberExpired'    => $memberExpired,
            'dari'             => $dari,
            'sampai'           => $sampai,
        ]);
    }
}
