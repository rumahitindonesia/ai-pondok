<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormQuestion extends Model
{
    protected $fillable = [
        'question_text',
        'type',
        'options',
        'is_required',
        'order_num',
    ];

    protected $casts = [
        'options' => 'array',
        'is_required' => 'boolean',
    ];

    public function responses()
    {
        return $this->hasMany(FormResponse::class);
    }
}
