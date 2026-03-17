<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdController extends Controller
{
    // GET /api/v1/ads?country=FR
    // Returns active ads for the given country (or all if no country)
    public function index(Request $request): JsonResponse
    {
        $country = $request->query('country');

        $ads = Ad::active()
            ->when($country, fn($q) => $q->where(fn($q2) =>
                $q2->whereNull('target_country')->orWhere('target_country', $country)
            ))
            ->orderByDesc('created_at')
            ->get();

        return response()->json(['data' => $ads]);
    }

    // GET /api/v1/ads/{id}
    public function show(int $id): JsonResponse
    {
        $ad = Ad::find($id);

        if (!$ad) {
            return response()->json(['message' => 'Ad not found'], 404);
        }

        return response()->json(['data' => $ad]);
    }

    // POST /api/v1/ads
    // Protected: requires sanctum token
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title'          => 'required|string|max:500',
            'body'           => 'nullable|string',
            'image_url'      => 'nullable|url|max:2000',
            'target_country' => 'nullable|string|in:FR,TN,EG,CA',
            'status'         => 'nullable|string|in:active,inactive,draft',
            'starts_at'      => 'nullable|date',
            'ends_at'        => 'nullable|date|after_or_equal:starts_at',
        ]);

        $ad = Ad::create($data);

        return response()->json(['data' => $ad], 201);
    }

    // PUT /api/v1/ads/{id}
    public function update(Request $request, int $id): JsonResponse
    {
        $ad = Ad::find($id);

        if (!$ad) {
            return response()->json(['message' => 'Ad not found'], 404);
        }

        $data = $request->validate([
            'title'          => 'sometimes|string|max:500',
            'body'           => 'nullable|string',
            'image_url'      => 'nullable|url|max:2000',
            'target_country' => 'nullable|string|in:FR,TN,EG,CA',
            'status'         => 'nullable|string|in:active,inactive,draft',
            'starts_at'      => 'nullable|date',
            'ends_at'        => 'nullable|date|after_or_equal:starts_at',
        ]);

        $ad->update($data);

        return response()->json(['data' => $ad]);
    }

    // DELETE /api/v1/ads/{id}
    public function destroy(int $id): JsonResponse
    {
        $ad = Ad::find($id);

        if (!$ad) {
            return response()->json(['message' => 'Ad not found'], 404);
        }

        $ad->delete();

        return response()->json(['message' => 'Ad deleted'], 200);
    }
}
