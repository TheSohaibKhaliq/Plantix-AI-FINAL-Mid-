<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ZonePoint extends Model
{
    public $timestamps = false;

    protected $fillable = ['zone_id', 'latitude', 'longitude', 'sort_order'];

    protected $casts = [
        'latitude'   => 'decimal:8',
        'longitude'  => 'decimal:8',
    ];

    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class);
    }
}
