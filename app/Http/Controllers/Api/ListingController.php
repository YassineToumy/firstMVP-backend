<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ListingFilterRequest;
use App\Services\ListingService;
use Illuminate\Http\JsonResponse;

class ListingController extends Controller
{
    public function __construct(
        private ListingService $service
    ) {}

    /**
     * GET /api/v1/listings
     * Paginated listings with filters.
     */
    public function index(ListingFilterRequest $request): JsonResponse
    {
        $filters = $request->validated();
        $paginated = $this->service->getListings($filters);

        return response()->json([
            'data' => $paginated->items(),
            'meta' => [
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
            ],
        ]);
    }

    /**
     * GET /api/v1/listings/{source}/{id}
     * Full detail from source-specific table.
     */
    public function show(string $source, string $id): JsonResponse
    {
        $listing = $this->service->getListingDetail($source, $id);

        if (!$listing) {
            return response()->json(['message' => 'Listing not found'], 404);
        }

        return response()->json(['data' => $listing]);
    }

    /**
     * GET /api/v1/listings/stats
     * Aggregate stats for a country.
     */
    public function stats(ListingFilterRequest $request): JsonResponse
    {
        $country = $request->validated()['country'] ?? null;
        $stats = $this->service->getStats($country);

        return response()->json(['data' => $stats]);
    }
}