<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pelanggaran extends Model
{
    protected $fillable = [
        'santri_id',
        'judul_pelanggaran',
        'deskripsi',
        'poin',
        'tanggal',
    ];

    public function santri()
    {
        return $this->belongsTo(Santri::class);
    }
}
