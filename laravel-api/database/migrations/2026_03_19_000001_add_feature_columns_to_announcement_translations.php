<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('
            ALTER TABLE announcement_translations
                ADD COLUMN IF NOT EXISTS interior_features JSONB,
                ADD COLUMN IF NOT EXISTS exterior_features JSONB,
                ADD COLUMN IF NOT EXISTS other_features    JSONB
        ');
    }

    public function down(): void
    {
        DB::statement('
            ALTER TABLE announcement_translations
                DROP COLUMN IF EXISTS interior_features,
                DROP COLUMN IF EXISTS exterior_features,
                DROP COLUMN IF EXISTS other_features
        ');
    }
};
