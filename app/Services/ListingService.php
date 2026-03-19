<?php

namespace App\Services;

use App\Models\Announcement;
use App\Models\AnnouncementTranslation;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListingService
{
    public function getListings(array $filters): LengthAwarePaginator
    {
        $query = Announcement::query();

        if (!empty($filters['country'])) {
            $query->where('country', $filters['country']);
        }

        if (!empty($filters['property_type'])) {
            // Try normalized code first (maps to DB variants)
            $variants = \App\Models\PropertyType::where('code', $filters['property_type'])
                ->value('variants');
            if ($variants) {
                $query->whereIn('property_type', $variants);
            } else {
                // Fallback: case-insensitive direct match
                $query->whereRaw('LOWER(property_type) LIKE LOWER(?)', ['%' . $filters['property_type'] . '%']);
            }
        }

        if (!empty($filters['listing_type'])) {
            $query->where('property_typology', $filters['listing_type']);
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
            $query->whereRaw("(interior_features::jsonb->>'surface_m2')::numeric >= ?", [(float) $filters['min_surface']]);
        }
        if (!empty($filters['max_surface'])) {
            $query->whereRaw("(interior_features::jsonb->>'surface_m2')::numeric <= ?", [(float) $filters['max_surface']]);
        }

        if (isset($filters['furnished']) && $filters['furnished'] !== '') {
            $val = filter_var($filters['furnished'], FILTER_VALIDATE_BOOLEAN) ? 'true' : 'false';
            $query->whereRaw("(other_features::jsonb->>'is_furnished') = ?", [$val]);
        }

        if (!empty($filters['city'])) {
            $query->where('location', 'ILIKE', '%' . $filters['city'] . '%');
        }

        $sort = $filters['sort'] ?? '';
        match ($sort) {
            'price_asc'  => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            default      => $query->orderBy('created_at', 'desc'),
        };

        $perPage = min((int) ($filters['per_page'] ?? 20), 100);
        $locale  = $filters['lang'] ?? null;

        $paginator = $query->paginate($perPage);

        // Eager-load translations for the requested locale to avoid N+1
        if ($locale) {
            $paginator->load(['translations' => fn($q) => $q->where('locale', $locale)]);
        }

        return $paginator;
    }

    // Merge translation fields (title, description, features) onto a serialized item array.
    public function applyTranslation(array $item, ?string $locale, Announcement $model): array
    {
        if (!$locale || !$model->relationLoaded('translations')) {
            return $item;
        }

        /** @var AnnouncementTranslation|null $t */
        $t = $model->translations->first();
        if (!$t) {
            return $item;
        }

        if (!empty($t->title)) {
            $item['title'] = $t->title;
        }

        if (!empty($t->description)) {
            $item['description'] = $t->description;
        }

        // Overlay translated features inside other_features
        // Note: always apply even if other_features is null (e.g. Mubawab listings)
        if (!empty($t->features_translated)) {
            $other = isset($item['other_features']) && is_array($item['other_features'])
                ? $item['other_features']
                : [];
            $other['features'] = $t->features_translated;
            $item['other_features'] = $other;
        }

        return $item;
    }

    public function createListing(array $data): Announcement
    {
        return Announcement::create($data);
    }

    public function getListingDetail(int $id, ?string $locale = null): ?Announcement
    {
        $announcement = Announcement::find($id);

        if ($announcement && $locale) {
            $announcement->load(['translations' => fn($q) => $q->where('locale', $locale)]);
        }

        return $announcement;
    }

    // Format a single Announcement as an array with translation applied.
    public function formatDetail(Announcement $announcement, ?string $locale): array
    {
        $data = $announcement->toArray();
        return $this->applyTranslation($data, $locale, $announcement);
    }

    public function getStats(?string $country): array
    {
        $query = Announcement::query();

        if ($country) {
            $query->where('country', $country);
        }

        $stats = $query->selectRaw("
            COUNT(*) as total,
            ROUND(AVG(price)::numeric, 0) as avg_price,
            COUNT(DISTINCT location) as cities_count
        ")->first();

        $byType = Announcement::query()
            ->when($country, fn($q) => $q->where('country', $country))
            ->selectRaw("property_type, COUNT(*) as count")
            ->groupBy('property_type')
            ->pluck('count', 'property_type')
            ->toArray();

        return [
            'total'        => (int) ($stats->total ?? 0),
            'avg_price'    => (float) ($stats->avg_price ?? 0),
            'cities_count' => (int) ($stats->cities_count ?? 0),
            'by_type'      => $byType,
        ];
    }

    public function getRegions(): array
    {
        $counts = Announcement::query()
            ->selectRaw("country, COUNT(*) as count")
            ->groupBy('country')
            ->pluck('count', 'country')
            ->toArray();

        $regions = [
            ['code' => 'FR', 'name' => 'France',  'currency' => 'EUR'],
            ['code' => 'TN', 'name' => 'Tunisia', 'currency' => 'TND'],
            ['code' => 'EG', 'name' => 'Egypt',   'currency' => 'EGP'],
            ['code' => 'CA', 'name' => 'Canada',  'currency' => 'CAD'],
        ];

        foreach ($regions as &$region) {
            $region['count'] = $counts[$region['code']] ?? 0;
        }

        return $regions;
    }

    public function getCities(?string $country): array
    {
        return Announcement::query()
            ->when($country, fn($q) => $q->where('country', $country))
            ->selectRaw("location, COUNT(*) as count")
            ->whereNotNull('location')
            ->groupBy('location')
            ->orderByDesc('count')
            ->limit(200)
            ->get()
            ->map(fn($row) => [
                'city'  => $row->location,
                'count' => (int) $row->count,
            ])
            ->toArray();
    }
}
