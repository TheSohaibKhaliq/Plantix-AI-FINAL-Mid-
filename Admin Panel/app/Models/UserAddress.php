<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAddress extends Model
{
    protected $fillable = [
        'user_id', 'label', 'address_line1', 'address_line2',
        'city', 'state', 'zip', 'country', 'lat', 'lng', 'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'lat'        => 'decimal:8',
        'lng'        => 'decimal:8',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Return the full address as a single string.
     */
    public function getFullAddressAttribute(): string
    {
        return collect([
            $this->address_line1,
            $this->address_line2,
            $this->city,
            $this->state,
            $this->zip,
            $this->country,
        ])->filter()->implode(', ');
    }
}
