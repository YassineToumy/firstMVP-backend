<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\AnnouncementTranslation;
use Illuminate\Http\Request;

class TranslationController extends Controller
{
    // POST /api/v1/admin/translations/push
    // Receive one or many translations from your AI server
    public function push(Request $request)
    {
        $data = $request->validate([
            'translations'                    => 'required|array|min:1',
            'translations.*.announcement_id'  => 'required|integer|exists:announcements,id',
            'translations.*.locale'           => 'required|string|in:fr,en,ar,es',
            'translations.*.title'            => 'nullable|string',
            'translations.*.description'      => 'nullable|string',
            'translations.*.features'         => 'nullable|array',
            'translations.*.features.*'       => 'string',
        ]);

        $saved = 0;
        foreach ($data['translations'] as $t) {
            AnnouncementTranslation::updateOrCreate(
                [
                    'announcement_id' => $t['announcement_id'],
                    'locale'          => $t['locale'],
                ],
                [
                    'title'               => $t['title'] ?? null,
                    'description'         => $t['description'] ?? null,
                    'features_translated' => $t['features'] ?? null,
                    'translated_at'       => now(),
                ]
            );
            $saved++;
        }

        return response()->json(['saved' => $saved]);
    }

    // GET /api/v1/admin/translations/pending?locale=ar&limit=50
    // Returns announcements that don't yet have a translation for the given locale.
    // Each item includes a `features_to_translate` array — the extracted feature strings
    // the AI must translate and return as the `features` field when calling /push.
    public function pending(Request $request)
    {
        $locale = $request->query('locale', 'ar');
        $limit  = min((int) $request->query('limit', 50), 200);

        $ids = AnnouncementTranslation::where('locale', $locale)->pluck('announcement_id');

        $announcements = Announcement::whereNotIn('id', $ids)
            ->select('id', 'title', 'description', 'other_features')
            ->limit($limit)
            ->get();

        $data = $announcements->map(function (Announcement $a) {
            // Extract clean feature strings from other_features (any array-valued key)
            $features = [];
            $other = $a->other_features; // already decoded via accessor
            if (is_array($other)) {
                $values = array_is_list($other) ? $other : [$other];
                foreach ($values as $obj) {
                    if (!is_array($obj)) continue;
                    foreach ($obj as $v) {
                        if (is_array($v)) {
                            foreach ($v as $s) {
                                if (is_string($s) && trim($s) !== '') $features[] = trim($s);
                            }
                        }
                    }
                }
            }

            return [
                'id'                   => $a->id,
                'title'                => $a->title,
                'description'          => $a->description,
                'features_to_translate' => array_values(array_unique($features)),
            ];
        });

        return response()->json(['data' => $data]);
    }

    // GET /api/v1/admin/translations/bad?locale=ar&limit=50
    // Returns announcement_ids whose translation has no valid features after sanitization
    // (useful for re-queuing bad AI output for re-translation)
    public function bad(Request $request)
    {
        $locale = $request->query('locale', 'ar');
        $limit  = min((int) $request->query('limit', 200), 500);

        $translations = AnnouncementTranslation::where('locale', $locale)
            ->whereNotNull('features_translated')
            ->select('announcement_id', 'features_translated')
            ->limit($limit)
            ->get();

        $badIds = $translations->filter(function ($t) {
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
