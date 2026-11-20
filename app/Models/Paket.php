<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paket extends Model
{
    use HasFactory;

    public const JENIS_PAKET = 'paket';
    public const JENIS_LATIHAN = 'latihan';

    protected $table = 'paket';

    protected $fillable = [
        'nama',
        'jenis',
        'durasi_hari',
        'harga',
        'keterangan',
        'status',
    ];

    protected $casts = [
        'harga' => 'decimal:2',
    ];

    public function langganan()
    {
        return $this->hasMany(Langganan::class);
    }

    /**
     * Paket berdurasi (mingguan, bulanan, dst).
     */
    public function scopeBerlangganan($query)
    {
        return $query->where('jenis', self::JENIS_PAKET);
    }

    /**
     * Paket bayar per kedatangan.
     */
    public function scopeLatihan($query)
    {
        return $query->where('jenis', self::JENIS_LATIHAN);
    }

    public function getIsLatihanAttribute(): bool
    {
        return $this->jenis === self::JENIS_LATIHAN;
    }
}
