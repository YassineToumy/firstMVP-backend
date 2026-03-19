<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PropertyType;
use Illuminate\Http\Request;

class PropertyTypeController extends Controller
{
    public function index(Request $request)
    {
        $locale = $request->query('lang', 'fr');
        app()->setLocale($locale);

        $types = PropertyType::all()->map(fn($t) => [
            'code'  => $t->code,
            'label' => $t->getTranslation('name', $locale, false) ?: $t->getTranslation('name', 'fr', false) ?: $t->code,
        ]);

        return response()->json(['data' => $types]);
    }
}
