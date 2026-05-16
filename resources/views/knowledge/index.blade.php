@extends('layouts.app')

@section('title', 'قاعدة المعرفة')

@section('content')
<div class="p-6 md:p-10 max-w-7xl mx-auto space-y-8 animate-fade-in-up">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between bg-gradient-to-br from-[#1e1e2d] to-[#151521] border border-gray-200 dark:border-gray-800 rounded-3xl p-8 shadow-2xl relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-r from-secondary-600/10 to-primary-600/10 mix-blend-overlay pointer-events-none"></div>
        <div class="relative z-10">
            <h2 class="text-3xl font-black text-gray-900 dark:text-white flex items-center gap-3">
                <i data-lucide="book-open" class="w-8 h-8 text-primary-500"></i> قاعدة المعرفة
            </h2>
            <p class="text-gray-600 dark:text-gray-400 mt-2 text-lg font-medium">ابحث عن حلول للمشاكل الشائعة، الدروس، والأسئلة المتكررة</p>
        </div>
        @if(Auth::check() && Auth::user()->isSupport())
        <div class="mt-4 md:mt-0 relative z-10">
            <a href="/knowledge/create" class="bg-gradient-to-r from-secondary-600 to-primary-600 text-gray-900 dark:text-white px-6 py-3 rounded-xl font-bold hover:from-secondary-500 hover:to-primary-500 hover:scale-105 transition-all shadow-[0_0_15px_rgba(63, 158, 143,0.4)] flex items-center gap-2">
                <i data-lucide="plus" class="w-5 h-5"></i> إضافة مقال
            </a>
        </div>
        @endif
    </div>

    <!-- Search Bar -->
    <form method="GET" action="/knowledge" class="bg-white dark:bg-[#1a1a24] rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 p-4 flex gap-4">
        <div class="relative flex-1">
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="ابحث عن مقالات، حلول، أو كلمات مفتاحية..."
                class="w-full pr-12 pl-4 py-4 text-lg bg-gray-200 dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl focus:ring-0 focus:border-primary-500 text-gray-900 dark:text-white placeholder-gray-600 transition-colors"
            >
            <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                <i data-lucide="search" class="h-6 w-6 text-gray-500"></i>
            </div>
        </div>
        <button type="submit" class="bg-gray-100 dark:bg-gray-800 hover:bg-gray-700 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-white px-8 py-4 rounded-xl font-bold transition-colors">
            بحث
        </button>
    </form>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Categories Sidebar -->
        <div class="lg:col-span-1 space-y-4">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white px-2">التصنيفات</h3>
            <div class="bg-white dark:bg-[#1a1a24] rounded-2xl border border-gray-200 dark:border-gray-800 p-2 overflow-hidden">
                <a href="/knowledge" class="flex items-center justify-between px-4 py-3 rounded-xl transition-colors {{ !request('category_id') ? 'bg-primary-500/10 text-primary-500 font-bold border border-primary-500/20' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:bg-gray-100 dark:bg-gray-800/50 hover:text-gray-800 dark:text-gray-200' }}">
                    <div class="flex items-center gap-3">
                        <i data-lucide="grid" class="w-5 h-5 {{ !request('category_id') ? 'text-primary-500' : 'text-gray-500' }}"></i>
                        <span>الكل</span>
                    </div>
                </a>
                
                @foreach($categories as $category)
                <a href="/knowledge?category_id={{ $category->id }}{{ request('search') ? '&search='.request('search') : '' }}" class="flex items-center justify-between px-4 py-3 rounded-xl transition-colors {{ request('category_id') == $category->id ? 'bg-primary-500/10 text-primary-500 font-bold border border-primary-500/20' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:bg-gray-100 dark:bg-gray-800/50 hover:text-gray-800 dark:text-gray-200' }}">
                    <div class="flex items-center gap-3">
                        <span class="text-xl opacity-80">{{ $category->icon }}</span>
                        <span>{{ $category->name }}</span>
                    </div>
                    <span class="bg-gray-200 dark:bg-gray-900 border border-gray-200 dark:border-gray-800 text-gray-500 py-1 px-3 rounded-full text-xs font-bold">{{ $category->articles_count }}</span>
                </a>
                @endforeach
            </div>
        </div>

        <!-- Articles Grid -->
        <div class="lg:col-span-3">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @forelse($articles as $article)
                <a href="/knowledge/{{ $article->id }}" class="block bg-white dark:bg-[#1a1a24] rounded-2xl border border-gray-200 dark:border-gray-800 p-6 hover:border-primary-500/50 hover:bg-primary-500/5 transition-all duration-300 group hover:-translate-y-1 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-primary-600/5 rounded-full blur-3xl group-hover:bg-primary-600/10 transition-all"></div>
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-700 group-hover:border-primary-500/30 transition-colors">
                                {{ $article->category->icon ?? '' }} {{ $article->category->name ?? 'غير مصنف' }}
                            </span>
                            <div class="flex items-center text-sm text-gray-500 gap-1 bg-gray-200 dark:bg-gray-900/80 px-2 py-1 rounded-lg border border-gray-200 dark:border-gray-800">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                                <span class="font-bold">{{ $article->views }}</span>
                            </div>
                        </div>
                        
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3 group-hover:text-primary-400 transition-colors line-clamp-2">
                            {{ $article->title }}
                        </h3>
                        
                        <p class="text-gray-500 mb-6 line-clamp-3 leading-relaxed text-sm">
                            {{ Str::limit(strip_tags($article->content), 150) }}
                        </p>
                        
                        <div class="flex items-center justify-between mt-auto pt-4 border-t border-gray-200 dark:border-gray-800/50">
                            <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 font-medium">
                                <div class="w-6 h-6 bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-full flex items-center justify-center text-xs border border-gray-300 dark:border-gray-700">
                                    {{ mb_substr($article->author->name ?? '?', 0, 1) }}
                                </div>
                                {{ $article->author->name ?? 'غير معروف' }}
                            </div>
                            <div class="text-xs text-gray-600 font-bold">
                                {{ $article->created_at->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                </a>
                @empty
                <div class="col-span-2 text-center py-20 bg-white dark:bg-[#1a1a24] rounded-3xl border border-dashed border-gray-300 dark:border-gray-700">
                    <div class="bg-gray-100 dark:bg-gray-100 dark:bg-gray-800/50 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-300 dark:border-gray-700">
                        <i data-lucide="search-x" class="w-10 h-10 text-gray-500"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">لا توجد مقالات مطابقة</h3>
                    <p class="text-gray-500 mt-2">جرب البحث بكلمات مختلفة أو تصفح تصنيف آخر</p>
                </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $articles->links() }}
            </div>
        </div>
    </div>
</div>

<style>
    .animate-fade-in-up { animation: fadeInUp 0.5s ease-out; }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }
</style>
@endsection
