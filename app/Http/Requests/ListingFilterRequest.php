<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ListingFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'country' => 'nullable|string|in:FR,TN,EG,CA',
            'property_type' => 'nullable|string|in:apartment,house,flat',
            'min_price' => 'nullable|numeric|min:0',
            'max_price' => 'nullable|numeric|min:0',
            'bedrooms' => 'nullable|integer|min:1|max:20',
            'min_surface' => 'nullable|numeric|min:0',
            'max_surface' => 'nullable|numeric|min:0',
            'furnished' => 'nullable',
            'city' => 'nullable|string|max:100',
            'sort' => 'nullable|string|in:price_asc,price_desc,newest',
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:100',
        ];
    }
}