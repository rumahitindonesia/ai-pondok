<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TagihanSpp extends Model
{
    protected $fillable = [
        'santri_id',
        'bulan',
        'tahun',
        'jumlah',
        'jatuh_tempo',
        'status',
    ];

    public function santri()
    {
        return $this->belongsTo(Santri::class);
    }

    public function pembayarans()
    {
        return $this->hasMany(Pembayaran::class, 'tagihan_id');
    }
}
