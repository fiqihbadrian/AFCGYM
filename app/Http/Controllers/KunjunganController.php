<?php

namespace App\Http\Controllers;

use App\Models\Kunjungan;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KunjunganController extends Controller
{
    public function index()
    {
        $kunjungan = Kunjungan::with(['member', 'pencatat'])
            ->orderByDesc('waktu_kunjungan')
            ->limit(50)
            ->get();

        return view('admin.kunjungan.index', [
            'judul'     => 'Check-in Kunjungan',
            'kunjungan' => $kunjungan,
        ]);
    }

    /**
     * Catat kunjungan berdasarkan kode member (hasil scan QR atau input manual).
     */
    public function store(Request $request)
    {
        $request->validate([
            'kode_member' => 'required|max:20',
        ], [
            'kode_member.required' => 'Kode member wajib diisi.',
        ]);

        $member = Member::where('kode_member', strtoupper(trim($request->kode_member)))->first();

        if (! $member) {
            return back()->with('error', 'Member dengan kode tersebut tidak ditemukan');
        }

        // Halaman check-in hanya untuk member berpaket. Tamu per latihan
        // harus lewat menu Kasir supaya pembayarannya ikut tercatat.
        if (! $member->aktif) {
            return redirect()->route('backend.kasir.index', ['member_id' => $member->id])
                ->with('success', 'Member ' . $member->nama . ' tidak punya paket aktif — lanjutkan di Kasir.');
        }

        // Cegah check-in ganda dalam 1 jam
        $cek = Kunjungan::where('member_id', $member->id)
            ->where('waktu_kunjungan', '>=', now()->subHour())
            ->exists();

        if ($cek) {
            return back()->with('error', 'Member ' . $member->nama . ' baru saja check-in kurang dari 1 jam lalu');
        }

        Kunjungan::create([
            'member_id'       => $member->id,
            'waktu_kunjungan' => now(),
            'tarif'           => 0,
            'jenis'           => 'paket',
            'dicatat_oleh'    => Auth::id(),
        ]);

        return back()->with('success', 'Check-in berhasil: ' . $member->nama . ' (sisa ' . $member->sisa_hari . ' hari)');
    }
}
