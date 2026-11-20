<?php

namespace App\Http\Controllers;

use App\Models\Paket;
use Illuminate\Http\Request;

class PaketController extends Controller
{
    public function index()
    {
        $paket = Paket::orderByDesc('updated_at')->get();

        return view('admin.paket.index', [
            'judul' => 'Data Paket',
            'paket' => $paket,
        ]);
    }

    public function create()
    {
        return view('admin.paket.create', [
            'judul' => 'Tambah Paket',
        ]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nama'        => 'required|max:255',
            'jenis'       => 'required|in:paket,latihan',
            'durasi_hari' => 'required_if:jenis,paket|nullable|integer|min:1',
            'harga'       => 'required|numeric|min:0',
            'keterangan'  => 'nullable|max:255',
        ]);

        if ($validatedData['jenis'] === Paket::JENIS_LATIHAN) {
            $validatedData['durasi_hari'] = 0;
        }

        $validatedData['status'] = 1;

        Paket::create($validatedData);

        return redirect()->route('backend.paket.index')
            ->with('success', 'Data paket berhasil tersimpan');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        $paket = Paket::findOrFail($id);

        return view('admin.paket.edit', [
            'judul' => 'Ubah Paket',
            'paket' => $paket,
        ]);
    }

    public function update(Request $request, string $id)
    {
        $paket = Paket::findOrFail($id);

        $validatedData = $request->validate([
            'nama'        => 'required|max:255',
            'jenis'       => 'required|in:paket,latihan',
            'durasi_hari' => 'required_if:jenis,paket|nullable|integer|min:1',
            'harga'       => 'required|numeric|min:0',
            'keterangan'  => 'nullable|max:255',
            'status'      => 'required',
        ]);

        if ($validatedData['jenis'] === Paket::JENIS_LATIHAN) {
            $validatedData['durasi_hari'] = 0;
        }

        $paket->update($validatedData);

        return redirect()->route('backend.paket.index')
            ->with('success', 'Data paket berhasil diperbaharui');
    }

    public function destroy(string $id)
    {
        $paket = Paket::findOrFail($id);

        if ($paket->langganan()->exists()) {
            return redirect()->route('backend.paket.index')
                ->with('error', 'Paket tidak bisa dihapus karena sudah dipakai pada data langganan');
        }

        $paket->delete();

        return redirect()->route('backend.paket.index')
            ->with('success', 'Data paket berhasil dihapus');
    }
}
