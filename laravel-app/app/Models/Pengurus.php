<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class Pengurus extends Pivot
{
    protected $table = 'pengurus';

    protected $fillable = ['santri_id', 'jabatan_id', 'status'];

    public function santri()
    {
        return $this->belongsTo(Santri::class);
    }

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class);
    }
}

