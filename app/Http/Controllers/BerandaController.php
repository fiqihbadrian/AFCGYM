<?php

namespace App\Http\Controllers;

use App\Models\Kunjungan;
use App\Models\Langganan;
use App\Models\Member;
use App\Models\Pembayaran;

class BerandaController extends Controller
{
    public function index()
    {
        $hariIni = now()->startOfDay();

        // Member aktif = punya langganan terakhir yang tanggal berakhirnya belum lewat
        $memberAktif = Member::whereHas('langgananTerakhir', function ($query) use ($hariIni) {
            $query->whereDate('tanggal_berakhir', '>=', $hariIni)
                  ->where('status', 1);
        })->count();

        $memberTotal = Member::count();

        $kunjunganHariIni = Kunjungan::whereDate('waktu_kunjungan', $hariIni)->count();

        $pendapatanBulanIni = Pembayaran::whereMonth('tanggal_bayar', now()->month)
            ->whereYear('tanggal_bayar', now()->year)
            ->sum('jumlah');

        // Bagian pendapatan yang berasal dari bayar per latihan
        $pendapatanLatihanBulanIni = Pembayaran::whereNotNull('kunjungan_id')
            ->whereMonth('tanggal_bayar', now()->month)
            ->whereYear('tanggal_bayar', now()->year)
            ->sum('jumlah');

        $kunjunganLatihanHariIni = Kunjungan::where('jenis', 'latihan')
            ->whereDate('waktu_kunjungan', $hariIni)
            ->count();

        // Member yang akan habis masa aktifnya dalam 7 hari ke depan
        $akanExpired = Member::with('langgananTerakhir')
            ->whereHas('langgananTerakhir', function ($query) use ($hariIni) {
                $query->whereBetween('tanggal_berakhir', [
                    $hariIni,
                    $hariIni->copy()->addDays(7),
                ])->where('status', 1);
            })
            ->get();

        $kunjunganTerakhir = Kunjungan::with('member')
            ->orderByDesc('waktu_kunjungan')
            ->limit(5)
            ->get();

        return view('admin.beranda.index', [
            'judul'             => 'Beranda',
            'memberAktif'       => $memberAktif,
            'memberTotal'       => $memberTotal,
            'kunjunganHariIni'  => $kunjunganHariIni,
            'pendapatanBulanIni'=> $pendapatanBulanIni,
            'pendapatanLatihanBulanIni' => $pendapatanLatihanBulanIni,
            'kunjunganLatihanHariIni'   => $kunjunganLatihanHariIni,
            'akanExpired'       => $akanExpired,
            'kunjunganTerakhir' => $kunjunganTerakhir,
        ]);
    }
}
