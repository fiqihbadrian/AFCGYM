<?php

namespace App\Http\Controllers;

use App\Models\Langganan;
use App\Models\Member;
use App\Models\Paket;
use App\Models\Pembayaran;
use App\Models\Pesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PesananController extends Controller
{
    /**
     * Daftar pesanan yang dibuat member dari halaman member.
     */
    public function index()
    {
        return view('admin.pesanan.index', [
            'judul'    => 'Pesanan Member',
            'menunggu' => Pesanan::with(['member', 'paket'])
                ->menunggu()
                ->orderBy('created_at')
                ->get(),
            'riwayat'  => Pesanan::with(['member', 'paket', 'pemroses'])
                ->sudahDiproses()
                ->orderByDesc('diproses_at')
                ->limit(30)
                ->get(),
        ]);
    }

    /**
     * Konfirmasi pesanan: uang sudah diterima di kasir.
     *
     * Langganan dan pembayaran baru dibuat di sini supaya laporan
     * pendapatan hanya menghitung uang yang benar-benar sudah masuk.
     */
    public function proses(Request $request, Pesanan $pesanan)
    {
        if ($pesanan->sudah_diproses) {
            return back()->with('error', 'Pesanan ini sudah diproses sebelumnya.');
        }

        $request->validate([
            'metode'        => 'required|max:30',
            'tanggal_bayar' => 'required|date',
        ], [
            'metode.required'        => 'Metode pembayaran wajib dipilih.',
            'tanggal_bayar.required' => 'Tanggal bayar wajib diisi.',
        ]);

        $member = $pesanan->member;
        $paket  = $pesanan->paket;

        if (! $paket) {
            return back()->with('error', 'Paket pada pesanan ini sudah tidak tersedia.');
        }

        DB::transaction(function () use ($pesanan, $member, $paket, $request) {
            $langgananAktif = $member->langganan()
                ->where('status', 1)
                ->whereDate('tanggal_berakhir', '>=', now()->startOfDay())
                ->orderByDesc('tanggal_berakhir')
                ->first();

            // Perpanjangan menyambung dari masa aktif yang masih berjalan
            $tanggalMulai = $langgananAktif
                ? $langgananAktif->tanggal_berakhir->copy()->addDay()
                : now()->startOfDay();

            $tanggalBerakhir = $tanggalMulai->copy()->addDays(max(0, $paket->durasi_hari - 1));

            $langganan = Langganan::create([
                'member_id'        => $member->id,
                'paket_id'         => $paket->id,
                'tanggal_mulai'    => $tanggalMulai,
                'tanggal_berakhir' => $tanggalBerakhir,
                'harga_bayar'      => $pesanan->jumlah,
                'status'           => 1,
                'created_by'       => Auth::id(),
            ]);

            Pembayaran::create([
                'langganan_id'  => $langganan->id,
                'jumlah'        => $pesanan->jumlah,
                'metode'        => $request->metode,
                'tanggal_bayar' => $request->tanggal_bayar,
                'keterangan'    => 'Pesanan member — ' . $paket->nama,
            ]);

            $pesanan->update([
                'status'        => Pesanan::STATUS_DIPROSES,
                'metode'        => $request->metode,
                'diproses_oleh' => Auth::id(),
                'diproses_at'   => now(),
            ]);
        });

        return back()->with('success', 'Pesanan ' . $member->nama . ' dikonfirmasi. Paket ' . $paket->nama . ' aktif.');
    }

    /**
     * Batalkan pesanan yang belum dibayar.
     */
    public function destroy(Pesanan $pesanan)
    {
        if ($pesanan->sudah_diproses) {
            return back()->with('error', 'Pesanan yang sudah diproses tidak bisa dibatalkan.');
        }

        $pesanan->delete();

        return back()->with('success', 'Pesanan dibatalkan.');
    }
}
