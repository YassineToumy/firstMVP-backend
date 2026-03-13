<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateListingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'             => 'required|string|max:500',
            'price'             => 'nullable|numeric|min:0',
            'description'       => 'nullable|string',
            'property_typology' => 'nullable|string|max:100',
            'property_type'     => 'nullable|string|in:rent,sale,buy',
            'country'           => 'nullable|string|in:FR,TN,EG,CA',
            'location'          => 'nullable|string|max:255',
            'bedrooms'          => 'nullable|integer|min:0',
            'bathrooms'         => 'nullable|integer|min:0',
            'price_per_m2'      => 'nullable|numeric|min:0',
            'latitude'          => 'nullable|numeric|between:-90,90',
            'longitude'         => 'nullable|numeric|between:-180,180',
            'photos'            => 'nullable|array',
            'photos.*'          => 'nullable|string|url',
            'interior_features' => 'nullable|array',
            'exterior_features' => 'nullable|array',
            'other_features'    => 'nullable|array',
            'extra_data'        => 'nullable|array',
        ];
    }
}
