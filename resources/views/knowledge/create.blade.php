@extends('layouts.app')
@section('title', 'إضافة مقال جديد')

@section('content')
<div class="p-6 md:p-10 max-w-4xl mx-auto animate-fade-in-up">
    <!-- Header -->
    <div class="flex items-center gap-4 mb-10">
        <div class="w-12 h-12 bg-gradient-to-br from-orange-600 to-red-600 rounded-xl flex items-center justify-center shadow-[0_0_15px_rgba(239,68,68,0.3)]">
            <i data-lucide="book-plus" class="w-6 h-6 text-white"></i>
        </div>
        <div>
            <h2 class="text-3xl font-black text-white">إضافة مقال جديد</h2>
            <p class="text-gray-400 mt-1 font-medium">قم بإثراء قاعدة المعرفة بمقال أو حل جديد</p>
        </div>
    </div>

    @if($errors->any())
    <div class="mb-8 bg-red-500/10 border border-red-500/50 text-red-400 px-6 py-5 rounded-2xl flex gap-4 items-start shadow-[0_0_15px_rgba(239,68,68,0.1)]">
        <i data-lucide="alert-circle" class="w-6 h-6 shrink-0 mt-0.5"></i>
        <div class="space-y-1">
            @foreach($errors->all() as $error)
                <p class="font-bold">{{ $error }}</p>
            @endforeach
        </div>
    </div>
    @endif

    <div class="bg-[#1a1a24] rounded-3xl shadow-xl border border-gray-800 p-8 md:p-12 relative overflow-hidden">
        <!-- Glow effects -->
        <div class="absolute top-0 right-0 w-64 h-64 bg-red-600/5 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 w-64 h-64 bg-orange-600/5 rounded-full blur-3xl pointer-events-none"></div>

        <form method="POST" action="/knowledge" class="space-y-10 relative z-10">
            @csrf
            
            <div class="group">
                <label class="block text-sm font-bold text-gray-400 mb-2 group-focus-within:text-red-400 transition-colors">عنوان المقال <span class="text-red-500">*</span></label>
                <div class="relative">
                    <input type="text" name="title" value="{{ old('title') }}" required
                        class="w-full bg-transparent border-0 border-b-2 border-gray-700 px-0 py-3 text-white text-lg focus:ring-0 focus:border-red-500 transition-colors placeholder-gray-600 outline-none"
                        placeholder="مثال: كيفية إعداد قيد يومي جديد">
                </div>
            </div>

            <div class="group">
                <label class="block text-sm font-bold text-gray-400 mb-2 group-focus-within:text-red-400 transition-colors">التصنيف <span class="text-red-500">*</span></label>
                <div class="relative">
                    <select name="category_id" required 
                        class="w-full bg-transparent border-0 border-b-2 border-gray-700 px-0 py-3 text-white text-lg focus:ring-0 focus:border-red-500 transition-colors appearance-none outline-none">
                        <option value="" class="bg-gray-900 text-gray-500">-- اختر التصنيف --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" class="bg-gray-900 text-white" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->icon }} {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    <i data-lucide="chevron-down" class="w-5 h-5 absolute left-0 top-1/2 -translate-y-1/2 text-gray-500 pointer-events-none group-focus-within:text-red-500 transition-colors"></i>
                </div>
            </div>

            <div class="group">
                <label class="block text-sm font-bold text-gray-400 mb-2 group-focus-within:text-red-400 transition-colors">المحتوى <span class="text-red-500">*</span></label>
                <textarea name="content" required rows="10"
                    class="w-full bg-gray-900/50 border border-gray-700 rounded-2xl px-5 py-4 text-white focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-all outline-none resize-y placeholder-gray-600 mt-2"
                    placeholder="اكتب محتوى المقال هنا...">{{ old('content') }}</textarea>
                <p class="text-xs font-bold text-gray-500 mt-3 flex items-center gap-1"><i data-lucide="info" class="w-4 h-4"></i> يجب أن يحتوي على 20 حرفاً على الأقل.</p>
            </div>

            <div class="group">
                <label class="block text-sm font-bold text-gray-400 mb-2 group-focus-within:text-red-400 transition-colors">الكلمات المفتاحية (اختياري)</label>
                <div class="relative">
                    <input type="text" name="keywords" value="{{ old('keywords') }}"
                        class="w-full bg-transparent border-0 border-b-2 border-gray-700 px-0 py-3 text-white text-lg focus:ring-0 focus:border-red-500 transition-colors placeholder-gray-600 outline-none"
                        placeholder="مثال: فاتورة, قيد, ضريبة (افصل بينها بفاصلة)">
                    <i data-lucide="tags" class="w-5 h-5 absolute left-0 top-1/2 -translate-y-1/2 text-gray-600 pointer-events-none"></i>
                </div>
            </div>

            <div class="pt-6 flex gap-4">
                <button type="submit" class="bg-gradient-to-r from-orange-600 to-red-600 text-white font-bold px-10 py-4 rounded-full hover:shadow-[0_0_20px_rgba(239,68,68,0.5)] hover:-translate-y-0.5 transition-all duration-300 flex items-center gap-2 text-lg">
                    <i data-lucide="save" class="w-5 h-5"></i> حفظ ونشر
                </button>
                <a href="/knowledge" class="bg-gray-800 border border-gray-700 text-gray-300 font-bold px-10 py-4 rounded-full hover:bg-gray-700 hover:text-white transition-colors text-lg">إلغاء</a>
            </div>
        </form>
    </div>
</div>

<style>
    .animate-fade-in-up { animation: fadeInUp 0.5s ease-out; }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }
    
    /* Remove default styling for select in dark mode to fix native appearance */
    select { -webkit-appearance: none; -moz-appearance: none; appearance: none; }
</style>
@endsection
