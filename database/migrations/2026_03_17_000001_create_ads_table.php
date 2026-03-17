<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('
            CREATE TABLE ads (
                id              BIGSERIAL PRIMARY KEY,
                title           TEXT NOT NULL,
                body            TEXT,
                image_url       TEXT,
                target_country  VARCHAR(10),
                status          VARCHAR(20) NOT NULL DEFAULT \'active\',
                starts_at       TIMESTAMP,
                ends_at         TIMESTAMP,
                created_at      TIMESTAMP DEFAULT NOW(),
                updated_at      TIMESTAMP DEFAULT NOW()
            )
        ');

        DB::statement('CREATE INDEX idx_ads_status  ON ads (status)');
        DB::statement('CREATE INDEX idx_ads_country ON ads (target_country)');
    }

    public function down(): void
    {
        Schema::dropIfExists('ads');
    }
};