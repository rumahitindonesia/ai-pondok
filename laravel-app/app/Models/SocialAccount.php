<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocialAccount extends Model
{
    protected $fillable = [
        'user_id',
        'platform',
        'platform_user_id',
        'access_token',
        'token_expires_at',
        'instagram_username',
        'instagram_business_id',
        'facebook_page_id',
    ];

    protected $casts = [
        'access_token' => 'encrypted',
        'token_expires_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
