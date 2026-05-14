<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KbCategory;
use App\Models\KnowledgeArticle;
use Illuminate\Http\Request;

class KnowledgeController extends Controller
{
    // GET /api/kb/categories
    public function categories()
    {
        return response()->json(
            KbCategory::withCount('articles')->get()
        );
    }

    // GET /api/kb/articles
    public function index(Request $request)
    {
        $query = KnowledgeArticle::with(['category:id,name,icon', 'author:id,name']);

        if ($request->search) {
            $s = $request->search;
            $query->where(fn($q) =>
                $q->where('title', 'like', "%$s%")
                  ->orWhere('content', 'like', "%$s%")
                  ->orWhere('keywords', 'like', "%$s%")
            );
        }

        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        $query->orderByDesc('views');

        return response()->json($query->paginate(12));
    }

    // GET /api/kb/articles/{id}
    public function show(KnowledgeArticle $article)
    {
        $article->incrementViews();
        return response()->json(
            $article->load(['category:id,name,icon', 'author:id,name'])
        );
    }

    // POST /api/kb/articles  (دعم/مشرف فقط)
    public function store(Request $request)
    {
        if (! $request->user()->isSupport()) {
            return response()->json(['message' => 'غير مصرح.'], 403);
        }

        $data = $request->validate([
            'title'       => 'required|string|min:5|max:255',
            'content'     => 'required|string|min:20',
            'category_id' => 'required|exists:kb_categories,id',
            'keywords'    => 'nullable|string|max:255',
        ]);

        $article = KnowledgeArticle::create([
            ...$data,
            'created_by' => $request->user()->id,
        ]);

        return response()->json($article->load('category:id,name'), 201);
    }

    // PUT /api/kb/articles/{id}
    public function update(Request $request, KnowledgeArticle $article)
    {
        if (! $request->user()->isSupport()) {
            return response()->json(['message' => 'غير مصرح.'], 403);
        }

        $data = $request->validate([
            'title'       => 'sometimes|string|min:5|max:255',
            'content'     => 'sometimes|string|min:20',
            'category_id' => 'sometimes|exists:kb_categories,id',
            'keywords'    => 'nullable|string|max:255',
        ]);

        $article->update($data);
        return response()->json($article->fresh('category:id,name'));
    }

    // DELETE /api/kb/articles/{id}
    public function destroy(Request $request, KnowledgeArticle $article)
    {
        if (! $request->user()->isAdmin()) {
            return response()->json(['message' => 'غير مصرح.'], 403);
        }

        $article->delete();
        return response()->json(['message' => 'تم الحذف.']);
    }

    // POST /api/kb/articles/{id}/rate
    public function rate(Request $request, KnowledgeArticle $article)
    {
        $request->validate(['helpful' => 'required|boolean']);

        if ($request->helpful) {
            $article->increment('helpful');
        } else {
            $article->increment('not_helpful');
        }

        return response()->json(['message' => 'شكراً على تقييمك.']);
    }
}
