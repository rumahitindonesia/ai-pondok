<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    protected $fillable = [
        'tagihan_id',
        'tanggal_bayar',
        'jumlah_bayar',
        'metode_pembayaran',
        'bukti_pembayaran',
    ];

    public function tagihan()
    {
        return $this->belongsTo(TagihanSpp::class, 'tagihan_id');
    }
}
