@extends('layouts.app')
@section('title', 'التذاكر')

@section('content')
<div class="p-6 md:p-10 max-w-7xl mx-auto space-y-8 animate-fade-in-up">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between bg-gradient-to-br from-[#1e1e2d] to-[#151521] border border-gray-800 rounded-3xl p-8 shadow-2xl relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-r from-red-600/5 to-orange-600/5 mix-blend-overlay pointer-events-none"></div>
        <div class="relative z-10">
            <h2 class="text-3xl font-black text-white flex items-center gap-3">
                <i data-lucide="ticket" class="w-8 h-8 text-red-500"></i> التذاكر
            </h2>
            <p class="text-gray-400 mt-2 text-lg font-medium">إجمالي: <span class="text-white font-bold">{{ $tickets->total() }}</span> تذكرة</p>
        </div>
        <div class="mt-4 md:mt-0 relative z-10">
            <a href="/tickets/create" class="bg-gradient-to-r from-orange-600 to-red-600 text-white px-6 py-3 rounded-xl font-bold hover:from-orange-500 hover:to-red-500 hover:scale-105 transition-all shadow-[0_0_15px_rgba(239,68,68,0.4)] flex items-center gap-2">
                <i data-lucide="plus" class="w-5 h-5"></i> تذكرة جديدة
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 px-6 py-4 rounded-2xl flex items-center gap-3">
        <i data-lucide="check-circle" class="w-5 h-5"></i>
        <span class="font-bold">{{ session('success') }}</span>
    </div>
    @endif

    <!-- Filters -->
    <form method="GET" action="/tickets" class="bg-[#1a1a24] rounded-2xl border border-gray-800 p-6 grid grid-cols-1 md:grid-cols-4 gap-6 relative z-10">
        <div class="relative">
            <label class="block text-xs font-bold text-gray-500 mb-2 uppercase tracking-wider">بحث حر</label>
            <input type="text" id="searchInput" name="search" value="{{ request('search') }}" placeholder="رقم، عنوان، عميل..." class="w-full bg-gray-900 border border-gray-700 text-white rounded-xl px-4 py-3 focus:ring-0 focus:border-red-500 transition-colors placeholder-gray-600 outline-none">
        </div>
        <div class="relative">
            <label class="block text-xs font-bold text-gray-500 mb-2 uppercase tracking-wider">حالة التذكرة</label>
            <div class="relative">
                <select name="status" onchange="this.form.submit()" class="w-full bg-gray-900 border border-gray-700 text-white rounded-xl px-4 py-3 focus:ring-0 focus:border-red-500 transition-colors appearance-none outline-none">
                    <option value="" class="bg-gray-900 text-gray-400">كل الحالات</option>
                    <option value="open" {{ request('status')=='open'?'selected':'' }} class="bg-gray-900 text-white">مفتوحة</option>
                    <option value="in_progress" {{ request('status')=='in_progress'?'selected':'' }} class="bg-gray-900 text-white">قيد المعالجة</option>
                    <option value="waiting_customer" {{ request('status')=='waiting_customer'?'selected':'' }} class="bg-gray-900 text-white">بانتظار العميل</option>
                    <option value="resolved" {{ request('status')=='resolved'?'selected':'' }} class="bg-gray-900 text-white">محلولة</option>
                    <option value="closed" {{ request('status')=='closed'?'selected':'' }} class="bg-gray-900 text-white">مغلقة</option>
                </select>
                <i data-lucide="chevron-down" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 pointer-events-none"></i>
            </div>
        </div>
        <div class="relative">
            <label class="block text-xs font-bold text-gray-500 mb-2 uppercase tracking-wider">الأولوية</label>
            <div class="relative">
                <select name="priority" onchange="this.form.submit()" class="w-full bg-gray-900 border border-gray-700 text-white rounded-xl px-4 py-3 focus:ring-0 focus:border-red-500 transition-colors appearance-none outline-none">
                    <option value="" class="bg-gray-900 text-gray-400">كل الأولويات</option>
                    <option value="normal" {{ request('priority')=='normal'?'selected':'' }} class="bg-gray-900 text-white">عادية</option>
                    <option value="high" {{ request('priority')=='high'?'selected':'' }} class="bg-gray-900 text-white">مرتفعة</option>
                    <option value="urgent" {{ request('priority')=='urgent'?'selected':'' }} class="bg-gray-900 text-white">عاجلة</option>
                </select>
                <i data-lucide="chevron-down" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 pointer-events-none"></i>
            </div>
        </div>
        <div class="flex items-end">
            <button type="submit" class="w-full bg-gray-800 hover:bg-gray-700 border border-gray-700 text-white rounded-xl px-4 py-3 font-bold transition-colors flex items-center justify-center gap-2">
                <i data-lucide="filter" class="w-5 h-5"></i> تصفية
            </button>
        </div>
    </form>

    <!-- Table -->
    <div class="bg-[#1a1a24] rounded-3xl border border-gray-800 overflow-hidden relative z-10 shadow-lg">
        <div class="overflow-x-auto">
            <table class="w-full text-right border-collapse">
                <thead>
                    <tr class="bg-gray-900/80 border-b border-gray-800 text-gray-400 text-sm font-bold">
                        <th class="px-6 py-4">رقم</th>
                        <th class="px-6 py-4">العنوان</th>
                        <th class="px-6 py-4">العميل</th>
                        <th class="px-6 py-4">النوع</th>
                        <th class="px-6 py-4 text-center">الأولوية</th>
                        <th class="px-6 py-4 text-center">الحالة</th>
                        <th class="px-6 py-4">التاريخ</th>
                        <th class="px-6 py-4 text-center">الإجراء</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800/50">
                    @forelse($tickets as $ticket)
                    @php
                        $statusColors = [
                            'open'=>'bg-blue-500/10 text-blue-400 border border-blue-500/20',
                            'in_progress'=>'bg-orange-500/10 text-orange-400 border border-orange-500/20',
                            'waiting_customer'=>'bg-purple-500/10 text-purple-400 border border-purple-500/20',
                            'resolved'=>'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20',
                            'closed'=>'bg-gray-500/10 text-gray-400 border border-gray-500/20'
                        ];
                        $priorityColors = [
                            'normal'=>'bg-gray-800 text-gray-300 border border-gray-700',
                            'high'=>'bg-orange-500/10 text-orange-400 border border-orange-500/20',
                            'urgent'=>'bg-red-500/10 text-red-500 border border-red-500/20 animate-pulse'
                        ];
                    @endphp
                    <tr class="hover:bg-gray-800/40 transition-colors group">
                        <td class="px-6 py-4 text-sm font-mono text-gray-500">{{ $ticket->ticket_number }}</td>
                        <td class="px-6 py-4 text-sm font-bold text-gray-200 group-hover:text-white transition-colors">
                            <a href="/tickets/{{ $ticket->id }}">{{ Str::limit($ticket->title, 40) }}</a>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-400 flex items-center gap-2">
                            <div class="w-6 h-6 bg-gray-800 rounded-full flex items-center justify-center text-xs text-gray-300 border border-gray-700">
                                {{ mb_substr($ticket->user->name ?? '?', 0, 1) }}
                            </div>
                            {{ $ticket->user->name ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-400">{{ $ticket->type_label }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-xs px-3 py-1.5 rounded-lg font-bold inline-block {{ $priorityColors[$ticket->priority] ?? 'bg-gray-800' }}">
                                {{ $ticket->priority_label }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-xs px-3 py-1.5 rounded-lg font-bold inline-block {{ $statusColors[$ticket->status] ?? 'bg-gray-800' }}">
                                {{ $ticket->status_label }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $ticket->created_at->format('Y/m/d') }}</td>
                        <td class="px-6 py-4 text-center">
                            <a href="/tickets/{{ $ticket->id }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-gray-800 text-gray-400 hover:text-red-400 hover:bg-red-500/10 border border-gray-700 hover:border-red-500/30 transition-all">
                                <i data-lucide="arrow-up-left" class="w-4 h-4"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-16 text-center">
                            <div class="w-16 h-16 bg-gray-900 rounded-full flex items-center justify-center mx-auto mb-3 border border-gray-800">
                                <i data-lucide="inbox" class="w-8 h-8 text-gray-600"></i>
                            </div>
                            <p class="text-gray-400 font-medium">لا توجد تذاكر حالياً</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-6 border-t border-gray-800 bg-gray-900/50">
            {{ $tickets->links() }}
        </div>
    </div>
</div>

<style>
    .animate-fade-in-up { animation: fadeInUp 0.5s ease-out; }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }
    select { -webkit-appearance: none; -moz-appearance: none; appearance: none; }
</style>

<script>
    let searchTimeout = null;
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        // Move cursor to end of input if there's a value
        if (searchInput.value) {
            searchInput.focus();
            const val = searchInput.value;
            searchInput.value = '';
            searchInput.value = val;
        }

        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                this.form.submit();
            }, 500);
        });
    }
</script>
@endsection
