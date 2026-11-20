<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    use HasFactory;

    protected $table = 'pembayaran';

    public const SUMBER_PAKET = 'langganan';
    public const SUMBER_LATIHAN = 'latihan';

    protected $fillable = [
        'langganan_id',
        'kunjungan_id',
        'jumlah',
        'metode',
        'tanggal_bayar',
        'keterangan',
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
        'tanggal_bayar' => 'date',
    ];

    public function langganan()
    {
        return $this->belongsTo(Langganan::class);
    }

    public function kunjungan()
    {
        return $this->belongsTo(Kunjungan::class);
    }

    /**
     * Pendapatan paket atau pendapatan per latihan.
     */
    public function getSumberAttribute(): string
    {
        return $this->kunjungan_id ? self::SUMBER_LATIHAN : self::SUMBER_PAKET;
    }
}
