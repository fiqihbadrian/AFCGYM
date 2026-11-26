<?php

namespace App\Http\Controllers;

use App\Models\Paket;
use App\Models\Pesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MemberAreaController extends Controller
{
    /**
     * Dashboard member: status keanggotaan, sisa hari, ringkasan kunjungan.
     */
    public function index()
    {
        $akun = Auth::user();
        $member = $akun->member;

        if (! $member) {
            Auth::logout();

            return redirect()->route('backend.login')
                ->with('error', 'Akun ini belum terhubung dengan data member');
        }

        $langgananTerakhir = $member->langgananTerakhir;
        $jumlahKunjungan = $member->kunjungan()->count();
        $kunjungan = $member->kunjungan()
            ->orderByDesc('waktu_kunjungan')
            ->limit(5)
            ->get();

        return view('member.beranda', [
            'judul'            => 'Beranda Member',
            'member'           => $member,
            'langgananTerakhir'=> $langgananTerakhir,
            'jumlahKunjungan'  => $jumlahKunjungan,
            'kunjungan'        => $kunjungan,
        ]);
    }

    /**
     * Paket yang bisa dipesan member sendiri.
     *
     * Pesanan tidak langsung membuat langganan: member tetap bayar di
     * kasir, lalu petugas mengonfirmasi dari menu Pesanan Member.
     */
    public function paket()
    {
        $member = Auth::user()->member;

        if (! $member) {
            return redirect()->route('member.beranda');
        }

        return view('member.paket', [
            'judul'      => 'Paket & Latihan',
            'member'     => $member,
            'paket'      => Paket::where('status', 1)->orderBy('durasi_hari')->get(),
            'pesananSaya'=> $member->pesanan()
                ->with('paket')
                ->orderByDesc('created_at')
                ->limit(10)
                ->get(),
        ]);
    }

    /**
     * Simpan pesanan paket dari member (belum dibayar).
     */
    public function pesan(Request $request)
    {
        $member = Auth::user()->member;

        if (! $member) {
            return redirect()->route('member.beranda');
        }

        $validatedData = $request->validate([
            'paket_id' => 'required|exists:paket,id',
            'metode'   => 'required|max:30',
        ], [
            'paket_id.required' => 'Paket wajib dipilih.',
            'metode.required'   => 'Metode pembayaran wajib dipilih.',
        ]);

        $paket = Paket::where('status', 1)->findOrFail($validatedData['paket_id']);

        $sudahAda = $member->pesanan()
            ->where('paket_id', $paket->id)
            ->menunggu()
            ->exists();

        if ($sudahAda) {
            return back()->with('error', 'Pesanan paket ini sudah menunggu konfirmasi kasir.');
        }

        $member->pesanan()->create([
            'paket_id' => $paket->id,
            'jumlah'   => $paket->harga,
            'metode'   => $validatedData['metode'],
            'status'   => Pesanan::STATUS_MENUNGGU,
        ]);

        return redirect()->route('member.paket')
            ->with('success', 'Pesanan ' . $paket->nama . ' dikirim. Bayar di kasir untuk mengaktifkan.');
    }

    /**
     * Kartu member dengan QR berisi kode member.
     */
    public function kartu()
    {
        $member = Auth::user()->member;

        if (! $member) {
            return redirect()->route('member.beranda');
        }

        return view('member.kartu', [
            'judul'  => 'Kartu Member',
            'member' => $member,
        ]);
    }

    /**
     * Riwayat langganan dan kunjungan milik member yang login.
     */
    public function riwayat()
    {
        $member = Auth::user()->member;

        if (! $member) {
            return redirect()->route('member.beranda');
        }

        $langganan = $member->langganan()
            ->with('paket')
            ->orderByDesc('tanggal_mulai')
            ->get();

        $kunjungan = $member->kunjungan()
            ->orderByDesc('waktu_kunjungan')
            ->limit(30)
            ->get();

        return view('member.riwayat', [
            'judul'     => 'Riwayat Saya',
            'member'    => $member,
            'langganan' => $langganan,
            'kunjungan' => $kunjungan,
        ]);
    }
}
