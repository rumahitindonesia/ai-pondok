<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContentRequest extends Model
{
    protected $fillable = [
        'user_id',
        'jabatan_id',
        'requester_name',
        'phone_number',
        'format',
        'deadline_date',
        'deadline_time',
        'size',
        'output_format',
        'include_logo',
        'include_website',
        'include_social_media',
        'include_phone',
        'content_description',
        'special_notes',
        'reference_link',
        'status',
        'assigned_to',
        'assigned_by',
        'published_at',
        'published_url',
        'reach_count',
        'engagement_count',
        'link_clicks',
        'insight_notes',
    ];

    protected $casts = [
        'deadline_date' => 'date',
        'include_logo' => 'boolean',
        'include_website' => 'boolean',
        'include_social_media' => 'boolean',
        'include_phone' => 'boolean',
        'published_at' => 'datetime',
        'reach_count' => 'integer',
        'engagement_count' => 'integer',
        'link_clicks' => 'integer',
    ];

    public function requester()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class, 'jabatan_id');
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
