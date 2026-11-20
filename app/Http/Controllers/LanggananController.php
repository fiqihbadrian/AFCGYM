<?php

namespace App\Http\Controllers;

use App\Models\Langganan;
use App\Models\Member;
use App\Models\Paket;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LanggananController extends Controller
{
    public function index()
    {
        $langganan = Langganan::with(['member', 'paket'])
            ->orderByDesc('tanggal_mulai')
            ->get();

        return view('admin.langganan.index', [
            'judul'     => 'Data Langganan',
            'langganan' => $langganan,
        ]);
    }

    public function create(Request $request)
    {
        $member = Member::orderBy('nama')->get();
        $paket = Paket::where('status', 1)->orderBy('durasi_hari')->get();

        return view('admin.langganan.create', [
            'judul'         => 'Tambah Langganan',
            'member'        => $member,
            'paket'         => $paket,
            'memberTerpilih'=> $request->member_id,
        ]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'member_id'   => 'required|exists:member,id',
            'paket_id'    => 'required|exists:paket,id',
            'metode'      => 'required|max:30',
            'keterangan'  => 'nullable|max:255',
            'tanggal_bayar' => 'required|date',
        ], [
            'member_id.required' => 'Member wajib dipilih.',
            'paket_id.required'  => 'Paket wajib dipilih.',
        ]);

        $member = Member::findOrFail($request->member_id);
        $paket = Paket::findOrFail($request->paket_id);

        $langgananAktif = $member->langganan()
            ->where('status', 1)
            ->whereDate('tanggal_berakhir', '>=', now()->startOfDay())
            ->orderByDesc('tanggal_berakhir')
            ->first();

        // Kalau masih aktif, perpanjangan dihitung menyambung dari tanggal berakhir lama
        $tanggalMulai = $langgananAktif
            ? $langgananAktif->tanggal_berakhir->copy()->addDay()
            : now()->startOfDay();

        $tanggalBerakhir = $tanggalMulai->copy()->addDays($paket->durasi_hari - 1);

        $langganan = Langganan::create([
            'member_id'        => $member->id,
            'paket_id'         => $paket->id,
            'tanggal_mulai'    => $tanggalMulai,
            'tanggal_berakhir' => $tanggalBerakhir,
            'harga_bayar'      => $paket->harga,
            'status'           => 1,
            'created_by'       => Auth::id(),
        ]);

        Pembayaran::create([
            'langganan_id'  => $langganan->id,
            'jumlah'        => $paket->harga,
            'metode'        => $request->metode,
            'tanggal_bayar' => $request->tanggal_bayar,
            'keterangan'    => $request->keterangan,
        ]);

        return redirect()->route('backend.langganan.index')
            ->with('success', 'Langganan berhasil disimpan. Masa aktif sampai ' . tanggal_id($tanggalBerakhir));
    }

    public function show(string $id)
    {
        $langganan = Langganan::with(['member', 'paket', 'pembayaran', 'pembuat'])->findOrFail($id);

        return view('admin.langganan.show', [
            'judul'     => 'Detail Langganan',
            'langganan' => $langganan,
        ]);
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        $langganan = Langganan::findOrFail($id);

        // Pembayaran ikut terhapus karena foreign key cascade
        $langganan->delete();

        return redirect()->route('backend.langganan.index')
            ->with('success', 'Data langganan berhasil dihapus');
    }
}
