<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PsbRegistration extends Model
{
    protected $fillable = [
        'nama_calon',
        'jenis_kelamin',
        'nama_wali',
        'no_hp_wali',
        'berkas_kk',
        'berkas_akta',
        'berkas_ijazah',
        'status_seleksi',
    ];
}
