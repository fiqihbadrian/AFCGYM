<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;

    protected $table = 'member';

    protected $fillable = [
        'kode_member',
        'nama',
        'nik',
        'no_hp',
        'alamat',
        'foto',
        'tanggal_daftar',
    ];

    protected $casts = [
        'tanggal_daftar' => 'date',
    ];

    public function langganan()
    {
        return $this->hasMany(Langganan::class);
    }

    /**
     * Langganan terbaru milik member (dipakai untuk menentukan status aktif).
     */
    public function langgananTerakhir()
    {
        return $this->hasOne(Langganan::class)->latestOfMany();
    }

    public function kunjungan()
    {
        return $this->hasMany(Kunjungan::class);
    }

    public function akun()
    {
        return $this->hasOne(User::class);
    }

    /**
     * Kunjungan berbayar pada tanggal tertentu (default hari ini).
     * Dipakai supaya tamu per latihan tidak ditagih dua kali di hari yang sama.
     */
    public function kunjunganBerbayar($tanggal = null)
    {
        $tanggal = $tanggal ?: now();

        return $this->kunjungan()
            ->where('jenis', 'latihan')
            ->whereDate('waktu_kunjungan', $tanggal->toDateString());
    }

    public function getSudahBayarHariIniAttribute(): bool
    {
        return $this->kunjunganBerbayar()->exists();
    }

    /**
     * Status keaktifan dihitung dari tanggal berakhir langganan terakhir.
     */
    public function getAktifAttribute(): bool
    {
        $langganan = $this->langgananTerakhir;

        return $langganan && $langganan->tanggal_berakhir->endOfDay()->isFuture();
    }

    public function getSisaHariAttribute(): int
    {
        $langganan = $this->langgananTerakhir;

        if (! $langganan) {
            return 0;
        }

        $sisa = now()->startOfDay()->diffInDays($langganan->tanggal_berakhir->startOfDay(), false);

        return max(0, (int) $sisa);
    }

    /**
     * Kode member otomatis, contoh: AFC-0001
     */
    public static function generateKode(): string
    {
        $terakhir = static::orderByDesc('id')->first();
        $nomor = $terakhir ? (int) substr($terakhir->kode_member, -4) + 1 : 1;

        return 'AFC-' . str_pad($nomor, 4, '0', STR_PAD_LEFT);
    }
}
