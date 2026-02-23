<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Zone extends Model
{
    protected $fillable = ['zone_name', 'description', 'status', 'firebase_doc_id'];

    public function vendors(): HasMany
    {
        return $this->hasMany(Vendor::class);
    }

    /** New: individual coordinate rows */
    public function points(): HasMany
    {
        return $this->hasMany(ZonePoint::class);
    }

    /** Legacy: keep until ZoneArea migration is complete */
    public function areas(): HasMany
    {
        return $this->hasMany(ZoneArea::class);
    }

    /** Polygon coordinates as [[lat, lng], ...] for Google Maps */
    public function getCoordinatesArrayAttribute(): array
    {
        return $this->points()
                    ->orderBy('sort_order')
                    ->get()
                    ->map(fn($p) => ['lat' => (float) $p->latitude, 'lng' => (float) $p->longitude])
                    ->toArray();
    }
}
