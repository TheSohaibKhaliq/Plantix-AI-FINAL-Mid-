<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookedTable extends Model
{
    protected $fillable = [
        'vendor_id', 'user_id', 'booking_date', 'booking_time',
        'party_size', 'status', 'notes',
    ];

    protected $casts = [
        'booking_date' => 'date',
    ];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
