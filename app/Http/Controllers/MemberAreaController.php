<?php

namespace App\Http\Controllers;

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
