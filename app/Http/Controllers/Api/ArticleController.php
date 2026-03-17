<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\JsonResponse;

class ArticleController extends Controller
{
    public function index(): JsonResponse
    {
        $articles = Article::published()
            ->when(request('country'), fn($q, $c) => $q->where('country', $c))
            ->select('id', 'title', 'slug', 'excerpt', 'image_url', 'alt_image', 'country', 'read_time', 'published_at')
            ->orderByDesc('published_at')
            ->paginate(request('per_page', 12));

        return response()->json($articles);
    }

    public function show(string $slug): JsonResponse
    {
        $article = Article::published()->where('slug', $slug)->firstOrFail();

        return response()->json(['data' => $article]);
    }
}
