<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\AnnouncementTranslation;
use Illuminate\Http\Request;

class TranslationController extends Controller
{
    // POST /api/v1/admin/translations/push
    // Accepts: title, description, features (legacy flat array),
    //          interior_features, exterior_features, other_features (new structured objects)
    public function push(Request $request)
    {
        $data = $request->validate([
            'translations'                             => 'required|array|min:1',
            'translations.*.announcement_id'           => 'required|integer|exists:announcements,id',
            'translations.*.locale'                    => 'required|string|in:fr,en,ar,es',
            'translations.*.title'                     => 'nullable|string',
            'translations.*.description'               => 'nullable|string',
            // legacy flat features list
            'translations.*.features'                  => 'nullable|array',
            'translations.*.features.*'                => 'string',
            // new structured fields mirroring announcements table
            'translations.*.interior_features'         => 'nullable|array',
            'translations.*.exterior_features'         => 'nullable|array',
            'translations.*.other_features'            => 'nullable|array',
        ]);

        $saved = 0;
        foreach ($data['translations'] as $t) {
            AnnouncementTranslation::updateOrCreate(
                [
                    'announcement_id' => $t['announcement_id'],
                    'locale'          => $t['locale'],
                ],
                [
                    'title'               => $t['title']               ?? null,
                    'description'         => $t['description']         ?? null,
                    'features_translated' => $t['features']            ?? null,
                    'interior_features'   => $t['interior_features']   ?? null,
                    'exterior_features'   => $t['exterior_features']   ?? null,
                    'other_features'      => $t['other_features']      ?? null,
                    'translated_at'       => now(),
                ]
            );
            $saved++;
        }

        return response()->json(['saved' => $saved]);
    }

    // GET /api/v1/admin/translations/pending?locale=ar&limit=50
    // Returns announcements without a translation for the given locale.
    // Includes all original feature objects so the AI knows what to translate.
    public function pending(Request $request)
    {
        $locale = $request->query('locale', 'ar');
        $limit  = min((int) $request->query('limit', 50), 200);

        // Only exclude announcements that already have title AND description translated.
        // Announcements with existing translations but missing title/description still need work.
        $ids = AnnouncementTranslation::where('locale', $locale)
            ->whereNotNull('title')
            ->whereNotNull('description')
            ->pluck('announcement_id');

        $announcements = Announcement::whereNotIn('id', $ids)
            ->select('id', 'title', 'description', 'interior_features', 'exterior_features', 'other_features')
            ->limit($limit)
            ->get();

        $data = $announcements->map(function (Announcement $a) {
            // Extract flat feature strings from other_features for legacy use
            $flatFeatures = [];
            $other = $a->other_features;
            if (is_array($other)) {
                $items = array_is_list($other) ? $other : [$other];
                foreach ($items as $obj) {
                    if (!is_array($obj)) continue;
                    foreach ($obj as $v) {
                        if (is_array($v)) {
                            foreach ($v as $s) {
                                if (is_string($s) && trim($s) !== '') $flatFeatures[] = trim($s);
                            }
                        }
                    }
                }
            }

            return [
                'id'                  => $a->id,
                'title'               => $a->title,
                'description'         => $a->description,
                // Original structured objects — translate these and push back with same keys
                'interior_features'   => $a->interior_features ?: null,
                'exterior_features'   => $a->exterior_features ?: null,
                'other_features'      => $a->other_features    ?: null,
                // Convenience: flat list of feature strings extracted from other_features
                'features_to_translate' => array_values(array_unique($flatFeatures)),
            ];
        });

        return response()->json(['data' => $data]);
    }

    // GET /api/v1/admin/translations/bad?locale=ar&limit=200
    // Returns announcement_ids whose translation has no valid features after sanitization.
    // Use this to identify bad AI output that needs re-queuing.
    public function bad(Request $request)
    {
        $locale = $request->query('locale', 'ar');
        $limit  = min((int) $request->query('limit', 200), 500);

        $translations = AnnouncementTranslation::where('locale', $locale)
            ->where(function ($q) {
                $q->whereNull('title')
                  ->orWhereNull('description')
                  ->orWhere(function ($q2) {
                      $q2->whereNull('other_features')
                         ->whereNotNull('features_translated');
                  });
            })
            ->select('announcement_id', 'title', 'description', 'features_translated')
            ->limit($limit)
            ->get();

        $badIds = $translations->filter(function ($t) {
            if (empty($t->title) || empty($t->description)) return true;
            $raw = is_array($t->features_translated) ? $t->features_translated : [];
            $clean = array_filter($raw, function ($f) {
                if (!is_string($f) || strlen(trim($f)) < 2) return false;
                $f = trim($f);
                if (preg_match('/^"[^"]+":\s/', $f)) return false;
                if (str_ends_with($f, '}') || str_ends_with($f, ']}')) return false;
                return true;
            });
            return empty($clean);
        })->pluck('announcement_id')->values();

        return response()->json(['bad_translation_ids' => $badIds, 'count' => $badIds->count()]);
    }
}
