<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('announcements')) {
            return;
        }

        Schema::create('announcements', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('title')->nullable();
            $table->decimal('price', 15, 2)->nullable();
            $table->text('description')->nullable();
            $table->string('property_typology')->nullable(); // apartment, house, etc.
            $table->string('property_type')->nullable();     // rent, sale, etc.
            $table->text('photos')->nullable();              // PostgreSQL TEXT[] stored as text
            $table->text('interior_features')->nullable();   // JSON string
            $table->text('exterior_features')->nullable();   // JSON string
            $table->text('other_features')->nullable();      // JSON string
            $table->jsonb('extra_data')->nullable();
            $table->string('country')->nullable();
            $table->string('location')->nullable();
            $table->integer('bedrooms')->nullable();
            $table->integer('bathrooms')->nullable();
            $table->decimal('surface_m2', 10, 2)->nullable();
            $table->decimal('price_per_m2', 10, 2)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('source')->nullable();
            $table->string('source_id')->nullable();
            $table->string('url')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};