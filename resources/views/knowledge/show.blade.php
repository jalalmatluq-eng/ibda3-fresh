@extends('layouts.app')

@section('title', $article->title)

@section('content')
<div class="p-6 md:p-10 max-w-7xl mx-auto animate-fade-in-up">
    
    <!-- Breadcrumb -->
    <nav class="flex items-center text-sm text-gray-500 mb-6 font-medium">
        <a href="/knowledge" class="hover:text-red-500 transition-colors flex items-center gap-1"><i data-lucide="home" class="w-4 h-4"></i> قاعدة المعرفة</a>
        <i data-lucide="chevron-left" class="w-4 h-4 mx-1"></i>
        <a href="/knowledge?category_id={{ $article->category_id }}" class="hover:text-red-500 transition-colors">{{ $article->category->name ?? 'غير مصنف' }}</a>
        <i data-lucide="chevron-left" class="w-4 h-4 mx-1"></i>
        <span class="text-gray-300 truncate max-w-xs">{{ $article->title }}</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        
        <!-- Main Content (Left Side) -->
        <div class="lg:col-span-3 space-y-6">
            <!-- Article Header -->
            <div class="bg-[#1a1a24] rounded-3xl border border-gray-800 overflow-hidden relative">
                <div class="absolute top-0 right-0 w-64 h-64 bg-red-600/5 rounded-full blur-3xl pointer-events-none"></div>
                
                <div class="p-8 md:p-10 relative z-10">
                    <div class="flex items-center gap-3 mb-6">
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold bg-gray-800/80 text-gray-300 border border-gray-700">
                            {{ $article->category->icon ?? '' }} {{ $article->category->name ?? 'غير مصنف' }}
                        </span>
                        <div class="flex items-center text-sm text-gray-400 gap-1 bg-gray-900/50 px-3 py-2 rounded-xl border border-gray-800">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                            <span class="font-bold">{{ $article->views }} مشاهدة</span>
                        </div>
                    </div>
                    
                    <h1 class="text-3xl md:text-4xl font-black text-white mb-6 leading-tight">
                        {{ $article->title }}
                    </h1>
                    
                    <div class="flex flex-wrap items-center gap-6 text-sm text-gray-400 pb-8 border-b border-gray-800/80">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gray-800 text-gray-300 rounded-full flex items-center justify-center font-bold border border-gray-700">
                                {{ mb_substr($article->author->name ?? '?', 0, 1) }}
                            </div>
                            <div class="flex flex-col">
                                <span class="font-bold text-gray-200">{{ $article->author->name ?? 'غير معروف' }}</span>
                                <span class="text-xs text-gray-500">الكاتب</span>
                            </div>
                        </div>
                        <div class="h-8 w-px bg-gray-800 hidden md:block"></div>
                        <div class="flex items-center gap-2">
                            <i data-lucide="calendar" class="w-4 h-4 text-gray-500"></i>
                            <span class="font-medium">{{ $article->created_at->format('Y-m-d') }}</span>
                        </div>
                    </div>

                    <!-- Article Body -->
                    <div class="mt-8 prose prose-lg prose-invert max-w-none text-gray-300 leading-loose prose-a:text-red-400 hover:prose-a:text-red-300 prose-strong:text-white">
                        {!! nl2br(e($article->content)) !!}
                    </div>

                    <!-- Keywords -->
                    @if(isset($article->keywords) && !empty($article->keywords))
                    <div class="mt-10 pt-6 border-t border-gray-800/80">
                        <div class="flex items-center gap-2 mb-4 text-sm font-bold text-gray-500">
                            <i data-lucide="tags" class="w-4 h-4"></i> الكلمات المفتاحية:
                        </div>
                        <div class="flex flex-wrap gap-2">
                            @foreach(explode(',', $article->keywords) as $keyword)
                                @if(trim($keyword))
                                <span class="px-4 py-1.5 bg-gray-900 border border-gray-700 text-gray-400 rounded-lg text-sm cursor-default hover:bg-gray-800 hover:text-gray-300 transition-colors">
                                    {{ trim($keyword) }}
                                </span>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Rating Section -->
            @auth
            <div class="bg-gradient-to-r from-gray-900 to-[#1a1a24] rounded-3xl p-8 border border-gray-800 flex flex-col sm:flex-row items-center justify-between gap-6 shadow-lg relative overflow-hidden">
                <div class="absolute left-0 top-0 w-1/2 h-full bg-gradient-to-r from-red-600/5 to-transparent pointer-events-none"></div>
                <div class="relative z-10">
                    <h3 class="text-lg font-bold text-white mb-1">هل كان هذا المقال مفيداً؟</h3>
                    <p class="text-gray-500 text-sm font-medium">تقييمك يساعدنا على تحسين محتوى قاعدة المعرفة باستمرار.</p>
                </div>
                <div class="flex items-center gap-3 relative z-10">
                    <form method="POST" action="/knowledge/{{ $article->id }}/rate">
                        @csrf
                        <input type="hidden" name="helpful" value="1">
                        <button type="submit" class="flex items-center gap-2 px-6 py-3 bg-gray-800 border border-gray-700 text-gray-300 rounded-xl hover:bg-emerald-500/10 hover:text-emerald-400 hover:border-emerald-500/50 font-bold transition-all group">
                            <i data-lucide="thumbs-up" class="w-5 h-5 group-hover:-translate-y-1 transition-transform"></i>
                            نعم ({{ $article->helpful }})
                        </button>
                    </form>
                    <form method="POST" action="/knowledge/{{ $article->id }}/rate">
                        @csrf
                        <input type="hidden" name="helpful" value="0">
                        <button type="submit" class="flex items-center gap-2 px-6 py-3 bg-gray-800 border border-gray-700 text-gray-300 rounded-xl hover:bg-red-500/10 hover:text-red-400 hover:border-red-500/50 font-bold transition-all group">
                            <i data-lucide="thumbs-down" class="w-5 h-5 group-hover:translate-y-1 transition-transform"></i>
                            لا ({{ $article->not_helpful }})
                        </button>
                    </form>
                </div>
            </div>
            
            @if(session('success'))
            <div class="bg-emerald-500/10 text-emerald-400 p-4 rounded-xl border border-emerald-500/20 flex items-center gap-3 animate-fade-in-up">
                <i data-lucide="check-circle" class="w-5 h-5"></i>
                <span class="font-bold">{{ session('success') }}</span>
            </div>
            @endif
            @if(session('error'))
            <div class="bg-red-500/10 text-red-400 p-4 rounded-xl border border-red-500/20 flex items-center gap-3 animate-fade-in-up">
                <i data-lucide="alert-circle" class="w-5 h-5"></i>
                <span class="font-bold">{{ session('error') }}</span>
            </div>
            @endif
            @endauth
        </div>

        <!-- Sidebar (Right Side) -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Related Articles -->
            <div class="bg-[#1a1a24] rounded-3xl border border-gray-800 p-6 sticky top-24">
                <h3 class="text-lg font-bold text-white mb-5 flex items-center gap-2">
                    <i data-lucide="layers" class="w-5 h-5 text-red-500"></i> مقالات ذات صلة
                </h3>
                
                @if(isset($related) && $related->count() > 0)
                    <div class="space-y-4">
                        @foreach($related as $rel)
                        <a href="/knowledge/{{ $rel->id }}" class="block p-4 rounded-2xl border border-gray-800 hover:border-red-500/30 hover:bg-gray-800/50 transition-all group">
                            <h4 class="font-bold text-gray-300 text-sm mb-3 line-clamp-2 group-hover:text-red-400 transition-colors leading-relaxed">
                                {{ $rel->title }}
                            </h4>
                            <div class="flex items-center justify-between text-xs text-gray-500 font-medium">
                                <span class="flex items-center gap-1.5"><i data-lucide="eye" class="w-3 h-3"></i> {{ $rel->views }}</span>
                                <span>{{ $rel->created_at->diffForHumans() }}</span>
                            </div>
                        </a>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <i data-lucide="book-x" class="w-8 h-8 text-gray-600 mx-auto mb-2"></i>
                        <p class="text-gray-500 text-sm font-medium">لا توجد مقالات ذات صلة حالياً</p>
                    </div>
                @endif
            </div>
        </div>
        
    </div>
</div>

<style>
    .animate-fade-in-up { animation: fadeInUp 0.5s ease-out; }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }
</style>
@endsection
