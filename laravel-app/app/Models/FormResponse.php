<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormResponse extends Model
{
    protected $fillable = [
        'psb_registration_id',
        'form_question_id',
        'response_text',
        'response_file_path',
    ];

    public function registration()
    {
        return $this->belongsTo(PsbRegistration::class, 'psb_registration_id');
    }

    public function question()
    {
        return $this->belongsTo(FormQuestion::class, 'form_question_id');
    }
}
