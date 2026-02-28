<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Portofolio extends Model
{
    protected $fillable = ['santri_id', 'judul', 'deskripsi', 'link_url', 'tanggal'];

    public function santri(): BelongsTo
    {
        return $this->belongsTo(Santri::class);
    }
}
