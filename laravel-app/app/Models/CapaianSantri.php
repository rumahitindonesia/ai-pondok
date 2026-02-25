<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CapaianSantri extends Model
{
    protected $table = 'capaian_santris';
    protected $fillable = ['santri_id', 'materi_id', 'tanggal_selesai', 'nilai'];

    public function materi(): BelongsTo
    {
        return $this->belongsTo(Materi::class);
    }

    public function santri(): BelongsTo
    {
        return $this->belongsTo(Santri::class);
    }
}
