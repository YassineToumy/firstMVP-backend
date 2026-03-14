<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('archived_announcements');
        Schema::dropIfExists('announcements');

        DB::statement('
            CREATE TABLE announcements (
                id                 BIGSERIAL PRIMARY KEY,
                title              TEXT,
                price              NUMERIC(15,2),
                description        TEXT,
                property_typology  VARCHAR(255),
                property_type      VARCHAR(255),
                photos             TEXT[],
                interior_features  JSONB,
                exterior_features  JSONB,
                other_features     JSONB,
                extra_data         JSONB,
                country            VARCHAR(10),
                location           VARCHAR(255),
                city               VARCHAR(255),
                bedrooms           INTEGER,
                bathrooms          INTEGER,
                surface_m2         NUMERIC(10,2),
                price_per_m2       NUMERIC(10,2),
                latitude           NUMERIC(10,7),
                longitude          NUMERIC(10,7),
                source             VARCHAR(255),
                source_id          VARCHAR(255),
                url                TEXT,
                created_at         TIMESTAMP,
                updated_at         TIMESTAMP
            )
        ');

        DB::statement('CREATE INDEX idx_announcements_country ON announcements (country)');
        DB::statement('CREATE INDEX idx_announcements_property_type ON announcements (property_type)');
        DB::statement('CREATE INDEX idx_announcements_property_typology ON announcements (property_typology)');
        DB::statement('CREATE INDEX idx_announcements_city ON announcements (city)');
        DB::statement('CREATE INDEX idx_announcements_bedrooms ON announcements (bedrooms)');
        DB::statement('CREATE INDEX idx_announcements_price ON announcements (price)');
        DB::statement('CREATE INDEX idx_announcements_source ON announcements (source, source_id)');

        DB::statement('
            CREATE TABLE archived_announcements (
                id                 BIGSERIAL PRIMARY KEY,
                title              TEXT,
                price              NUMERIC(15,2),
                description        TEXT,
                property_typology  VARCHAR(255),
                property_type      VARCHAR(255),
                photos             TEXT[],
                interior_features  JSONB,
                exterior_features  JSONB,
                other_features     JSONB,
                extra_data         JSONB,
                country            VARCHAR(10),
                location           VARCHAR(255),
                city               VARCHAR(255),
                bedrooms           INTEGER,
                bathrooms          INTEGER,
                surface_m2         NUMERIC(10,2),
                price_per_m2       NUMERIC(10,2),
                latitude           NUMERIC(10,7),
                longitude          NUMERIC(10,7),
                source             VARCHAR(255),
                source_id          VARCHAR(255),
                url                TEXT,
                created_at         TIMESTAMP,
                updated_at         TIMESTAMP,
                archived_at        TIMESTAMP
            )
        ');

        DB::statement('CREATE INDEX idx_archived_country ON archived_announcements (country)');
        DB::statement('CREATE INDEX idx_archived_at ON archived_announcements (archived_at)');
    }

    public function down(): void
    {
        Schema::dropIfExists('archived_announcements');
        Schema::dropIfExists('announcements');
    }
};
