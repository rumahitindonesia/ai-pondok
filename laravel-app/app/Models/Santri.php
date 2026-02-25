<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Santri extends Model
{
    protected $fillable = [
        'nis',
        'nama',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'alamat',
        'wali_id',
        'status',
        'kamar',
        'kelas',
        'foto',
        'bio',
        'minat_bakat',
        'cita_cita',
        'email',
        'no_hp',
    ];

    public function wali()
    {
        return $this->belongsTo(WaliSantri::class, 'wali_id');
    }

    public function tagihanSpps()
    {
        return $this->hasMany(TagihanSpp::class);
    }

    public function absensis()
    {
        return $this->hasMany(Absensi::class);
    }

    public function pelanggarans()
    {
        return $this->hasMany(Pelanggaran::class);
    }

    public function achievements()
    {
        return $this->hasMany(CapaianSantri::class);
    }

    public function portfolios()
    {
        return $this->hasMany(Portofolio::class);
    }
}
