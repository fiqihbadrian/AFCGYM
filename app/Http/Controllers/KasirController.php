<?php

namespace App\Http\Controllers;

use App\Models\Kunjungan;
use App\Models\Member;
use App\Models\Paket;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KasirController extends Controller
{
    /**
     * Layar kasir: cari nama, lihat tarif, terima uang, catat masuk.
     */
    public function index(Request $request)
    {
        $cari = trim((string) $request->q);

        $hasil = collect();
        if ($cari !== '' && mb_strlen($cari) >= 2) {
            $hasil = Member::with('langgananTerakhir')
                ->where(function ($q) use ($cari) {
                    $q->where('nama', 'like', '%' . $cari . '%')
                      ->orWhere('kode_member', 'like', '%' . $cari . '%')
                      ->orWhere('no_hp', 'like', '%' . $cari . '%');
                })
                ->orderBy('nama')
                ->limit(12)
                ->get();
        }

        $terpilih = $request->filled('member_id')
            ? Member::find($request->member_id)
            : null;

        $paketLatihan = Paket::latihan()->where('status', 1)->orderBy('harga')->get();
        $paketPaket   = Paket::berlangganan()->where('status', 1)->orderBy('durasi_hari')->get();

        return view('admin.kasir.index', [
            'judul'        => 'Kasir Latihan',
            'cari'         => $cari,
            'hasil'        => $hasil,
            'terpilih'     => $terpilih,
            'paketLatihan' => $paketLatihan,
            'paketPaket'   => $paketPaket,
            'kunjunganHariIni' => Kunjungan::with('member')
                ->whereDate('waktu_kunjungan', now()->toDateString())
                ->orderByDesc('waktu_kunjungan')
                ->get(),
        ]);
    }

    /**
     * Catat satu kedatangan sekaligus pembayarannya (kalau berbayar).
     */
    public function store(Request $request)
    {
        $request->validate([
            'member_id' => 'required|exists:member,id',
            'paket_id'  => 'nullable|exists:paket,id',
            'metode'    => 'required|max:30',
            'uang_bayar'=> 'nullable|numeric|min:0',
        ], [
            'member_id.required' => 'Pilih member atau tamu terlebih dahulu.',
            'metode.required'    => 'Metode pembayaran wajib dipilih.',
        ]);

        $member = Member::with('langgananTerakhir')->findOrFail($request->member_id);

        // Member dengan paket aktif masuk gratis, apa pun pilihan paketnya.
        if ($member->aktif) {
            Kunjungan::create([
                'member_id'       => $member->id,
                'waktu_kunjungan' => now(),
                'tarif'           => 0,
                'jenis'           => 'paket',
                'dicatat_oleh'    => Auth::id(),
            ]);

            return redirect()->route('backend.kasir.index')
                ->with('success', $member->nama . ' punya paket aktif — masuk gratis, sisa ' . $member->sisa_hari . ' hari.');
        }

        // Sudah bayar latihan hari ini: tidak ditagih dua kali.
        if ($member->sudah_bayar_hari_ini) {
            Kunjungan::create([
                'member_id'       => $member->id,
                'waktu_kunjungan' => now(),
                'tarif'           => 0,
                'jenis'           => 'latihan',
                'dicatat_oleh'    => Auth::id(),
            ]);

            return redirect()->route('backend.kasir.index')
                ->with('success', $member->nama . ' sudah membayar latihan hari ini — tidak ditagih lagi.');
        }

        $paket = Paket::latihan()->where('status', 1)->find($request->paket_id);

        if (! $paket) {
            return back()->with('error', 'Pilih tarif latihan terlebih dahulu.')->withInput();
        }

        $tarif = $paket->harga;
        $uang  = $request->filled('uang_bayar') ? (float) $request->uang_bayar : null;

        if ($uang !== null && $uang < $tarif) {
            return back()->with('error', 'Uang yang diterima kurang dari tarif.')->withInput();
        }

        DB::transaction(function () use ($member, $paket, $tarif, $request) {
            $kunjungan = Kunjungan::create([
                'member_id'       => $member->id,
                'waktu_kunjungan' => now(),
                'tarif'           => $tarif,
                'jenis'           => 'latihan',
                'dicatat_oleh'    => Auth::id(),
            ]);

            Pembayaran::create([
                'kunjungan_id'  => $kunjungan->id,
                'jumlah'        => $tarif,
                'metode'        => $request->metode,
                'tanggal_bayar' => now()->toDateString(),
                'keterangan'    => 'Bayar ' . $paket->nama,
            ]);
        });

        $kembalian = $uang !== null ? $uang - $tarif : null;
        $pesan = $member->nama . ' — dibayar ' . rupiah($tarif) . '.';
        if ($kembalian !== null) {
            $pesan .= ' Kembalian ' . rupiah($kembalian) . '.';
        }

        return redirect()->route('backend.kasir.index')->with('success', $pesan);
    }

    /**
     * Daftarkan tamu tanpa akun, langsung dari layar kasir.
     */
    public function daftarTamu(Request $request)
    {
        $request->validate([
            'nama'  => 'required|max:255',
            'no_hp' => 'nullable|max:20',
        ], [
            'nama.required' => 'Nama tamu wajib diisi.',
        ]);

        $member = Member::create([
            'kode_member'    => Member::generateKode(),
            'nama'           => $request->nama,
            'no_hp'          => $request->no_hp,
            'tanggal_daftar' => now()->toDateString(),
        ]);

        return redirect()->route('backend.kasir.index', ['member_id' => $member->id])
            ->with('success', 'Tamu ' . $member->nama . ' terdaftar dengan kode ' . $member->kode_member . '.');
    }
}
