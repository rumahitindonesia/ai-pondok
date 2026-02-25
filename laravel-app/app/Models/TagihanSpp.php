<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TagihanSpp extends Model
{
    protected $fillable = [
        'santri_id',
        'jenis_pembayaran_id',
        'bulan',
        'tahun',
        'jumlah',
        'jatuh_tempo',
        'keterangan',
        'status',
    ];

    public function jenisPembayaran()
    {
        return $this->belongsTo(JenisPembayaran::class);
    }

    public function santri()
    {
        return $this->belongsTo(Santri::class);
    }

    public function pembayarans()
    {
        return $this->hasMany(Pembayaran::class, 'tagihan_id');
    }
}
