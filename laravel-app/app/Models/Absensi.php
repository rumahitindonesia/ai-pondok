<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    protected $fillable = [
        'santri_id',
        'jenis',
        'status',
        'tanggal',
        'keterangan',
    ];

    public function santri()
    {
        return $this->belongsTo(Santri::class);
    }

    public function jenisAbsensi()
    {
        return $this->belongsTo(JenisAbsensi::class, 'jenis_absensi_id');
    }
}
