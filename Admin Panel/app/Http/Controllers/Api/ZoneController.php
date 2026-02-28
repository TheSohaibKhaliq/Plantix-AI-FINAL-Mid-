<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Zone;
use App\Models\ZoneArea;
use App\Services\Admin\ZoneService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * ZoneController (API)
 *
 * Replaces direct Firestore JS calls in:
 *   - zone/index.blade.php   (collection('zone'))
 *   - zone/create.blade.php  (collection('zone').doc(id).set({area:[GeoPoint,...]}))
 *   - zone/edit.blade.php    (collection('zone').doc(id).set({...}))
 */
class ZoneController extends Controller
{
    public function __construct(private readonly ZoneService $zoneService)
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * GET /api/zones
     */
    public function index(Request $request): JsonResponse
    {
        $zones = Zone::with('points')
                     ->when($request->status, fn($q, $v) => $q->where('status', $v))
                     ->skip((int) $request->get('skip', 0))
                     ->take((int) $request->get('limit', 50))
                     ->get()
                     ->map(fn(Zone $z) => array_merge($z->toArray(), [
                         'area' => $z->coordinates_array,
                     ]));

        return response()->json($zones);
    }

    /**
     * POST /api/zones
     * Replaces: database.collection('zone').doc(id).set({ id, name, area:[GeoPoint,...] })
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'zone_name'   => 'required|string|unique:zones',
            'description' => 'nullable|string',
            'area'        => 'required|array|min:3',
            'area.*.lat'  => 'required|numeric|between:-90,90',
            'area.*.lng'  => 'required|numeric|between:-180,180',
        ]);

        $zone = $this->zoneService->create(
            $request->zone_name,
            $request->area,
            $request->description,
        );

        return response()->json(array_merge($zone->toArray(), ['area' => $zone->coordinates_array]), 201);
    }

    /**
     * GET /api/zones/{zone}
     */
    public function show(Zone $zone): JsonResponse
    {
        return response()->json(array_merge($zone->toArray(), ['area' => $zone->coordinates_array]));
    }

    /**
     * PUT /api/zones/{zone}
     * Replaces: database.collection('zone').doc(id).set({ ... GeoPoint array ... })
     */
    public function update(Request $request, Zone $zone): JsonResponse
    {
        $request->validate([
            'zone_name'   => 'sometimes|string|unique:zones,zone_name,' . $zone->id,
            'description' => 'nullable|string',
            'status'      => 'sometimes|in:active,inactive',
            'area'        => 'sometimes|array|min:3',
            'area.*.lat'  => 'required_with:area|numeric|between:-90,90',
            'area.*.lng'  => 'required_with:area|numeric|between:-180,180',
        ]);

        if ($request->has('area')) {
            $zone = $this->zoneService->update(
                $zone,
                $request->get('zone_name', $zone->zone_name),
                $request->area,
                $request->description,
            );
        } else {
            $zone->update($request->only(['zone_name', 'description', 'status']));
        }

        return response()->json(array_merge($zone->toArray(), ['area' => $zone->coordinates_array]));
    }

    /**
     * DELETE /api/zones/{zone}
     * Replaces: database.collection('zone').doc(dataId).delete()
     */
    public function destroy(Zone $zone): JsonResponse
    {
        $this->zoneService->delete($zone);
        return response()->json(null, 204);
    }

    // -------------------------------------------------------------------------
    // Legacy ZoneArea endpoints — preserved for compatibility during transition
    // -------------------------------------------------------------------------

    public function addArea(Request $request, Zone $zone): JsonResponse
    {
        $validated = $request->validate([
            'latitude'  => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'address'   => 'nullable|string',
        ]);

        $area = $zone->areas()->create($validated);
        return response()->json($area, 201);
    }

    public function getAreas(Zone $zone): JsonResponse
    {
        return response()->json($zone->areas);
    }

    public function removeArea(Zone $zone, ZoneArea $area): JsonResponse
    {
        if ($area->zone_id !== $zone->id) {
            return response()->json(['error' => 'Area not found in this zone'], 404);
        }

        $area->delete();
        return response()->json(null, 204);
    }
}

