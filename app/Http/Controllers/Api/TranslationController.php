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
    // Returns announcements that don't yet have a translation for the given locale
    public function pending(Request $request)
    {
        $locale = $request->query('locale', 'ar');
        $limit  = min((int) $request->query('limit', 50), 200);

        $ids = AnnouncementTranslation::where('locale', $locale)->pluck('announcement_id');

        $announcements = Announcement::whereNotIn('id', $ids)
            ->select('id', 'title', 'description', 'other_features')
            ->limit($limit)
            ->get();

        return response()->json(['data' => $announcements]);
    }
}
