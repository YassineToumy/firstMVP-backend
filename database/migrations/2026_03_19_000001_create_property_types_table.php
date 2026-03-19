<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('property_types', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // normalized code: apartment, house, villa...
            $table->json('name');             // {"fr": "Appartement", "en": "Apartment", "ar": "شقة", "es": "Apartamento"}
            $table->json('variants');         // raw scraper values that map to this code
            $table->timestamps();
        });

        // Seed property types with translations and scraper variants
        $types = [
            [
                'code' => 'apartment',
                'name' => json_encode(['fr' => 'Appartement', 'en' => 'Apartment', 'ar' => 'شقة', 'es' => 'Apartamento']),
                'variants' => json_encode(['Appartement', 'Appartement meublé', 'Duplex', 'Penthouse', 'apartment', 'Appartement T1', 'Appartement T2', 'Appartement T3', 'Appartement T4', 'Appartement T5', 'Appartement T6', 'شقة', 'شقق', 'Flat', 'flat', 'Appartamento', 'Triplex']),
            ],
            [
                'code' => 'house',
                'name' => json_encode(['fr' => 'Maison', 'en' => 'House', 'ar' => 'منزل', 'es' => 'Casa']),
                'variants' => json_encode(['Maison', 'Maison individuelle', 'Maison de ville', 'Maison de campagne', 'house', 'House', 'منزل', 'بيت', 'Townhouse', 'townhouse', 'Bungalow', 'bungalow', 'Semi-detached']),
            ],
            [
                'code' => 'villa',
                'name' => json_encode(['fr' => 'Villa', 'en' => 'Villa', 'ar' => 'فيلا', 'es' => 'Villa']),
                'variants' => json_encode(['Villa', 'villa', 'فيلا', 'Villa individuelle', 'Chalet', 'chalet', 'Mas', 'Bastide']),
            ],
            [
                'code' => 'studio',
                'name' => json_encode(['fr' => 'Studio', 'en' => 'Studio', 'ar' => 'استوديو', 'es' => 'Estudio']),
                'variants' => json_encode(['Studio', 'studio', 'استوديو', 'Studio meublé', 'Chambre', 'Room']),
            ],
            [
                'code' => 'land',
                'name' => json_encode(['fr' => 'Terrain', 'en' => 'Land', 'ar' => 'أرض', 'es' => 'Terreno']),
                'variants' => json_encode(['Terrain', 'terrain', 'Land', 'land', 'أرض', 'Lot', 'Parcelle', 'Terrain constructible', 'Terrain agricole']),
            ],
            [
                'code' => 'commercial',
                'name' => json_encode(['fr' => 'Local commercial', 'en' => 'Commercial', 'ar' => 'محل تجاري', 'es' => 'Local comercial']),
                'variants' => json_encode(['Local commercial', 'Commerce', 'Bureau', 'Office', 'office', 'مكتب', 'محل', 'Fonds de commerce', 'Entrepôt', 'Boutique', 'Immeuble']),
            ],
        ];

        DB::table('property_types')->insert(array_map(fn($t) => array_merge($t, ['created_at' => now(), 'updated_at' => now()]), $types));
    }

    public function down(): void
    {
        Schema::dropIfExists('property_types');
    }
};
