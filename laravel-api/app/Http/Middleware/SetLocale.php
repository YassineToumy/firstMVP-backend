<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocale
{
    protected array $supported = ['en', 'fr', 'ar', 'es'];

    public function handle(Request $request, Closure $next): mixed
    {
        $lang = $request->query('lang') ?? $request->header('X-Locale');

        if ($lang && in_array($lang, $this->supported, true)) {
            App::setLocale($lang);
        }

        return $next($request);
    }
}
