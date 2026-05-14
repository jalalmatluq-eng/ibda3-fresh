<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\KbCategory;
use App\Models\KnowledgeArticle;
use App\Models\KbRating;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KnowledgeWebController extends Controller
{
    public function index(Request $request)
    {
        $categories = KbCategory::withCount('articles')->get();
        $query      = KnowledgeArticle::with(['category:id,name,icon', 'author:id,name']);

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

        $articles = $query->orderByDesc('views')->paginate(12)->withQueryString();
        return view('knowledge.index', compact('categories', 'articles'));
    }

    public function show($id)
    {
        $article = KnowledgeArticle::with(['category:id,name,icon', 'author:id,name'])->findOrFail($id);
        $article->increment('views');
        $related = KnowledgeArticle::where('category_id', $article->category_id)
            ->where('id', '!=', $article->id)->limit(4)->get();
        return view('knowledge.show', compact('article', 'related'));
    }

    public function rate(Request $request, $id)
    {
        $article = KnowledgeArticle::findOrFail($id);
        $request->validate(['helpful' => 'required|boolean']);

        $existing = KbRating::where('user_id', Auth::id())->where('article_id', $id)->first();
        if ($existing) {
            return back()->with('error', 'لقد قمت بتقييم هذا المقال مسبقاً.');
        }

        KbRating::create([
            'user_id' => Auth::id(),
            'article_id' => $id,
            'is_helpful' => $request->helpful,
        ]);

        $request->helpful ? $article->increment('helpful') : $article->increment('not_helpful');
        return back()->with('success', 'شكراً على تقييمك!');
    }

    public function create()
    {
        $categories = KbCategory::all();
        return view('knowledge.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'       => 'required|string|min:5|max:255',
            'content'     => 'required|string|min:20',
            'category_id' => 'required|exists:kb_categories,id',
            'keywords'    => 'nullable|string|max:255',
        ]);
        $article = KnowledgeArticle::create([...$data, 'created_by' => Auth::id()]);
        
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'إضافة مقال',
            'description' => 'قام بإنشاء مقال جديد في قاعدة المعرفة',
            'model_type' => KnowledgeArticle::class,
            'model_id' => $article->id,
        ]);

        return redirect('/knowledge')->with('success', 'تم إضافة المقال!');
    }
}
