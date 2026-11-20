<?php

namespace Database\Seeders;

use App\Models\Kunjungan;
use App\Models\Langganan;
use App\Models\Member;
use App\Models\Paket;
use App\Models\Pembayaran;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create([
            'nama'     => 'Admin AFCGym',
            'email'    => 'admin@afcgym.test',
            'password' => Hash::make('admin123'),
            'role'     => User::ROLE_ADMIN,
            'status'   => 1,
            'hp'       => '081234567890',
        ]);

        $paketList = [
            ['nama' => 'Latihan', 'jenis' => 'latihan', 'durasi_hari' => 0, 'harga' => 20000, 'keterangan' => 'Bayar per kedatangan, sampai gym tutup'],
            ['nama' => 'Harian', 'durasi_hari' => 1, 'harga' => 25000, 'keterangan' => 'Sekali masuk'],
            ['nama' => 'Mingguan', 'durasi_hari' => 7, 'harga' => 75000, 'keterangan' => 'Berlaku 7 hari'],
            ['nama' => 'Bulanan Reguler', 'durasi_hari' => 30, 'harga' => 250000, 'keterangan' => 'Akses semua alat'],
            ['nama' => 'Bulanan Premium', 'durasi_hari' => 30, 'harga' => 400000, 'keterangan' => 'Akses alat + kelas'],
            ['nama' => 'Tiga Bulan', 'durasi_hari' => 90, 'harga' => 650000, 'keterangan' => 'Lebih hemat'],
        ];

        foreach ($paketList as $item) {
            Paket::create($item + ['jenis' => 'paket', 'status' => 1]);
        }

        $memberList = [
            ['nama' => 'Budi Santoso', 'nik' => '3201234567890001', 'no_hp' => '0811111111', 'alamat' => 'Jl. Melati No. 12'],
            ['nama' => 'Siti Rahmawati', 'nik' => '3201234567890002', 'no_hp' => '0822222222', 'alamat' => 'Jl. Anggrek No. 5'],
            ['nama' => 'Andi Pratama', 'nik' => '3201234567890003', 'no_hp' => '0833333333', 'alamat' => 'Jl. Kenanga No. 8'],
            ['nama' => 'Dewi Lestari', 'nik' => '3201234567890004', 'no_hp' => '0844444444', 'alamat' => 'Jl. Mawar No. 21'],
            ['nama' => 'Rizky Firmansyah', 'nik' => '3201234567890005', 'no_hp' => '0855555555', 'alamat' => 'Jl. Dahlia No. 3'],
            ['nama' => 'Nadia Putri', 'nik' => '3201234567890006', 'no_hp' => '0866666666', 'alamat' => 'Jl. Cempaka No. 9'],
            ['nama' => 'Fajar Nugroho', 'nik' => '3201234567890007', 'no_hp' => '0877777777', 'alamat' => 'Jl. Teratai No. 14'],
            ['nama' => 'Ayu Wulandari', 'nik' => '3201234567890008', 'no_hp' => '0888888888', 'alamat' => 'Jl. Kamboja No. 2'],
        ];

        // Selisih hari dari hari ini: positif = masih aktif, negatif = sudah expired
        // Paket: 1 Latihan (tanpa langganan), 2 Harian, 3 Mingguan,
        // 4 Bulanan Reguler, 5 Bulanan Premium, 6 Tiga Bulan
        $skenario = [
            0 => ['paket' => 4, 'mulai' => -10],  // aktif, sisa 19 hari
            1 => ['paket' => 5, 'mulai' => -25],  // aktif, sisa 4  hari (akan expired)
            2 => ['paket' => 3, 'mulai' => -2],   // aktif, sisa 4  hari (akan expired)
            3 => ['paket' => 4, 'mulai' => -40],  // expired 11 hari lalu
            4 => ['paket' => 6, 'mulai' => -20],  // aktif, sisa 69 hari
            5 => ['paket' => 2, 'mulai' => 0],    // aktif 1 hari
            6 => ['paket' => 4, 'mulai' => -35],  // expired 6 hari lalu
            7 => ['paket' => 4, 'mulai' => -5],   // aktif, sisa 24 hari
        ];

        foreach ($memberList as $i => $data) {
            $member = Member::create([
                'kode_member'    => 'AFC-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'nama'           => $data['nama'],
                'nik'            => $data['nik'],
                'no_hp'          => $data['no_hp'],
                'alamat'         => $data['alamat'],
                'tanggal_daftar' => now()->subDays(abs($skenario[$i]['mulai']))->toDateString(),
            ]);

            User::create([
                'nama'      => $member->nama,
                'email'     => strtolower(str_replace(' ', '.', $member->nama)) . '@afcgym.test',
                'password'  => Hash::make('member123'),
                'role'      => User::ROLE_MEMBER,
                'status'    => 1,
                'hp'        => $member->no_hp,
                'member_id' => $member->id,
            ]);

            $paket = Paket::find($skenario[$i]['paket']);

            $tanggalMulai = now()->addDays($skenario[$i]['mulai'])->startOfDay();
            $tanggalBerakhir = $tanggalMulai->copy()->addDays($paket->durasi_hari - 1);

            $langganan = Langganan::create([
                'member_id'        => $member->id,
                'paket_id'         => $paket->id,
                'tanggal_mulai'    => $tanggalMulai,
                'tanggal_berakhir' => $tanggalBerakhir,
                'harga_bayar'      => $paket->harga,
                'status'           => 1,
                'created_by'       => $admin->id,
            ]);

            $metode = ['Tunai', 'Transfer', 'QRIS'][$i % 3];

            Pembayaran::create([
                'langganan_id'  => $langganan->id,
                'jumlah'        => $paket->harga,
                'metode'        => $metode,
                'tanggal_bayar' => $tanggalMulai->toDateString(),
                'keterangan'    => 'Pembayaran ' . $paket->nama,
            ]);

            // Kunjungan contoh untuk member yang aktif
            if ($langganan->tanggal_berakhir->isFuture()) {
                $jumlahKunjungan = fake()->numberBetween(2, 8);

                for ($k = 0; $k < $jumlahKunjungan; $k++) {
                    $waktu = now()->subDays(fake()->numberBetween(0, 20))->setTime(
                        fake()->numberBetween(6, 21),
                        fake()->numberBetween(0, 59)
                    );

                    if ($waktu->greaterThan(now())) {
                        continue;
                    }

                    Kunjungan::create([
                        'member_id'       => $member->id,
                        'waktu_kunjungan' => $waktu,
                        'tarif'           => 0,
                        'jenis'           => 'paket',
                        'dicatat_oleh'    => $admin->id,
                    ]);
                }
            }
        }

        // ---------- Contoh tamu bayar per latihan ----------
        $tamuList = [
            ['nama' => 'Joko Susilo', 'no_hp' => '0899001122', 'hari' => 0],
            ['nama' => 'Rudi Hartono', 'no_hp' => '0899003344', 'hari' => 1],
            ['nama' => 'Bagas Saputra', 'no_hp' => null, 'hari' => 2],
        ];

        $paketLatihan = Paket::latihan()->where('status', 1)->first();

        foreach ($tamuList as $i => $tamu) {
            $member = Member::create([
                'kode_member'    => Member::generateKode(),
                'nama'           => $tamu['nama'],
                'no_hp'          => $tamu['no_hp'],
                'tanggal_daftar' => now()->subDays($tamu['hari'])->toDateString(),
            ]);

            $waktu = now()->subDays($tamu['hari'])->setTime(9 + $i, 15);

            $kunjungan = Kunjungan::create([
                'member_id'       => $member->id,
                'waktu_kunjungan' => $waktu,
                'tarif'           => $paketLatihan->harga,
                'jenis'           => 'latihan',
                'dicatat_oleh'    => $admin->id,
            ]);

            Pembayaran::create([
                'kunjungan_id'  => $kunjungan->id,
                'jumlah'        => $paketLatihan->harga,
                'metode'        => 'Tunai',
                'tanggal_bayar' => $waktu->toDateString(),
                'keterangan'    => 'Bayar ' . $paketLatihan->nama,
            ]);
        }
    }
}
