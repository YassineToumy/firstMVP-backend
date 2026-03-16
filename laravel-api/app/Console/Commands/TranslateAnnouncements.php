<?php

namespace App\Console\Commands;

use App\Models\Announcement;
use App\Services\TranslationService;
use Illuminate\Console\Command;

class TranslateAnnouncements extends Command
{
    protected $signature = 'translations:run
                            {--locale=all : Locale to translate (fr|en|ar|es|all)}
                            {--batch=20   : Number of announcements to process per chunk}
                            {--limit=500  : Maximum announcements to process in this run}
                            {--id=        : Translate a single announcement by ID}';

    protected $description = 'Translate announcement titles, descriptions and features using Claude AI';

    private const LOCALES = ['fr', 'en', 'ar', 'es'];

    public function handle(TranslationService $service): int
    {
        $localeOpt = $this->option('locale');
        $batch     = (int) $this->option('batch');
        $limit     = (int) $this->option('limit');
        $singleId  = $this->option('id');

        $locales = $localeOpt === 'all' ? self::LOCALES : [$localeOpt];

        // Single announcement mode
        if ($singleId) {
            $announcement = Announcement::find((int) $singleId);
            if (!$announcement) {
                $this->error("Announcement #{$singleId} not found.");
                return 1;
            }
            foreach ($locales as $locale) {
                $ok = $service->translate($announcement, $locale);
                $this->line($ok
                    ? "  ✓ #{$announcement->id} → {$locale}"
                    : "  ✗ #{$announcement->id} → {$locale} (failed)"
                );
            }
            return 0;
        }

        foreach ($locales as $locale) {
            $this->info("▶ Processing locale: {$locale}");
            $processed = 0;

            // Query announcements that do NOT yet have a translation for this locale
            $query = Announcement::query()
                ->whereNotExists(function ($sub) use ($locale) {
                    $sub->from('announcement_translations')
                        ->whereColumn('announcement_id', 'announcements.id')
                        ->where('locale', $locale);
                })
                ->orderBy('id')
                ->limit($limit);

            $query->chunk($batch, function ($chunk) use ($service, $locale, &$processed) {
                foreach ($chunk as $announcement) {
                    $ok = $service->translate($announcement, $locale);
                    $processed++;

                    if ($ok) {
                        $this->line("  ✓ #{$announcement->id}");
                    } else {
                        $this->warn("  ✗ #{$announcement->id} (failed — will retry next run)");
                    }

                    // Small pause to respect API rate limits
                    usleep(200_000); // 200 ms
                }
            });

            $this->info("  → {$processed} announcements processed for [{$locale}]");
        }

        $this->info('Done.');
        return 0;
    }
}
