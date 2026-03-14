<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('archived_announcements');
        Schema::dropIfExists('announcements');

        // Columns match the Python scraper INSERT exactly:
        // source, source_id, title, price, description,
        // property_typology, property_type, price_per_m2, url, photos,
        // interior_features, exterior_features, other_features,
        // location, longitude, latitude, bedrooms, bathrooms,
        // seller_name, seller_phone, currency, country, extra_data, created_at
        DB::statement('
            CREATE TABLE announcements (
                id                  BIGSERIAL PRIMARY KEY,
                source              VARCHAR(50),
                source_id           TEXT,
                title               TEXT,
                price               NUMERIC(15, 2),
                description         TEXT,
                property_typology   VARCHAR(255),
                property_type       VARCHAR(255),
                price_per_m2        NUMERIC(10, 2),
                url                 TEXT,
                photos              TEXT[],
                interior_features   TEXT,
                exterior_features   TEXT,
                other_features      TEXT,
                location            TEXT,
                longitude           NUMERIC(10, 7),
                latitude            NUMERIC(10, 7),
                bedrooms            INTEGER,
                bathrooms           INTEGER,
                seller_name         VARCHAR(255),
                seller_phone        VARCHAR(100),
                currency            VARCHAR(10),
                country             VARCHAR(10),
                extra_data          JSONB,
                created_at          TIMESTAMP,
                updated_at          TIMESTAMP
            )
        ');

        // Unique on url — required for ON CONFLICT DO NOTHING in the scraper
        DB::statement('CREATE UNIQUE INDEX idx_announcements_url ON announcements (url) WHERE url IS NOT NULL');
        DB::statement('CREATE INDEX idx_announcements_source   ON announcements (source)');
        DB::statement('CREATE INDEX idx_announcements_country  ON announcements (country)');
        DB::statement('CREATE INDEX idx_announcements_price    ON announcements (price)');
        DB::statement('CREATE INDEX idx_announcements_type     ON announcements (property_type, property_typology)');
        DB::statement('CREATE INDEX idx_announcements_beds     ON announcements (bedrooms)');
        DB::statement('CREATE INDEX idx_announcements_location ON announcements (location)');
        DB::statement('CREATE INDEX idx_announcements_coords   ON announcements (latitude, longitude) WHERE latitude IS NOT NULL AND longitude IS NOT NULL');

        DB::statement('
            CREATE TABLE archived_announcements (
                id                  BIGSERIAL PRIMARY KEY,
                source              VARCHAR(50),
                source_id           TEXT,
                title               TEXT,
                price               NUMERIC(15, 2),
                description         TEXT,
                property_typology   VARCHAR(255),
                property_type       VARCHAR(255),
                price_per_m2        NUMERIC(10, 2),
                url                 TEXT,
                photos              TEXT[],
                interior_features   TEXT,
                exterior_features   TEXT,
                other_features      TEXT,
                location            TEXT,
                longitude           NUMERIC(10, 7),
                latitude            NUMERIC(10, 7),
                bedrooms            INTEGER,
                bathrooms           INTEGER,
                seller_name         VARCHAR(255),
                seller_phone        VARCHAR(100),
                currency            VARCHAR(10),
                country             VARCHAR(10),
                extra_data          JSONB,
                created_at          TIMESTAMP,
                updated_at          TIMESTAMP,
                archived_at         TIMESTAMPTZ DEFAULT NOW(),
                archive_reason      VARCHAR(100) DEFAULT \'listing_removed\'
            )
        ');

        DB::statement('CREATE INDEX idx_archived_url     ON archived_announcements (url)');
        DB::statement('CREATE INDEX idx_archived_country ON archived_announcements (country)');
        DB::statement('CREATE INDEX idx_archived_at      ON archived_announcements (archived_at)');
    }

    public function down(): void
    {
        Schema::dropIfExists('archived_announcements');
        Schema::dropIfExists('announcements');
    }
};
