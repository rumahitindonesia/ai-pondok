<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jabatan extends Model
{
    use HasFactory;

    protected $fillable = ['nama', 'parent_id', 'deskripsi'];

    // Hierarchical relationships
    public function parent()
    {
        return $this->belongsTo(Jabatan::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Jabatan::class, 'parent_id');
    }

    // Recursively load all descendants to unlimited depth
    public function childrenRecursive()
    {
        return $this->hasMany(Jabatan::class, 'parent_id')
            ->with('childrenRecursive');
    }

    // Pivot table relationship
    public function pengurus()
    {
        return $this->hasMany(Pengurus::class);
    }
    
    // Direct relationship to Santri through pengurus pivot
    public function pejabat()
    {
        return $this->belongsToMany(Santri::class, 'pengurus')->withPivot('status')->withTimestamps();
    }
}
