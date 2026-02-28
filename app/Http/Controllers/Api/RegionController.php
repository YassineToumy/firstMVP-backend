<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ListingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RegionController extends Controller
{
    public function __construct(
        private ListingService $service
    ) {}

    /**
     * GET /api/v1/regions
     * List all regions with counts.
     */
    public function index(): JsonResponse
    {
        $regions = $this->service->getRegions();

        return response()->json(['data' => $regions]);
    }

    /**
     * GET /api/v1/cities
     * List cities for a country with counts.
     */
    public function cities(Request $request): JsonResponse
    {
        $country = $request->validate([
            'country' => 'nullable|string|in:FR,TN,EG,CA',
        ])['country'] ?? null;

        $cities = $this->service->getCities($country);

        return response()->json(['data' => $cities]);
    }
}