<?php

namespace App\Console\Commands;

use App\Models\Zone;
use App\Models\ZonePoint;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * php artisan firebase:import-zones
 *
 * Reads firebase_export/zone.json and inserts records into `zones` + `zone_points`.
 * Firestore stored GeoPoint arrays as { lat, lng } objects — these become individual
 * zone_points rows, replacing the old JSON `coordinates` column.
 *
 * Run AFTER: php artisan migrate
 */
class MigrateFirebaseZones extends Command
{
    protected $signature   = 'firebase:import-zones
                                {--file=firebase_export/zone.json}
                                {--dry-run}';

    protected $description = 'Import zones (+ GeoPoints → zone_points) from Firebase export JSON';

    public function handle(): int
    {
        $file = base_path($this->option('file'));

        if (!file_exists($file)) {
            $this->error("File not found: $file  —  Run: node export_firebase.js");
            return self::FAILURE;
        }

        $zones  = json_decode(file_get_contents($file), true);
        $dryRun = $this->option('dry-run');
        $count  = 0; $skipped = 0;

        $this->info('Importing ' . count($zones) . ' zones' . ($dryRun ? ' [DRY RUN]' : '') . '...');

        foreach ($zones as $doc) {
            $firebaseId = $doc['_id'];

            $existing = Zone::where('firebase_doc_id', $firebaseId)->first();
            if ($existing) {
                $this->line("  Skip zone: {$doc['name']} (already imported)");
                $skipped++;
                continue;
            }

            // Firestore field names vary — support: area, coordinates, polygon, points
            $geoPoints = $doc['area']        // array of {lat, lng} from export_firebase.js
                      ?? $doc['coordinates']
                      ?? $doc['polygon']
                      ?? $doc['points']
                      ?? [];

            if (!$dryRun) {
                DB::transaction(function () use ($doc, $firebaseId, $geoPoints) {
                    $zone = Zone::create([
                        'firebase_doc_id' => $firebaseId,
                        'zone_name'       => $doc['name'] ?? $doc['zone_name'] ?? 'Zone ' . $firebaseId,
                        'description'     => $doc['description'] ?? null,
                        'status'          => ($doc['active'] ?? true) ? 'active' : 'inactive',
                        'created_at'      => $doc['created_at'] ?? now(),
                        'updated_at'      => $doc['updated_at'] ?? now(),
                    ]);

                    foreach ($geoPoints as $i => $point) {
                        ZonePoint::create([
                            'zone_id'    => $zone->id,
                            'latitude'   => (float) ($point['lat'] ?? $point['latitude'] ?? 0),
                            'longitude'  => (float) ($point['lng'] ?? $point['longitude'] ?? 0),
                            'sort_order' => $i,
                        ]);
                    }

                    $this->line("  ✓ Zone: {$zone->zone_name} (" . count($geoPoints) . " points)");
                });
            } else {
                $this->line("  [dry] Zone: " . ($doc['name'] ?? $firebaseId) . " (" . count($geoPoints) . " points)");
            }

            $count++;
        }

        $this->info("✅ Imported: $count zones | Skipped: $skipped");
        return self::SUCCESS;
    }
}
