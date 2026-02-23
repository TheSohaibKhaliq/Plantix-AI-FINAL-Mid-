<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = 'real_time_notifications';
    
    protected $fillable = [
        'title',
        'message',
        'type',
        'recipient_id',
        'read',
        'metadata',
        'sent_at'
    ];

    protected $casts = [
        'metadata' => 'array',
        'read' => 'boolean',
        'sent_at' => 'datetime'
    ];
}
