@extends('layouts.app')
@section('title', 'إدارة المستخدمين')

@section('content')
<div class="p-6 md:p-10 max-w-7xl mx-auto space-y-8 animate-fade-in-up">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between bg-gradient-to-br from-[#1e1e2d] to-[#151521] border border-gray-200 dark:border-gray-800 rounded-3xl p-8 shadow-2xl relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-r from-primary-600/5 to-secondary-600/5 mix-blend-overlay pointer-events-none"></div>
        <div class="relative z-10">
            <h2 class="text-3xl font-black text-gray-900 dark:text-white flex items-center gap-3">
                <i data-lucide="users" class="w-8 h-8 text-primary-500"></i> إدارة المستخدمين
            </h2>
            <p class="text-gray-600 dark:text-gray-400 mt-2 text-lg font-medium">إجمالي: <span class="text-gray-900 dark:text-white font-bold">{{ $users->total() }}</span> مستخدم</p>
        </div>
        <div class="mt-4 md:mt-0 relative z-10">
            <button onclick="document.getElementById('createForm').classList.toggle('hidden')" class="bg-gradient-to-r from-secondary-600 to-primary-600 text-gray-900 dark:text-white px-6 py-3 rounded-xl font-bold hover:from-secondary-500 hover:to-primary-500 hover:scale-105 transition-all shadow-[0_0_15px_rgba(63, 158, 143,0.4)] flex items-center gap-2">
                <i data-lucide="plus" class="w-5 h-5"></i> مستخدم جديد
            </button>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 px-6 py-4 rounded-2xl flex items-center gap-3">
        <i data-lucide="check-circle" class="w-5 h-5"></i>
        <span class="font-bold">{{ session('success') }}</span>
    </div>
    @endif

    <!-- Create Form -->
    <div id="createForm" class="hidden bg-white dark:bg-[#1a1a24] rounded-3xl shadow-xl border border-primary-500/30 p-8 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-primary-600/5 rounded-full blur-3xl pointer-events-none"></div>
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-8 flex items-center gap-2">
            <i data-lucide="user-plus" class="w-5 h-5 text-primary-500"></i> إضافة مستخدم جديد
        </h3>
        <form method="POST" action="/users" class="grid grid-cols-1 md:grid-cols-2 gap-8 relative z-10">
            @csrf
            <div class="group">
                <label class="block text-sm font-bold text-gray-600 dark:text-gray-400 mb-2 group-focus-within:text-primary-400 transition-colors">الاسم <span class="text-primary-500">*</span></label>
                <input type="text" name="name" required class="w-full bg-transparent border-0 border-b-2 border-gray-300 dark:border-gray-700 px-0 py-2 text-gray-900 dark:text-white text-base focus:ring-0 focus:border-primary-500 transition-colors placeholder-gray-600 outline-none">
            </div>
            <div class="group">
                <label class="block text-sm font-bold text-gray-600 dark:text-gray-400 mb-2 group-focus-within:text-primary-400 transition-colors">البريد الإلكتروني <span class="text-primary-500">*</span></label>
                <input type="email" name="email" required class="w-full bg-transparent border-0 border-b-2 border-gray-300 dark:border-gray-700 px-0 py-2 text-gray-900 dark:text-white text-base focus:ring-0 focus:border-primary-500 transition-colors placeholder-gray-600 outline-none" dir="ltr" text-right>
            </div>
            <div class="group">
                <label class="block text-sm font-bold text-gray-600 dark:text-gray-400 mb-2 group-focus-within:text-primary-400 transition-colors">كلمة المرور <span class="text-primary-500">*</span></label>
                <input type="password" name="password" required class="w-full bg-transparent border-0 border-b-2 border-gray-300 dark:border-gray-700 px-0 py-2 text-gray-900 dark:text-white text-base focus:ring-0 focus:border-primary-500 transition-colors placeholder-gray-600 outline-none" dir="ltr" text-right>
            </div>
            <div class="group">
                <label class="block text-sm font-bold text-gray-600 dark:text-gray-400 mb-2 group-focus-within:text-primary-400 transition-colors">رقم الهاتف</label>
                <input type="text" name="phone" class="w-full bg-transparent border-0 border-b-2 border-gray-300 dark:border-gray-700 px-0 py-2 text-gray-900 dark:text-white text-base focus:ring-0 focus:border-primary-500 transition-colors placeholder-gray-600 outline-none" dir="ltr" text-right>
            </div>
            <div class="group">
                <label class="block text-sm font-bold text-gray-600 dark:text-gray-400 mb-2 group-focus-within:text-primary-400 transition-colors">الدور <span class="text-primary-500">*</span></label>
                <div class="relative">
                    <select name="role" class="w-full bg-transparent border-0 border-b-2 border-gray-300 dark:border-gray-700 px-0 py-2 text-gray-900 dark:text-white text-base focus:ring-0 focus:border-primary-500 transition-colors appearance-none outline-none">
                        <option value="accountant" class="bg-gray-200 dark:bg-gray-900 text-gray-900 dark:text-white">محاسب</option>
                        <option value="financial_manager" class="bg-gray-200 dark:bg-gray-900 text-gray-900 dark:text-white">مدير مالي</option>
                        <option value="support" class="bg-gray-200 dark:bg-gray-900 text-gray-900 dark:text-white">دعم فني</option>
                        <option value="admin" class="bg-gray-200 dark:bg-gray-900 text-gray-900 dark:text-white">مشرف</option>
                    </select>
                    <i data-lucide="chevron-down" class="w-4 h-4 absolute left-0 top-1/2 -translate-y-1/2 text-gray-500 pointer-events-none group-focus-within:text-primary-500 transition-colors"></i>
                </div>
            </div>
            <div class="group">
                <label class="block text-sm font-bold text-gray-600 dark:text-gray-400 mb-2 group-focus-within:text-primary-400 transition-colors">القسم</label>
                <input type="text" name="department" class="w-full bg-transparent border-0 border-b-2 border-gray-300 dark:border-gray-700 px-0 py-2 text-gray-900 dark:text-white text-base focus:ring-0 focus:border-primary-500 transition-colors placeholder-gray-600 outline-none">
            </div>
            <div class="col-span-1 md:col-span-2 pt-4 flex gap-4">
                <button type="submit" class="bg-gradient-to-r from-secondary-600 to-primary-600 text-gray-900 dark:text-white font-bold px-8 py-3 rounded-xl hover:shadow-[0_0_15px_rgba(63, 158, 143,0.4)] hover:-translate-y-0.5 transition-all flex items-center gap-2 text-sm">
                    <i data-lucide="save" class="w-4 h-4"></i> حفظ
                </button>
                <button type="button" onclick="document.getElementById('createForm').classList.add('hidden')" class="bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 font-bold px-8 py-3 rounded-xl hover:bg-gray-700 hover:text-gray-900 dark:text-white transition-colors text-sm">إلغاء</button>
            </div>
        </form>
    </div>

    <!-- Users Table -->
    @php $roleLabels = ['accountant'=>'محاسب','financial_manager'=>'مدير مالي','support'=>'دعم فني','admin'=>'مشرف']; @endphp
    <div class="bg-white dark:bg-[#1a1a24] rounded-3xl border border-gray-200 dark:border-gray-800 overflow-hidden relative z-10 shadow-lg">
        <div class="overflow-x-auto">
            <table class="w-full text-right border-collapse">
                <thead>
                    <tr class="bg-gray-200 dark:bg-gray-900/80 border-b border-gray-200 dark:border-gray-800 text-gray-600 dark:text-gray-400 text-sm font-bold">
                        <th class="px-6 py-4">الاسم</th>
                        <th class="px-6 py-4">البريد</th>
                        <th class="px-6 py-4">الدور</th>
                        <th class="px-6 py-4">القسم</th>
                        <th class="px-6 py-4 text-center">الحالة</th>
                        <th class="px-6 py-4 text-center">إجراء</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800/50">
                    @forelse($users as $user)
                    <tr class="hover:bg-gray-100 dark:bg-gray-800/40 transition-colors group">
                        <td class="px-6 py-4 text-sm font-bold text-gray-800 dark:text-gray-200 flex items-center gap-3">
                            <div class="w-8 h-8 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-full flex items-center justify-center text-xs text-gray-700 dark:text-gray-300">
                                {{ mb_substr($user->name, 0, 1) }}
                            </div>
                            {{ $user->name }}
                        </td>
                        <td class="px-6 py-4 text-sm font-mono text-gray-600 dark:text-gray-400">{{ $user->email }}</td>
                        <td class="px-6 py-4">
                            <span class="text-xs font-bold bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 px-3 py-1.5 rounded-lg inline-block">
                                {{ $roleLabels[$user->role] ?? $user->role }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $user->department ?? '-' }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-xs font-bold px-3 py-1.5 rounded-lg inline-block {{ $user->is_active ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-primary-500/10 text-primary-500 border border-primary-500/20' }}">
                                {{ $user->is_active ? 'نشط' : 'موقوف' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if(Auth::user()->id !== $user->id)
                            <form method="POST" action="/users/{{ $user->id }}/toggle">
                                @csrf
                                <button type="submit" class="text-xs font-bold px-3 py-1.5 rounded-lg transition-colors border {{ $user->is_active ? 'bg-gray-100 dark:bg-gray-800 text-primary-400 border-gray-300 dark:border-gray-700 hover:bg-primary-500/10 hover:border-primary-500/30' : 'bg-gray-100 dark:bg-gray-800 text-emerald-400 border-gray-300 dark:border-gray-700 hover:bg-emerald-500/10 hover:border-emerald-500/30' }}">
                                    {{ $user->is_active ? 'إيقاف الحساب' : 'تفعيل الحساب' }}
                                </button>
                            </form>
                            @else
                            <span class="text-xs text-gray-600 font-bold">حسابك الحالي</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center">
                            <div class="w-16 h-16 bg-gray-200 dark:bg-gray-900 rounded-full flex items-center justify-center mx-auto mb-3 border border-gray-200 dark:border-gray-800">
                                <i data-lucide="users-x" class="w-8 h-8 text-gray-600"></i>
                            </div>
                            <p class="text-gray-600 dark:text-gray-400 font-medium">لا يوجد مستخدمون</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-6 border-t border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-200 dark:bg-gray-900/50">
            {{ $users->links() }}
        </div>
    </div>
</div>

<style>
    .animate-fade-in-up { animation: fadeInUp 0.5s ease-out; }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }
    select { -webkit-appearance: none; -moz-appearance: none; appearance: none; }
</style>
@endsection
