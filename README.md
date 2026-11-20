# AFCGym — Sistem Informasi Manajemen Gym

Tugas Web Programming. Laravel 10 + MySQL + Blade.

Dokumen ini menjelaskan sistem dari sisi **apa, kenapa, dan bagaimana**, jadi bisa dipakai langsung untuk presentasi ke dosen maupun untuk menjalankan ulang proyek.

---

## 1. Gambaran Singkat

AFCGym mengurus tiga hal pokok:

1. **Data member** — siapa saja yang terdaftar.
2. **Langganan** — siapa yang masa aktifnya masih berjalan, kapan berakhir.
3. **Kunjungan** — siapa datang hari ini, dan kalau datang tanpa paket, berapa yang dibayar.

Sistem punya **dua peran pengguna**: **Admin** (petugas/kasir gym) dan **Member**.

---

## 2. Masalah yang Diselesaikan

Gym kecil biasanya mencatat keanggotaan di buku tulis atau Excel. Masalah yang muncul:

| Masalah | Penyelesaian di sistem |
|---|---|
| Masa aktif member sering terlewat, member baru tahu sudah expired saat mau masuk | Status aktif dihitung otomatis dari tanggal berakhir langganan, admin dapat peringatan 7 hari sebelum habis |
| Pendapatan tidak terpisah antara penjualan paket dan pembayaran harian | Tabel pembayaran mencatat sumbernya, laporan memisahkan keduanya |
| Member tidak membawa kartu / tidak punya HP | Kasir mencetak kartu fisik berisi kode dan QR |
| Tamu yang hanya datang sekali tidak terdata | Kasir bisa mendaftarkan tamu hanya dengan nama, langsung bayar dan masuk |

---

## 3. Teknologi

| Bagian | Dipakai |
|---|---|
| Framework | Laravel 10 (PHP 8.1+) |
| Database | MySQL 8 |
| Tampilan | Blade + Bootstrap 5.3 (dipasang lokal, bukan CDN) |
| Tabel data | DataTables 2 (pencarian & urutan) |
| Notifikasi | SweetAlert2 |
| QR Code | qrcode.js (dibuat di sisi browser) |
| Autentikasi | Bawaan Laravel (`Auth::attempt`) + middleware peran sendiri |

Semua library disimpan di `public/vendor/` supaya sistem tetap jalan tanpa internet.

---

## 4. Cara Menjalankan

### 1. Siapkan database

```bash
# kalau memakai Docker (nama container: mysql-local)
docker start mysql-local
docker exec mysql-local mysql -uroot -proot -e "CREATE DATABASE IF NOT EXISTS db_afcgym"
```

Sesuaikan `.env`:

```
APP_NAME=AFCGym
DB_DATABASE=db_afcgym
DB_USERNAME=root
DB_PASSWORD=root
```

### 2. Pasang dependensi & siapkan data

```bash
composer install
php artisan migrate:fresh --seed
```

### 3. Jalankan

```bash
php artisan serve --port=8123
```

Buka `http://127.0.0.1:8123`.

### Akun Demo

| Peran | Email | Password |
|---|---|---|
| Admin | `admin@afcgym.test` | `admin123` |
| Member (aktif, sisa 19 hari) | `budi.santoso@afcgym.test` | `member123` |
| Member (expired) | `dewi.lestari@afcgym.test` | `member123` |

Semua member hasil seeder memakai password `member123`.

Data contoh berisi 6 paket, 11 member, 8 langganan (2 sudah expired, 3 hampir habis), dan 3 tamu per latihan.

---

## 5. Peran Pengguna

### Admin (role = 1)
Petugas gym yang mengoperasikan sistem. Bisa mengakses semua menu:

- Beranda (ringkasan kondisi gym)
- Data Member (tambah, ubah, hapus, cetak kartu)
- Paket (daftar harga)
- Langganan (mencatat pembelian paket)
- **Kasir Latihan** (menerima pembayaran per kedatangan)
- Check-in (member berpaket masuk gratis)
- Laporan

### Member (role = 0)
Pemilik akun yang bisa melihat datanya sendiri. Hanya tiga halaman:

- Beranda (status keanggotaan, sisa hari, kunjungan terakhir)
- Kartu Member (QR untuk ditunjukkan ke petugas)
- Riwayat (daftar langganan dan kunjungan)

**Penting:** member yang tidak punya HP atau tidak mau memakai aplikasi **tetap bisa menjadi anggota** — dia tidak perlu akun, hanya diberi kartu fisik.

---

## 6. Struktur Database

Enam tabel utama.

### `user`
Akun login untuk admin dan member.

| Kolom | Keterangan |
|---|---|
| `role` | 1 = Admin, 0 = Member |
| `status` | 1 = Aktif, 0 = Nonaktif |
| `member_id` | Menghubungkan akun member ke data member (boleh kosong untuk admin) |

### `member`
Data orang yang terdaftar di gym, terlepas dari punya akun atau tidak.

| Kolom | Keterangan |
|---|---|
| `kode_member` | Unik, format `AFC-0001`, dipakai di kartu dan QR |
| `no_hp` | Boleh kosong (tamu sering tidak mau memberi nomor) |
| `tanggal_daftar` | Tanggal pertama terdaftar |

### `paket`
Daftar harga yang dijual. Punya **dua jenis**:

| `jenis` | Arti | `durasi_hari` |
|---|---|---|
| `paket` | Berlangganan (mingguan, bulanan, dst) | 7, 30, 90, … |
| `latihan` | Bayar per kedatangan | 0 (tidak terikat hari) |

### `langganan`
Catatan setiap kali member membeli paket berdurasi.

| Kolom | Keterangan |
|---|---|
| `tanggal_mulai` / `tanggal_berakhir` | Masa aktif |
| `harga_bayar` | Harga saat transaksi (disimpan terpisah dari harga paket, supaya perubahan harga tidak mengubah riwayat) |
| `status` | 1 = Aktif, 0 = Dibatalkan |

### `kunjungan`
Catatan setiap kali seseorang masuk gym.

| Kolom | Keterangan |
|---|---|
| `waktu_kunjungan` | Tanggal dan jam masuk |
| `tarif` | 0 kalau gratis (punya paket), atau nominal yang dibayar |
| `jenis` | `paket` (gratis) atau `latihan` (bayar per kedatangan) |
| `dicatat_oleh` | Admin yang mencatat |

### `pembayaran`
Catatan uang yang masuk. Bisa berasal dari dua sumber:

| Kolom | Sumber |
|---|---|
| `langganan_id` terisi | Pembayaran paket berdurasi |
| `kunjungan_id` terisi | Pembayaran per latihan |

Hanya salah satu yang terisi, tidak pernah dua-duanya. Inilah yang membuat laporan bisa memisahkan pendapatan.

### Relasi antar tabel

```
user ──── member_id ───► member
                          │
                          ├──► langganan ──► paket
                          │        │
                          │        └──► pembayaran
                          │
                          └──► kunjungan ──► pembayaran
```

Dibaca begini:
- Satu **member** punya banyak **langganan** (setiap perpanjangan jadi satu baris).
- Satu **langganan** memakai satu **paket** dan menghasilkan satu **pembayaran**.
- Satu **member** punya banyak **kunjungan**.
- **Kunjungan** yang berbayar menghasilkan satu **pembayaran**.

---

## 7. Alur Kerja Sistem

### A. Member mendaftar (dilakukan admin)

1. Admin buka **Data Member → Tambah Member**.
2. Isi nama (wajib), no HP (opsional), tanggal daftar.
3. **Email dan password boleh dikosongkan** kalau member tidak memakai HP.
4. Simpan → sistem membuat `kode_member` otomatis (`AFC-0001`, `AFC-0002`, …).
5. Layar langsung pindah ke detail member → admin menekan **Cetak Kartu**.

### B. Member membeli paket

1. Admin buka **Langganan → Tambah Langganan**.
2. Pilih member, pilih paket, isi metode bayar dan tanggal bayar.
3. Sistem menghitung tanggal berakhir:

   ```
   tanggal_mulai    = hari ini
   tanggal_berakhir = tanggal_mulai + (durasi_hari - 1)
   ```

4. **Kalau member masih punya paket aktif**, perpanjangan **menyambung** dari tanggal berakhir yang lama, bukan dari hari ini. Jadi masa aktif tidak hangus kalau bayar lebih awal:

   ```
   tanggal_mulai = tanggal_berakhir lama + 1 hari
   ```

5. Tersimpan dua baris: satu di `langganan`, satu di `pembayaran`.

### C. Member datang (punya paket aktif)

Ini kasus paling sering.

1. Member menyerahkan **kartu fisik** (atau menunjukkan QR dari HP kalau punya akun).
2. Admin buka **Check-in**, memindai QR atau mengetik kode member.
3. Sistem mencatat kunjungan dengan `tarif = 0`, `jenis = paket`.
4. Muncul pesan: *"Check-in berhasil: Budi Santoso (sisa 19 hari)"*.

Kalau member sudah check-in kurang dari 1 jam sebelumnya, sistem menolak untuk mencegah pencatatan ganda.

### D. Member datang (tidak punya paket aktif)

Ini kasus bayar per latihan.

1. Admin buka **Kasir Latihan**.
2. Ketik sebagian nama → daftar nama muncul → klik orangnya.
3. Sistem otomatis menentukan statusnya, admin tidak perlu memilih:
   - **Punya paket aktif** → tombol "Catat Masuk — Gratis", tidak ada tagihan.
   - **Sudah bayar latihan hari ini** → "Tidak perlu ditagih lagi".
   - **Belum/tidak berpaket** → tampil form pembayaran.
4. Admin pilih tarif (misal Rp 20.000), isi **uang diterima** (misal Rp 50.000).
5. Layar **langsung menampilkan kembalian sebagai angka besar**: "Kembalian Rp 30.000".
6. Tekan **Bayar & Masuk** → satu transaksi menyimpan tiga hal sekaligus: kunjungan, pembayaran, dan tarif.

**Aturan penting:** satu pembayaran berlaku **sampai gym tutup, tanpa batas jam**. Kalau orang itu datang lagi di hari yang sama, sistem mendeteksinya dari data kunjungan `jenis = latihan` hari itu dan **tidak menagih dua kali**.

Pendapatan dipisah di laporan: **Dari Paket** vs **Dari Latihan**.

### E. Tamu yang belum pernah datang

1. Di halaman Kasir ada panel **"Tamu belum terdaftar?"**.
2. Admin isi nama saja (no HP boleh kosong) → **Daftarkan Tamu**.
3. Sistem membuat member baru dengan kode otomatis dan langsung membuka form pembayaran untuk orang tersebut.
4. Tamu itu bisa langsung diberi kartu fisik kalau akan sering datang.

---

## 8. Kartu Fisik

Member yang tidak memakai HP tetap punya identitas: kasir mencetak kartu dari detail member
(`backend/member/{id}/kartu`). Tombol **Cetak Kartu** ada di halaman detail dan edit member,
jadi cetak ulang saat kartu hilang tidak perlu verifikasi apa pun.

Kartu memuat nama, kode member besar, dan QR berisi kode member. Ukuran cetak 90×60 mm, hitam-putih ramah printer biasa. Kode dicetak besar justru sebagai jaring pengaman kalau QR luntur atau kamera rewel.

---

## 9. Keputusan Desain yang Perlu Dijelaskan

Bagian ini yang biasanya ditanya dosen.

### 9.1 Status aktif tidak disimpan di database

Tidak ada kolom `aktif` di tabel `member`. Status **dihitung** dari tanggal berakhir langganan terakhir:

```php
public function getAktifAttribute(): bool
{
    $langganan = $this->langgananTerakhir;
    return $langganan && $langganan->tanggal_berakhir->endOfDay()->isFuture();
}
```

**Alasannya:** kalau status disimpan sebagai kolom, setiap hari sistem harus punya proses terjadwal untuk mengubah member yang expired menjadi tidak aktif. Kalau proses itu gagal jalan, datanya jadi salah. Dengan menghitung, status selalu benar tanpa perlu di-update.

### 9.2 Harga disimpan di dua tempat

`paket.harga` adalah harga jual saat ini. `langganan.harga_bayar` adalah harga yang benar-benar dibayar saat transaksi.

**Alasannya:** kalau gym menaikkan harga paket bulanan tahun depan, riwayat transaksi tahun ini tidak boleh ikut berubah. Laporan pendapatan harus mencerminkan uang yang benar-benar diterima.

### 9.3 Tabel pembayaran melayani dua sumber

Awalnya `pembayaran` hanya bisa menempel ke `langganan`. Setelah ada bayar per latihan, pembayaran juga bisa menempel ke `kunjungan`.

**Alasannya:** kalau bayar per latihan dibuat sebagai tabel pembayaran sendiri, laporan pendapatan harus menjumlahkan dua tabel. Dengan satu tabel yang punya dua kemungkinan sumber, total pendapatan tetap satu query, dan pemisahan paket vs latihan cukup dari kolom mana yang terisi.

### 9.4 Member boleh tanpa akun

`user.email` tetap wajib (kebutuhan tabel), tetapi **pembuatan akun hanya terjadi kalau admin mengisi email**. Member tanpa akun tetap punya `kode_member` dan kartu fisik.

**Alasannya:** orang yang tidak memakai HP tetap harus bisa jadi anggota. Kalau pendaftaran mewajibkan email, petugas akan menolak melayani mereka, atau mengarang email palsu yang membuat data kotor.

### 9.5 QR dibuat di browser, bukan di server

Isi QR hanyalah teks `kode_member`. Membuatnya di server butuh library tambahan dan menyimpan file gambar. Dibuat di browser lewat `qrcode.js`, hasilnya sama, tanpa beban server, dan kartu fisik maupun kartu digital memakai kode yang sama.

---

## 10. Hak Akses

Dua lapis pengamanan:

1. **Middleware `auth`** — harus login.
2. **Middleware `role`** (buatan sendiri, `App\Http\Middleware\RoleMiddleware`) — memeriksa angka role.

```php
// routes/web.php
Route::middleware(['auth', 'role:1'])->group(function () {   // admin
    Route::get('backend/beranda', ...);
    ...
});

Route::middleware(['auth', 'role:0'])->group(function () {   // member
    Route::get('member/beranda', ...);
    ...
});
```

Kalau member mencoba membuka halaman admin, dia langsung dikeluarkan dan dikembalikan ke login dengan pesan *"Anda tidak memiliki hak akses"*.

---

## 11. Struktur Folder yang Relevan

```
app/
├── Helpers/
│   ├── helpers.php          # fungsi rupiah() dan tanggal_id()
│   └── ImageHelper.php      # upload + resize foto member
├── Http/
│   ├── Controllers/         # satu controller per entitas
│   └── Middleware/RoleMiddleware.php
└── Models/                  # User, Member, Paket, Langganan, Kunjungan, Pembayaran

database/
├── migrations/              # skema tabel, urut berdasarkan waktu
└── seeders/DatabaseSeeder.php   # data contoh

resources/views/
├── auth/login.blade.php            # halaman masuk (admin & member)
├── admin/                          # khusus administrator
│   ├── layouts/app.blade.php       # layout sidebar gelap
│   ├── layouts/sidebar.blade.php
│   ├── beranda/index.blade.php
│   ├── member/        index|create|edit|show|kartu
│   ├── paket/         index|create|edit
│   ├── langganan/     index|create|show
│   ├── kasir/         index
│   ├── kunjungan/     index
│   └── laporan/       index
└── member/                         # khusus member, TANPA sidebar
    ├── layouts/app.blade.php       # layout navbar atas
    ├── layouts/partials/navbar.blade.php
    ├── beranda.blade.php           # hero + ringkasan, bukan dashboard
    ├── kartu.blade.php
    └── riwayat.blade.php

public/
├── assets/css/app.css       # seluruh tampilan sistem
└── vendor/                  # Bootstrap, Icons, DataTables, jQuery, qrcode
```

Controller admin memakai `view('admin.*')`, controller member memakai `view('member.*')`, login memakai `view('auth.login')`.

**Pemisahan view admin dan member** dilakukan supaya keduanya punya identitas berbeda: admin memakai sidebar gelap seperti aplikasi back-office, member memakai navbar atas tanpa sidebar karena hanya butuh tiga halaman.

---

## 12. Fungsi Bantuan

Dua helper global, dipakai di seluruh tampilan:

```php
rupiah(250000)             // "Rp 250.000"
tanggal_id('2026-11-20')   // "20 November 2026"
```

`ImageHelper::uploadAndResize()` memakai GD untuk mengecilkan foto member menjadi 385×400 piksel saat diunggah, supaya file tidak membengkak dan halaman tetap ringan.

---

## 13. Catatan Logika Tambahan

- Perpanjangan saat masih aktif: `tanggal_mulai = tanggal_berakhir_lama + 1 hari`.
- Role disimpan di kolom `user.role`: `1` = Admin, `0` = Member. Middleware `role` ada di `app/Http/Middleware/RoleMiddleware.php`.
- Check-in ganda dalam 1 jam ditolak (halaman Check-in).
- Member tanpa paket aktif yang di-check-in di halaman Check-in otomatis dialihkan ke Kasir, supaya pembayarannya tidak hilang dari laporan.

---

## 14. Urutan Demonstrasi yang Disarankan

Supaya presentasi mengalir dan setiap fitur terlihat:

1. **Login sebagai admin** → tunjukkan beranda: jumlah member aktif, kunjungan hari ini, peringatan member akan expired.
2. **Data Member** → tunjukkan tabel dengan status aktif/tidak. Buka satu member, tekan **Cetak Kartu** untuk memperlihatkan kartu fisik.
3. **Tambah member baru tanpa email** → jelaskan bahwa member gaptek tetap bisa terdaftar; langsung tercetak kartu.
4. **Langganan** → catat pembelian paket untuk member baru itu, tunjukkan tanggal berakhir dihitung otomatis. Lalu catat lagi paket yang sama → tunjukkan tanggal mulai **menyambung** dari tanggal berakhir lama.
5. **Kasir Latihan** → cari nama tamu, tunjukkan layar menampilkan tarif, isi uang Rp 50.000, tunjukkan **kembalian muncul sebagai angka besar**, tekan Bayar & Masuk.
6. **Kasir lagi untuk orang yang sama** → tunjukkan sistem berkata *"sudah membayar latihan hari ini"*, tidak menagih dua kali.
7. **Check-in** → masukkan kode member berpaket (misal `AFC-0001`) → gratis. Lalu coba kode member expired → sistem otomatis mengalihkan ke Kasir.
8. **Laporan** → tunjukkan empat kartu: Total Pendapatan, Dari Paket, Dari Latihan, Jumlah Kunjungan.
9. **Login sebagai member** (`budi.santoso@afcgym.test`) → tunjukkan tampilan member berbeda (navbar atas, bukan sidebar), buka Kartu Member dengan QR, lalu Riwayat.
10. **Coba akses paksa** → saat login sebagai member, buka `backend/beranda` → ditolak. Ini bukti middleware peran bekerja.

---

## 15. Batasan Sistem (Jujur untuk Disebutkan)

Disebutkan lebih dulu supaya tidak terkesan klaim berlebihan:

1. **Belum ada pembayaran online.** Semua pembayaran dicatat setelah uang diterima petugas. Bagian "member membeli sendiri lewat akun" sudah dirancang tapi belum dibuat; saat ini semua transaksi lewat kasir.
2. **Belum ada laporan export Excel/PDF.** Laporan masih tampil di layar dengan filter tanggal.
3. **Belum ada pengelolaan jadwal kelas atau alat.** Paket Premium bertuliskan "akses alat + kelas", tetapi modul kelas belum dibuat.
4. **Belum ada notifikasi otomatis** (WhatsApp/SMS) untuk member yang akan expired. Peringatan baru muncul di beranda admin.
5. **Absensi tidak memakai fingerprint atau wajah.** Kartu dan QR adalah identitas utama; keamanannya bergantung pada petugas yang memeriksa.
6. **Belum ada audit log.** Kolom `dicatat_oleh` mencatat admin mana yang memasukkan kunjungan, tapi perubahan data member tidak dilacak riwayatnya.

---

## 16. Kemungkinan Pertanyaan Dosen

**"Kenapa status aktif tidak disimpan?"**
Karena status berubah sendiri seiring waktu. Menyimpannya berarti butuh proses terjadwal harian; kalau gagal, data salah. Menghitung dari tanggal berakhir selalu benar.

**"Kalau member bayar lebih awal, harinya hangus?"**
Tidak. Perpanjangan dihitung menyambung dari tanggal berakhir lama, bukan dari hari pembayaran. Ini sudah diuji.

**"Bagaimana kalau member kehilangan kartu?"**
Buka detail member → Cetak Kartu. Kode member tetap sama, jadi riwayat kunjungan tidak terputus.

**"Bagaimana kalau tamu tidak mau memberi nama?"**
Bisa dicatat sebagai "Tamu" dengan kode otomatis. Sistem tidak memaksa kelengkapan data, karena menolak transaksi berarti kehilangan pendapatan.

**"Bagaimana mencegah orang masuk tanpa bayar?"**
Secara teknis sistem mencatat, bukan mengunci pintu. Pencegahan fisik tetap tugas petugas. Yang sistem lakukan: membuat setiap kunjungan punya catatan tarif, sehingga penyimpangan bisa terlihat di laporan.

**"Kenapa tidak memakai template admin jadi?"**
Tampilan ditulis sendiri karena template admin yang umum dipakai terlihat seperti dashboard perusahaan, bukan gym. Library tetap dipakai (Bootstrap, DataTables), yang ditulis sendiri hanya lapisan tampilannya.

**"Apakah bisa dikembangkan ke banyak cabang?"**
Perlu tambahan tabel `cabang` dan kolom `cabang_id` di `member`, `paket`, dan `kunjungan`. Struktur sekarang belum mendukungnya.

---

## 17. Ringkasan Satu Paragraf

AFCGym mencatat keanggotaan gym, masa aktif langganan, dan setiap kedatangan. Status aktif dihitung otomatis dari tanggal berakhir, bukan disimpan, sehingga tidak perlu proses terjadwal. Admin dapat mencatat penjualan paket maupun pembayaran per kedatangan dari satu layar kasir, lengkap dengan hitungan kembalian. Member yang memakai HP bisa melihat kartu digital dan riwayatnya sendiri, sementara member yang tidak memakai HP tetap terlayani lewat kartu fisik yang dicetak petugas. Semua uang yang masuk tercatat di satu tabel pembayaran dengan penanda sumber, sehingga laporan bisa memisahkan pendapatan paket dari pendapatan harian.
