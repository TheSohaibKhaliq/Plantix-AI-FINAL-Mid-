<?php

namespace App\Services;

use App\Models\Zone;
use App\Models\ZonePoint;
use Illuminate\Support\Facades\DB;

class ZoneService
{
    /**
     * Create a zone with its polygon points.
     * Replaces the Firestore JS: database.collection('zone').doc(id).set({area:[GeoPoint,...]})
     *
     * @param  string  $name
     * @param  array   $points  [ ['lat' => float, 'lng' => float], ... ]
     */
    public function create(string $name, array $points, ?string $description = null): Zone
    {
        return DB::transaction(function () use ($name, $points, $description) {
            $zone = Zone::create([
                'zone_name'   => $name,
                'description' => $description,
                'status'      => 'active',
            ]);

            $this->syncPoints($zone, $points);

            return $zone->load('points');
        });
    }

    /**
     * Update zone name/description and replace all points.
     */
    public function update(Zone $zone, string $name, array $points, ?string $description = null): Zone
    {
        return DB::transaction(function () use ($zone, $name, $points, $description) {
            $zone->update([
                'zone_name'   => $name,
                'description' => $description,
            ]);

            $this->syncPoints($zone, $points);

            return $zone->fresh('points');
        });
    }

    public function delete(Zone $zone): void
    {
        DB::transaction(function () use ($zone) {
            $zone->points()->delete();
            $zone->delete();
        });
    }

    /**
     * Check if a lat/lng coordinate falls inside a given zone polygon.
     * Uses the ray-casting algorithm.
     */
    public function containsPoint(Zone $zone, float $lat, float $lng): bool
    {
        $points  = $zone->points()->orderBy('sort_order')->get();
        $n       = $points->count();
        $inside  = false;

        for ($i = 0, $j = $n - 1; $i < $n; $j = $i++) {
            $xi = (float) $points[$i]->latitude;
            $yi = (float) $points[$i]->longitude;
            $xj = (float) $points[$j]->latitude;
            $yj = (float) $points[$j]->longitude;

            $intersect = (($yi > $lng) !== ($yj > $lng))
                && ($lat < ($xj - $xi) * ($lng - $yi) / ($yj - $yi) + $xi);

            if ($intersect) $inside = !$inside;
        }

        return $inside;
    }

    // -------------------------------------------------------------------------
    // Private
    // -------------------------------------------------------------------------

    private function syncPoints(Zone $zone, array $points): void
    {
        $zone->points()->delete();

        $rows = [];
        foreach ($points as $index => $point) {
            $rows[] = [
                'zone_id'    => $zone->id,
                'latitude'   => $point['lat'],
                'longitude'  => $point['lng'],
                'sort_order' => $index,
            ];
        }

        if (!empty($rows)) {
            ZonePoint::insert($rows);
        }
    }
}
