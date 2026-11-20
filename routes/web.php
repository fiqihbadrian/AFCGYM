<?php

use App\Http\Controllers\BerandaController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\KunjunganController;
use App\Http\Controllers\LanggananController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MemberAreaController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\PaketController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes AFCGym
|--------------------------------------------------------------------------
| Pola penamaan route mengikuti "backend." supaya konsisten dengan
| contoh project sebelumnya (TokoOnline).
*/

Route::get('/', function () {
    return redirect()->route('backend.login');
});

/*
|--------------------------------------------------------------------------
| Autentikasi
|--------------------------------------------------------------------------
*/
Route::get('backend/login', [LoginController::class, 'loginBackend'])->name('backend.login');
Route::post('backend/login', [LoginController::class, 'authenticateBackend'])->name('backend.login.aksi');
Route::post('backend/logout', [LoginController::class, 'logoutBackend'])->name('backend.logout');

/*
|--------------------------------------------------------------------------
| Area Admin
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:1'])->group(function () {
    Route::get('backend/beranda', [BerandaController::class, 'index'])->name('backend.beranda');

    Route::resource('backend/member', MemberController::class, ['as' => 'backend']);
    Route::resource('backend/paket', PaketController::class, ['as' => 'backend']);
    Route::resource('backend/langganan', LanggananController::class, ['as' => 'backend']);

    // Check-in kunjungan (member berpaket)
    Route::get('backend/kunjungan', [KunjunganController::class, 'index'])->name('backend.kunjungan.index');
    Route::post('backend/kunjungan', [KunjunganController::class, 'store'])->name('backend.kunjungan.store');

    // Kasir latihan (bayar per kedatangan)
    Route::get('backend/kasir', [KasirController::class, 'index'])->name('backend.kasir.index');
    Route::post('backend/kasir', [KasirController::class, 'store'])->name('backend.kasir.store');
    Route::post('backend/kasir/tamu', [KasirController::class, 'daftarTamu'])->name('backend.kasir.tamu');

    // Cetak kartu member
    Route::get('backend/member/{member}/kartu', [MemberController::class, 'kartu'])->name('backend.member.kartu');

    // Laporan
    Route::get('backend/laporan', [LaporanController::class, 'index'])->name('backend.laporan.index');
});

/*
|--------------------------------------------------------------------------
| Area Member
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:0'])->group(function () {
    Route::get('member/beranda', [MemberAreaController::class, 'index'])->name('member.beranda');
    Route::get('member/kartu', [MemberAreaController::class, 'kartu'])->name('member.kartu');
    Route::get('member/riwayat', [MemberAreaController::class, 'riwayat'])->name('member.riwayat');
});
