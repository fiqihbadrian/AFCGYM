<?php

namespace App\Http\Controllers;

use App\Helpers\ImageHelper;
use App\Models\Member;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class MemberController extends Controller
{
    public function index()
    {
        $member = Member::with('langgananTerakhir')->orderByDesc('updated_at')->get();

        return view('admin.member.index', [
            'judul'  => 'Data Member',
            'member' => $member,
        ]);
    }

    public function create()
    {
        return view('admin.member.create', [
            'judul' => 'Tambah Member',
        ]);
    }

    public function store(Request $request)
    {
        $messages = [
            'foto.image'  => 'Format gambar gunakan file dengan ekstensi jpeg, jpg, png, atau gif.',
            'foto.max'    => 'Ukuran file gambar maksimal 1024 KB.',
            'email.unique'=> 'Email sudah digunakan, pilih email lain.',
        ];

        $validatedData = $request->validate([
            'nama'           => 'required|max:255',
            'nik'            => 'nullable|max:20',
            'no_hp'          => 'nullable|max:20',
            'alamat'         => 'nullable|max:255',
            'email'          => 'nullable|email|max:255|unique:user,email',
            'password'       => 'nullable|min:4|confirmed',
            'tanggal_daftar' => 'required|date',
            'foto'           => 'image|mimes:jpeg,jpg,png,gif|file|max:1024',
        ], $messages);

        // Upload foto member
        if ($request->file('foto')) {
            $file = $request->file('foto');
            $extension = $file->getClientOriginalExtension();
            $namaFile = date('YmdHis') . '_' . uniqid() . '.' . $extension;

            ImageHelper::uploadAndResize($file, 'storage/img-member', $namaFile, 385, 400);

            $validatedData['foto'] = $namaFile;
        }

        $validatedData['kode_member'] = Member::generateKode();

        $member = Member::create($validatedData);

        // Akun login hanya dibuat kalau kasir mengisikan email.
        // Member yang tidak melek HP tetap terdaftar lewat kartu fisik.
        if ($request->filled('email')) {
            User::create([
                'nama'      => $member->nama,
                'email'     => $request->email,
                'password'  => Hash::make($request->password ?: 'member123'),
                'role'      => User::ROLE_MEMBER,
                'status'    => 1,
                'hp'        => $member->no_hp,
                'member_id' => $member->id,
            ]);
        }

        return redirect()->route('backend.member.show', $member->id)
            ->with('success', 'Member ' . $member->nama . ' tersimpan. Kode ' . $member->kode_member . ' — silakan cetak kartunya.');
    }

    public function show(string $id)
    {
        $member = Member::with(['langganan' => function ($query) {
                $query->orderByDesc('tanggal_mulai');
            }, 'langganan.paket', 'langganan.pembayaran'])
            ->findOrFail($id);

        return view('admin.member.show', [
            'judul'  => 'Detail Member',
            'member' => $member,
            'akun'   => User::where('member_id', $member->id)->first(),
        ]);
    }

    /**
     * Kartu member versi cetak (untuk member yang tidak memakai HP).
     */
    public function kartu(string $id)
    {
        $member = Member::with('langgananTerakhir.paket')->findOrFail($id);

        return view('admin.member.kartu', [
            'judul'  => 'Cetak Kartu Member',
            'member' => $member,
        ]);
    }

    public function edit(string $id)
    {
        $member = Member::findOrFail($id);
        $akun = User::where('member_id', $member->id)->first();

        return view('admin.member.edit', [
            'judul'  => 'Ubah Member',
            'member' => $member,
            'akun'   => $akun,
        ]);
    }

    public function update(Request $request, string $id)
    {
        $member = Member::findOrFail($id);
        $akun = User::where('member_id', $member->id)->first();

        $messages = [
            'foto.image'  => 'Format gambar gunakan file dengan ekstensi jpeg, jpg, png, atau gif.',
            'foto.max'    => 'Ukuran file gambar maksimal 1024 KB.',
            'email.unique'=> 'Email sudah digunakan, pilih email lain.',
        ];

        $validatedData = $request->validate([
            'nama'           => 'required|max:255',
            'nik'            => 'nullable|max:20',
            'no_hp'          => 'nullable|max:20',
            'alamat'         => 'nullable|max:255',
            'tanggal_daftar' => 'required|date',
            'email'          => ['nullable', 'email', 'max:255', Rule::unique('user', 'email')->ignore($akun?->id)],
            'status'         => 'nullable',
            'foto'           => 'image|mimes:jpeg,jpg,png,gif|file|max:1024',
        ], $messages);

        if ($request->file('foto')) {
            if ($member->foto) {
                $fotoLama = public_path('storage/img-member/') . $member->foto;
                if (file_exists($fotoLama)) {
                    unlink($fotoLama);
                }
            }

            $file = $request->file('foto');
            $extension = $file->getClientOriginalExtension();
            $namaFile = date('YmdHis') . '_' . uniqid() . '.' . $extension;

            ImageHelper::uploadAndResize($file, 'storage/img-member', $namaFile, 385, 400);

            $validatedData['foto'] = $namaFile;
        }

        $member->update($validatedData);

        // Sinkronkan akun login member
        if ($akun) {
            $akun->update([
                'nama'   => $member->nama,
                'email'  => $request->filled('email') ? $request->email : $akun->email,
                'hp'     => $member->no_hp,
                'status' => $request->has('status') ? 1 : 0,
            ]);
        } elseif ($request->filled('email')) {
            // Member lama yang tadinya tanpa akun, sekarang dibuatkan
            User::create([
                'nama'      => $member->nama,
                'email'     => $request->email,
                'password'  => Hash::make($request->password ?: 'member123'),
                'role'      => User::ROLE_MEMBER,
                'status'    => 1,
                'hp'        => $member->no_hp,
                'member_id' => $member->id,
            ]);
        }

        return redirect()->route('backend.member.index')
            ->with('success', 'Data member berhasil diperbaharui');
    }

    public function destroy(string $id)
    {
        $member = Member::findOrFail($id);

        if ($member->foto) {
            $fotoLama = public_path('storage/img-member/') . $member->foto;
            if (file_exists($fotoLama)) {
                unlink($fotoLama);
            }
        }

        // Hapus akun login member juga
        User::where('member_id', $member->id)->delete();

        $member->delete();

        return redirect()->route('backend.member.index')
            ->with('success', 'Data member berhasil dihapus');
    }
}
