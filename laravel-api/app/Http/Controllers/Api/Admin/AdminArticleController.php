<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminArticleController extends Controller
{
    public function index(): JsonResponse
    {
        $articles = Article::with('admin:id,name')
            ->when(request('status'), fn($q, $s) => $q->where('status', $s))
            ->when(request('country'), fn($q, $c) => $q->where('country', $c))
            ->orderByDesc('created_at')
            ->paginate(request('per_page', 20));

        return response()->json($articles);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title'            => 'required|string|max:255',
            'slug'             => 'nullable|string|unique:articles,slug|max:255',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'excerpt'          => 'nullable|string|max:1000',
            'image_url'        => 'nullable|url|max:500',
            'alt_image'        => 'nullable|string|max:255',
            'country'          => 'nullable|string|size:2',
            'read_time'        => 'nullable|integer|min:1|max:999',
            'article_contents' => 'required|string',
            'status'           => 'in:draft,published,archived',
        ]);

        $data['slug']       = $data['slug'] ?? Str::slug($data['title']);
        $data['admin_id']   = $request->user('admin')->id;
        $data['published_at'] = ($data['status'] ?? 'draft') === 'published' ? now() : null;

        $article = Article::create($data);

        return response()->json(['data' => $article], 201);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(['data' => Article::findOrFail($id)]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $article = Article::findOrFail($id);

        $data = $request->validate([
            'title'            => 'sometimes|string|max:255',
            'slug'             => "sometimes|string|unique:articles,slug,{$id}|max:255",
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'excerpt'          => 'nullable|string|max:1000',
            'image_url'        => 'nullable|url|max:500',
            'alt_image'        => 'nullable|string|max:255',
            'country'          => 'nullable|string|size:2',
            'read_time'        => 'nullable|integer|min:1|max:999',
            'article_contents' => 'sometimes|string',
            'status'           => 'in:draft,published,archived',
        ]);

        if (isset($data['status']) && $data['status'] === 'published' && ! $article->published_at) {
            $data['published_at'] = now();
        }

        $article->update($data);

        return response()->json(['data' => $article]);
    }

    public function destroy(int $id): JsonResponse
    {
        Article::findOrFail($id)->delete();

        return response()->json(['message' => 'Article supprimé.']);
    }
}
