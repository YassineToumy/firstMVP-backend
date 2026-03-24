<?php

namespace App\Services;

use App\Models\Announcement;
use App\Models\AnnouncementTranslation;
use App\Models\PropertyType;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListingService
{
    // ── Cached lookup tables (per request) ──
    private ?array $variantToNameCache = null;
    private ?string $cachedLocale = null;

    // Typology translations
    private const TYPOLOGY_MAP = [
        'fr' => ['rent' => 'Location',  'sale' => 'Vente',  'buy' => 'Achat'],
        'en' => ['rent' => 'Rental',    'sale' => 'Sale',   'buy' => 'Purchase'],
        'ar' => ['rent' => 'إيجار',     'sale' => 'بيع',    'buy' => 'شراء'],
        'es' => ['rent' => 'Alquiler',  'sale' => 'Venta',  'buy' => 'Compra'],
    ];

    /**
     * Build variant → translated property type name lookup.
     * Loaded once per request, keyed by lowercase variant.
     */
    private function getVariantToName(string $locale): array
    {
        if ($this->variantToNameCache !== null && $this->cachedLocale === $locale) {
            return $this->variantToNameCache;
        }

        $map = [];
        foreach (PropertyType::all() as $pt) {
            $translated = $pt->getTranslation('name', $locale, false)
                ?: $pt->getTranslation('name', 'fr', false)
                ?: $pt->code;

            $variants = is_array($pt->variants)
                ? $pt->variants
                : (json_decode($pt->variants, true) ?? []);

            foreach ($variants as $v) {
                $map[mb_strtolower(trim($v))] = $translated;
            }
        }

        $this->variantToNameCache = $map;
        $this->cachedLocale = $locale;
        return $map;
    }

    /**
     * Translate a raw property_type value to its Spatie-translated name.
     */
    private function translatePropertyType(?string $raw, string $locale): ?string
    {
        if (!$raw) return $raw;
        $map = $this->getVariantToName($locale);
        return $map[mb_strtolower(trim($raw))] ?? $raw;
    }

    /**
     * Translate a raw property_typology value (rent/sale/buy etc.) to a localised label.
     */
    private function translateTypology(?string $raw, string $locale): ?string
    {
        if (!$raw) return $raw;
        $key = mb_strtolower(trim($raw));
        // Normalise common French/Arabic scraped values to canonical keys
        $aliases = [
            'location' => 'rent', 'loyer' => 'rent', 'louer' => 'rent',
            'à louer'  => 'rent', 'a louer' => 'rent', 'for_rent' => 'rent',
            'for rent' => 'rent', 'rent' => 'rent', 'إيجار' => 'rent',
            'vente'    => 'sale', 'for_sale' => 'sale', 'for sale' => 'sale',
            'sale'     => 'sale', 'بيع' => 'sale',
            'achat'    => 'buy',  'buy' => 'buy', 'شراء' => 'buy',
        ];
        $canonical = $aliases[$key] ?? null;
        if (!$canonical) return $raw;
        return self::TYPOLOGY_MAP[$locale][$canonical]
            ?? self::TYPOLOGY_MAP['fr'][$canonical]
            ?? $raw;
    }

    /**
     * Translate field names on a serialised listing array.
     */
    public function translateFieldNames(array $item, string $locale): array
    {
        if (!empty($item['property_type'])) {
            $item['property_type'] = $this->translatePropertyType($item['property_type'], $locale);
        }
        if (!empty($item['property_typology'])) {
            $item['property_typology'] = $this->translateTypology($item['property_typology'], $locale);
        }
        return $item;
    }

    public function getListings(array $filters): LengthAwarePaginator
    {
        $query = Announcement::query();

        if (!empty($filters['country'])) {
            $query->where('country', $filters['country']);
        }

        if (!empty($filters['property_type'])) {
            // value() returns the raw JSON string — decode it manually
            $raw = \App\Models\PropertyType::where('code', $filters['property_type'])
                ->value('variants');
            $variants = is_array($raw) ? $raw : json_decode($raw, true);

            if (!empty($variants)) {
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

    // Merge translation fields onto a serialized item array.
    // Supports both the legacy `features_translated` (flat string array) and
    // the new mirrored `interior_features`, `exterior_features`, `other_features` objects.
    public function applyTranslation(array $item, ?string $locale, Announcement $model): array
    {
        // Always translate field names when locale is provided
        if ($locale) {
            $item = $this->translateFieldNames($item, $locale);
        }

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

        // ── New: full structured fields mirroring announcements table ──
        if (!empty($t->interior_features) && is_array($t->interior_features)) {
            $item['interior_features'] = $t->interior_features;
        }

        if (!empty($t->exterior_features) && is_array($t->exterior_features)) {
            $item['exterior_features'] = $t->exterior_features;
        }

        if (!empty($t->other_features) && is_array($t->other_features)) {
            // Deep-merge: keep original scalar flags (is_furnished, energy_class, etc.)
            // but replace any translated keys (features array, etc.)
            $original = isset($item['other_features']) && is_array($item['other_features'])
                ? $item['other_features']
                : [];
            $item['other_features'] = array_merge($original, $t->other_features);
        }

        // ── Legacy: flat features_translated string array ──
        // Only applied when new other_features translation is absent
        if (empty($t->other_features) && !empty($t->features_translated)) {
            $raw = is_array($t->features_translated) ? $t->features_translated : [];
            $clean = array_values(array_filter(array_map(function (mixed $f) {
                if (!is_string($f)) return null;
                $f = trim($f);
                if ($f === '' || strlen($f) < 2) return null;
                if (str_starts_with($f, '{') || str_starts_with($f, '[')) return null;
                if (preg_match('/^"[^"]+":\s/', $f)) return null;
                if (str_ends_with($f, '}') || str_ends_with($f, ']}') || str_ends_with($f, ']')) return null;
                return trim($f, '"\'');
            }, $raw)));

            if (!empty($clean)) {
                $other = isset($item['other_features']) && is_array($item['other_features'])
                    ? $item['other_features']
                    : [];
                $other['features'] = $clean;
                $item['other_features'] = $other;
            }
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
