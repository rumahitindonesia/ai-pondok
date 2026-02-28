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
        'referred_by',
        'token'
    ];

    public function tagihanPendaftarans()
    {
        return $this->hasMany(TagihanPendaftaran::class);
    }

    /**
     * Get the student answers for Stage 2.
     */
    public function responses()
    {
        return $this->hasMany(FormResponse::class);
    }

    /**
     * Get the user who referred this applicant.
     */
    public function referrer()
    {
        return $this->belongsTo(User::class, 'referred_by');
    }
}
