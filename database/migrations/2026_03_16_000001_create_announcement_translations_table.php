<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('
            CREATE TABLE announcement_translations (
                id                  BIGSERIAL PRIMARY KEY,
                announcement_id     BIGINT NOT NULL REFERENCES announcements(id) ON DELETE CASCADE,
                locale              VARCHAR(10) NOT NULL,
                title               TEXT,
                description         TEXT,
                features_translated JSONB,
                translated_at       TIMESTAMP DEFAULT NOW(),
                UNIQUE (announcement_id, locale)
            )
        ');

        DB::statement('CREATE INDEX idx_ann_trans_announcement ON announcement_translations (announcement_id)');
        DB::statement('CREATE INDEX idx_ann_trans_locale       ON announcement_translations (locale)');
    }

    public function down(): void
    {
        Schema::dropIfExists('announcement_translations');
    }
};
