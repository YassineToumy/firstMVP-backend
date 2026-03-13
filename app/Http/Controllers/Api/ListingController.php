<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateListingRequest;
use App\Http\Requests\ListingFilterRequest;
use App\Services\ListingService;
use Illuminate\Http\JsonResponse;

class ListingController extends Controller
{
    public function __construct(
        private ListingService $service
    ) {}

    public function index(ListingFilterRequest $request): JsonResponse
    {
        try {
            $filters = $request->validated();
            $paginated = $this->service->getListings($filters);

            return response()->json([
                'data' => $paginated->items(),
                'meta' => [
                    'current_page' => $paginated->currentPage(),
                    'last_page'    => $paginated->lastPage(),
                    'per_page'     => $paginated->perPage(),
                    'total'        => $paginated->total(),
                ],
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'error'   => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ], 500);
        }
    }

    public function store(CreateListingRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            // Convert photos array to PostgreSQL TEXT[] format
            if (!empty($data['photos'])) {
                $escaped = array_map(fn($url) => '"' . addslashes($url) . '"', $data['photos']);
                $data['photos'] = '{' . implode(',', $escaped) . '}';
            }

            // JSON-encode array fields for TEXT columns
            foreach (['interior_features', 'exterior_features', 'other_features'] as $field) {
                if (isset($data[$field]) && is_array($data[$field])) {
                    $data[$field] = json_encode($data[$field]);
                }
            }

            $data['created_at'] = now();

            $listing = $this->service->createListing($data);

            return response()->json(['data' => $listing], 201);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'file'  => $e->getFile(),
                'line'  => $e->getLine(),
            ], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $listing = $this->service->getListingDetail($id);

            if (!$listing) {
                return response()->json(['message' => 'Listing not found'], 404);
            }

            return response()->json(['data' => $listing]);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'file'  => $e->getFile(),
                'line'  => $e->getLine(),
            ], 500);
        }
    }

    public function stats(ListingFilterRequest $request): JsonResponse
    {
        try {
            $country = $request->validated()['country'] ?? null;
            $stats   = $this->service->getStats($country);

            return response()->json(['data' => $stats]);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'file'  => $e->getFile(),
                'line'  => $e->getLine(),
            ], 500);
        }
    }
}