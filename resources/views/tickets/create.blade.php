@extends('layouts.app')
@section('title', 'إنشاء تذكرة جديدة')

@section('content')
<div class="p-6 md:p-10 max-w-4xl mx-auto animate-fade-in-up">
    <!-- Header -->
    <div class="flex items-center gap-4 mb-10">
        <div class="w-12 h-12 bg-gradient-to-br from-secondary-600 to-primary-600 rounded-xl flex items-center justify-center shadow-[0_0_15px_rgba(63, 158, 143,0.3)]">
            <i data-lucide="ticket" class="w-6 h-6 text-gray-900 dark:text-white"></i>
        </div>
        <div>
            <h2 class="text-3xl font-black text-gray-900 dark:text-white">إنشاء تذكرة جديدة</h2>
            <p class="text-gray-600 dark:text-gray-400 mt-1 font-medium">قدم طلبك بوضوح وسيقوم فريق الدعم بمساعدتك في أسرع وقت</p>
        </div>
    </div>

    @if($errors->any())
    <div class="mb-8 bg-primary-500/10 border border-primary-500/50 text-primary-400 px-6 py-5 rounded-2xl flex gap-4 items-start shadow-[0_0_15px_rgba(63, 158, 143,0.1)]">
        <i data-lucide="alert-circle" class="w-6 h-6 shrink-0 mt-0.5"></i>
        <div class="space-y-1">
            @foreach($errors->all() as $error)
                <p class="font-bold">{{ $error }}</p>
            @endforeach
        </div>
    </div>
    @endif

    <div class="bg-white dark:bg-[#1a1a24] rounded-3xl shadow-xl border border-gray-200 dark:border-gray-800 p-8 md:p-12 relative overflow-hidden">
        <!-- Glow effects -->
        <div class="absolute top-0 right-0 w-64 h-64 bg-primary-600/5 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 w-64 h-64 bg-secondary-600/5 rounded-full blur-3xl pointer-events-none"></div>

        <form method="POST" action="/tickets" enctype="multipart/form-data" class="space-y-10 relative z-10">
            @csrf
            
            <div class="group">
                <label class="block text-sm font-bold text-gray-600 dark:text-gray-400 mb-2 group-focus-within:text-primary-400 transition-colors">عنوان التذكرة <span class="text-primary-500">*</span></label>
                <div class="relative">
                    <input type="text" name="title" value="{{ old('title') }}" required
                        class="w-full bg-transparent border-0 border-b-2 border-gray-300 dark:border-gray-700 px-0 py-3 text-gray-900 dark:text-white text-lg focus:ring-0 focus:border-primary-500 transition-colors placeholder-gray-600 outline-none"
                        placeholder="اكتب عنواناً يصف المشكلة باختصار (5-200 حرف)">
                </div>
            </div>

            <div class="group">
                <label class="block text-sm font-bold text-gray-600 dark:text-gray-400 mb-2 group-focus-within:text-primary-400 transition-colors">وصف المشكلة <span class="text-primary-500">*</span></label>
                <textarea name="description" required rows="5"
                    class="w-full bg-gray-50 dark:bg-gray-200 dark:bg-gray-900/50 border border-gray-300 dark:border-gray-700 rounded-2xl px-5 py-4 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all outline-none resize-y placeholder-gray-600 mt-2"
                    placeholder="اشرح المشكلة بالتفصيل لتسهيل مهمة فريق الدعم (20 حرف على الأقل)...">{{ old('description') }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="group">
                    <label class="block text-sm font-bold text-gray-600 dark:text-gray-400 mb-2 group-focus-within:text-primary-400 transition-colors">نوع المشكلة <span class="text-primary-500">*</span></label>
                    <div class="relative">
                        <select name="type" class="w-full bg-transparent border-0 border-b-2 border-gray-300 dark:border-gray-700 px-0 py-3 text-gray-900 dark:text-white text-lg focus:ring-0 focus:border-primary-500 transition-colors appearance-none outline-none">
                            <option value="technical" class="bg-gray-200 dark:bg-gray-900 text-gray-900 dark:text-white" {{ old('type') == 'technical' ? 'selected' : '' }}>تقني</option>
                            <option value="accounting" class="bg-gray-200 dark:bg-gray-900 text-gray-900 dark:text-white" {{ old('type') == 'accounting' ? 'selected' : '' }}>استفسار محاسبي</option>
                            <option value="development" class="bg-gray-200 dark:bg-gray-900 text-gray-900 dark:text-white" {{ old('type') == 'development' ? 'selected' : '' }}>طلب تطوير</option>
                        </select>
                        <i data-lucide="chevron-down" class="w-5 h-5 absolute left-0 top-1/2 -translate-y-1/2 text-gray-500 pointer-events-none group-focus-within:text-primary-500 transition-colors"></i>
                    </div>
                </div>
                
                <div class="group">
                    <label class="block text-sm font-bold text-gray-600 dark:text-gray-400 mb-2 group-focus-within:text-primary-400 transition-colors">الأولوية <span class="text-primary-500">*</span></label>
                    <div class="relative">
                        <select name="priority" class="w-full bg-transparent border-0 border-b-2 border-gray-300 dark:border-gray-700 px-0 py-3 text-gray-900 dark:text-white text-lg focus:ring-0 focus:border-primary-500 transition-colors appearance-none outline-none">
                            <option value="normal" class="bg-gray-200 dark:bg-gray-900 text-gray-700 dark:text-gray-300" {{ old('priority') == 'normal' ? 'selected' : '' }}>عادية (24-48 ساعة)</option>
                            <option value="high" class="bg-gray-200 dark:bg-gray-900 text-secondary-400" {{ old('priority') == 'high' ? 'selected' : '' }}>مرتفعة (4-12 ساعة)</option>
                            <option value="urgent" class="bg-gray-200 dark:bg-gray-900 text-primary-500 font-bold" {{ old('priority') == 'urgent' ? 'selected' : '' }}>عاجلة (فوري)</option>
                        </select>
                        <i data-lucide="chevron-down" class="w-5 h-5 absolute left-0 top-1/2 -translate-y-1/2 text-gray-500 pointer-events-none group-focus-within:text-primary-500 transition-colors"></i>
                    </div>
                </div>
            </div>

            <div class="group">
                <label class="block text-sm font-bold text-gray-600 dark:text-gray-400 mb-3 group-focus-within:text-primary-400 transition-colors">مرفق (اختياري)</label>
                <div class="relative w-full border-2 border-dashed border-gray-300 dark:border-gray-700 rounded-2xl p-6 text-center hover:border-primary-500/50 hover:bg-primary-500/5 transition-all group-focus-within:border-primary-500/50">
                    <input type="file" name="attachment" accept=".jpg,.jpeg,.png,.pdf" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                    <div class="flex flex-col items-center justify-center gap-2 pointer-events-none">
                        <div class="w-12 h-12 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mb-1 group-focus-within:bg-primary-500/20 group-hover:bg-primary-500/20 transition-colors">
                            <i data-lucide="upload-cloud" class="w-6 h-6 text-gray-600 dark:text-gray-400 group-focus-within:text-primary-400 group-hover:text-primary-400 transition-colors"></i>
                        </div>
                        <span class="text-sm font-bold text-gray-700 dark:text-gray-300">اضغط لاختيار ملف أو قم بسحبه وإفلاته هنا</span>
                        <span class="text-xs font-medium text-gray-500">الأنواع المسموحة: JPG, PNG, PDF — بحد أقصى 2MB</span>
                    </div>
                </div>
            </div>

            <div class="pt-6 flex gap-4">
                <button type="submit" class="bg-gradient-to-r from-secondary-600 to-primary-600 text-gray-900 dark:text-white font-bold px-10 py-4 rounded-full hover:shadow-[0_0_20px_rgba(63, 158, 143,0.5)] hover:-translate-y-0.5 transition-all duration-300 flex items-center gap-2 text-lg">
                    <i data-lucide="send" class="w-5 h-5"></i> إرسال التذكرة
                </button>
                <a href="/tickets" class="bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 font-bold px-10 py-4 rounded-full hover:bg-gray-700 hover:text-gray-900 dark:text-white transition-colors text-lg">إلغاء</a>
            </div>
        </form>
    </div>
</div>

<style>
    .animate-fade-in-up { animation: fadeInUp 0.5s ease-out; }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }
    select { -webkit-appearance: none; -moz-appearance: none; appearance: none; }
</style>
@endsection
