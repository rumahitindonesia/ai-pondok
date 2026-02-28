<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisPembayaran extends Model
{
    protected $fillable = [
        'nama',
        'kode',
        'sifat',
        'nominal_default',
        'keterangan',
    ];

    public function tagihanSpps()
    {
        return $this->hasMany(TagihanSpp::class);
    }
}
