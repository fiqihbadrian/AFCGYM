<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    use HasFactory;

    public const STATUS_MENUNGGU = 'menunggu';
    public const STATUS_DIPROSES = 'diproses';

    protected $table = 'pesanan';

    protected $fillable = [
        'member_id',
        'paket_id',
        'jumlah',
        'metode',
        'status',
        'diproses_oleh',
        'diproses_at',
    ];

    protected $casts = [
        'jumlah'      => 'decimal:2',
        'diproses_at' => 'datetime',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function paket()
    {
        return $this->belongsTo(Paket::class);
    }

    public function pemroses()
    {
        return $this->belongsTo(User::class, 'diproses_oleh');
    }

    /**
     * Pesanan yang belum dikonfirmasi petugas (belum dibayar).
     */
    public function scopeMenunggu($query)
    {
        return $query->where('status', self::STATUS_MENUNGGU);
    }

    public function scopeSudahDiproses($query)
    {
        return $query->where('status', self::STATUS_DIPROSES);
    }

    public function getSudahDiprosesAttribute(): bool
    {
        return $this->status === self::STATUS_DIPROSES;
    }
}
