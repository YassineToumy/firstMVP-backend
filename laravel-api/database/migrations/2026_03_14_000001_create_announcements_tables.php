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

        Schema::create('announcements', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('title')->nullable();
            $table->decimal('price', 15, 2)->nullable();
            $table->text('description')->nullable();
            $table->string('property_typology')->nullable(); // apartment, house, villa, etc.
            $table->string('property_type')->nullable();     // rent, sale
            $table->specificType('photos', 'text[]')->nullable(); // PostgreSQL native array
            $table->jsonb('interior_features')->nullable();  // { surface_m2, ... }
            $table->jsonb('exterior_features')->nullable();
            $table->jsonb('other_features')->nullable();
            $table->jsonb('extra_data')->nullable();
            $table->string('country', 10)->nullable();
            $table->string('location')->nullable();
            $table->string('city')->nullable();
            $table->integer('bedrooms')->nullable();
            $table->integer('bathrooms')->nullable();
            $table->decimal('surface_m2', 10, 2)->nullable();
            $table->decimal('price_per_m2', 10, 2)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('source')->nullable();
            $table->string('source_id')->nullable();
            $table->text('url')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            // Indexes for common filters
            $table->index('country');
            $table->index('property_type');
            $table->index('property_typology');
            $table->index('city');
            $table->index('bedrooms');
            $table->index('price');
            $table->index(['source', 'source_id']);
        });

        Schema::create('archived_announcements', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('title')->nullable();
            $table->decimal('price', 15, 2)->nullable();
            $table->text('description')->nullable();
            $table->string('property_typology')->nullable();
            $table->string('property_type')->nullable();
            $table->specificType('photos', 'text[]')->nullable();
            $table->jsonb('interior_features')->nullable();
            $table->jsonb('exterior_features')->nullable();
            $table->jsonb('other_features')->nullable();
            $table->jsonb('extra_data')->nullable();
            $table->string('country', 10)->nullable();
            $table->string('location')->nullable();
            $table->string('city')->nullable();
            $table->integer('bedrooms')->nullable();
            $table->integer('bathrooms')->nullable();
            $table->decimal('surface_m2', 10, 2)->nullable();
            $table->decimal('price_per_m2', 10, 2)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('source')->nullable();
            $table->string('source_id')->nullable();
            $table->text('url')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('archived_at')->nullable();

            $table->index('country');
            $table->index('archived_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('archived_announcements');
        Schema::dropIfExists('announcements');
    }
};
