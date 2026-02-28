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
        'entitas',
        'angkatan',
    ];

    protected static function booted()
    {
        static::saving(function ($santri) {
            // 1. Auto-calculate angkatan from the first 3 digits of NIS if not provided
            if (empty($santri->angkatan) && !empty($santri->nis) && strlen($santri->nis) >= 3) {
                $prefix = substr($santri->nis, 0, 3);
                if (is_numeric($prefix)) {
                    $santri->angkatan = (int) $prefix;
                }
            }

            // 2. Auto-generate NIS from angkatan if NIS is missing
            if (empty($santri->nis) && !empty($santri->angkatan)) {
                $angkatanPrefix = str_pad((int)$santri->angkatan, 3, '0', STR_PAD_LEFT);

                // Find the latest NIS for this angkatan to determine the next sequence
                // We use static::where to avoid issues with $this context in saving event if needed
                $latestSantri = static::where('nis', 'like', $angkatanPrefix . '%')
                    ->orderByRaw('CAST(SUBSTRING(nis, 4) AS UNSIGNED) DESC')
                    ->first();

                $nextSequence = 1;
                if ($latestSantri && strlen($latestSantri->nis) > 3) {
                    $lastSequence = (int)substr($latestSantri->nis, 3);
                    $nextSequence = $lastSequence + 1;
                }

                // Format sequence as 3 digits
                $sequenceString = str_pad($nextSequence, 3, '0', STR_PAD_LEFT);
                $santri->nis = $angkatanPrefix . $sequenceString;
            }
        });
    }

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
