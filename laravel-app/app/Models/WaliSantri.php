<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WaliSantri extends Model
{
    protected $fillable = [
        'nama',
        'hubungan',
        'no_hp',
        'alamat',
        'email',
        'pekerjaan',
    ];

    public function santris()
    {
        return $this->hasMany(Santri::class, 'wali_id');
    }
}
