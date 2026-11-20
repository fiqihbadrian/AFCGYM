<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kunjungan extends Model
{
    use HasFactory;

    protected $table = 'kunjungan';

    protected $fillable = [
        'member_id',
        'waktu_kunjungan',
        'tarif',
        'jenis',
        'dicatat_oleh',
    ];

    protected $casts = [
        'waktu_kunjungan' => 'datetime',
        'tarif'           => 'decimal:2',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function pencatat()
    {
        return $this->belongsTo(User::class, 'dicatat_oleh');
    }

    public function pembayaran()
    {
        return $this->hasOne(Pembayaran::class);
    }

    public function getBerbayarAttribute(): bool
    {
        return $this->jenis === 'latihan';
    }
}
