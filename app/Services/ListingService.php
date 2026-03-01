<?php

namespace App\Services;

use App\Models\AllListing;
use App\Models\BieniciListing;
use App\Models\MubawabListing;
use App\Models\PropertyfinderListing;
use App\Models\MktlistListing;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ListingService
{
    private const COUNTRY_SOURCE = [
        'FR' => 'bienici',
        'TN' => 'mubawab',
        'EG' => 'propertyfinder',
        'CA' => 'mktlist',
    ];

    private const COUNTRY_CURRENCY = [
        'FR' => 'EUR',
        'TN' => 'TND',
        'EG' => 'EGP',
        'CA' => 'CAD',
    ];

    public function getListings(array $filters): LengthAwarePaginator
    {
        $query = AllListing::query();

        if (!empty($filters['country'])) {
            $query->where('country', $filters['country']);
        }

        if (!empty($filters['property_type'])) {
            $query->where('property_type', $filters['property_type']);
        }

        if (!empty($filters['min_price'])) {
            $query->where('price', '>=', (float) $filters['min_price']);
        }
        if (!empty($filters['max_price'])) {
            $query->where('price', '<=', (float) $filters['max_price']);
        }

        if (!empty($filters['bedrooms'])) {
            $bedrooms = (int) $filters['bedrooms'];
            if ($bedrooms >= 4) {
                $query->where('bedrooms', '>=', 4);
            } else {
                $query->where('bedrooms', $bedrooms);
            }
        }

        if (!empty($filters['min_surface'])) {
            $query->where('surface_m2', '>=', (float) $filters['min_surface']);
        }
        if (!empty($filters['max_surface'])) {
            $query->where('surface_m2', '<=', (float) $filters['max_surface']);
        }

        if (isset($filters['furnished']) && $filters['furnished'] !== '') {
            $query->where('is_furnished', filter_var($filters['furnished'], FILTER_VALIDATE_BOOLEAN));
        }

        if (!empty($filters['city'])) {
            $query->where('city', 'ILIKE', '%' . $filters['city'] . '%');
        }

        $sort = $filters['sort'] ?? '';
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $perPage = min((int) ($filters['per_page'] ?? 20), 100);

        return $query->paginate($perPage);
    }

    public function getListingDetail(string $source, string $id): ?Model
    {
        $modelClass = $this->resolveModel($source);

        if (!$modelClass) {
            return null;
        }

        $uniqueCol = $this->resolveUniqueColumn($source);

        return $modelClass::where($uniqueCol, $id)->first();
    }

    public function getStats(?string $country): array
    {
        $query = AllListing::query();

        if ($country) {
            $query->where('country', $country);
        }

        $stats = $query->selectRaw("
            COUNT(*) as total,
            ROUND(AVG(price)::numeric, 0) as avg_price,
            COUNT(DISTINCT city) as cities_count
        ")->first();

        $byType = AllListing::query()
            ->when($country, fn($q) => $q->where('country', $country))
            ->selectRaw("property_type, COUNT(*) as count")
            ->groupBy('property_type')
            ->pluck('count', 'property_type')
            ->toArray();

        return [
            'total' => (int) ($stats->total ?? 0),
            'avg_price' => (float) ($stats->avg_price ?? 0),
            'cities_count' => (int) ($stats->cities_count ?? 0),
            'by_type' => $byType,
        ];
    }

    public function getRegions(): array
    {
        $counts = AllListing::query()
            ->selectRaw("country, COUNT(*) as count")
            ->groupBy('country')
            ->pluck('count', 'country')
            ->toArray();

        $regions = [
            ['code' => 'FR', 'name' => 'France', 'currency' => 'EUR'],
            ['code' => 'TN', 'name' => 'Tunisia', 'currency' => 'TND'],
            ['code' => 'EG', 'name' => 'Egypt', 'currency' => 'EGP'],
            ['code' => 'CA', 'name' => 'Canada', 'currency' => 'CAD'],
        ];

        foreach ($regions as &$region) {
            $region['count'] = $counts[$region['code']] ?? 0;
        }

        return $regions;
    }

    public function getCities(?string $country): array
    {
        return AllListing::query()
            ->when($country, fn($q) => $q->where('country', $country))
            ->selectRaw("city, COUNT(*) as count")
            ->whereNotNull('city')
            ->groupBy('city')
            ->orderByDesc('count')
            ->limit(200)
            ->get()
            ->map(fn($row) => [
                'city' => $row->city,
                'count' => (int) $row->count,
            ])
            ->toArray();
    }

    private function resolveModel(string $source): ?string
    {
        return match ($source) {
            'bienici' => BieniciListing::class,
            'mubawab' => MubawabListing::class,
            'propertyfinder' => PropertyfinderListing::class,
            'mktlist' => MktlistListing::class,
            default => null,
        };
    }

    private function resolveUniqueColumn(string $source): string
    {
        return match ($source) {
            'bienici' => 'source_id',
            'mubawab' => 'ad_id',
            'propertyfinder' => 'property_id',
            'mktlist' => 'url',
            default => 'id',
        };
    }
}