@extends('layouts.app')
@section('title', 'تذكرة #' . $ticket->ticket_number)

@section('content')
@php
    $statusColors = [
        'open'=>'bg-blue-500/10 text-blue-400 border border-blue-500/20',
        'in_progress'=>'bg-orange-500/10 text-orange-400 border border-orange-500/20',
        'waiting_customer'=>'bg-purple-500/10 text-purple-400 border border-purple-500/20',
        'resolved'=>'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20',
        'closed'=>'bg-gray-500/10 text-gray-400 border border-gray-500/20'
    ];
    $statusLabels = ['open'=>'مفتوحة','in_progress'=>'قيد المعالجة','waiting_customer'=>'بانتظار العميل','resolved'=>'محلولة','closed'=>'مغلقة'];
    $priorityColors = [
        'normal'=>'bg-gray-800 text-gray-300 border border-gray-700',
        'high'=>'bg-orange-500/10 text-orange-400 border border-orange-500/20',
        'urgent'=>'bg-red-500/10 text-red-500 border border-red-500/20 animate-pulse'
    ];
    $priorityLabels = ['normal'=>'عادية','high'=>'مرتفعة','urgent'=>'عاجلة'];
    $typeLabels = ['technical'=>'تقني','accounting'=>'محاسبي','development'=>'تطوير'];
@endphp

<div class="p-6 md:p-10 max-w-5xl mx-auto animate-fade-in-up space-y-6">

    @if(session('success'))
    <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 px-6 py-4 rounded-2xl flex items-center gap-3 shadow-lg">
        <i data-lucide="check-circle" class="w-5 h-5"></i>
        <span class="font-bold">{{ session('success') }}</span>
    </div>
    @endif

    <!-- Header & Controls -->
    <div class="bg-[#1a1a24] rounded-3xl border border-gray-800 p-8 relative overflow-hidden shadow-xl">
        <div class="absolute top-0 right-0 w-64 h-64 bg-red-600/5 rounded-full blur-3xl pointer-events-none"></div>
        
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6 relative z-10">
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-3">
                    <span class="text-sm font-mono text-gray-500 bg-gray-900 border border-gray-800 px-3 py-1 rounded-lg">#{{ $ticket->ticket_number }}</span>
                    <span class="text-xs text-gray-500 flex items-center gap-1"><i data-lucide="clock" class="w-3 h-3"></i> {{ $ticket->created_at->format('Y/m/d H:i') }}</span>
                </div>
                <h1 class="text-2xl md:text-3xl font-black text-white mb-4">{{ $ticket->title }}</h1>
                <div class="flex flex-wrap gap-2">
                    <span class="text-xs px-3 py-1.5 rounded-lg font-bold bg-gray-800 text-gray-300 border border-gray-700 flex items-center gap-1">
                        <i data-lucide="tag" class="w-3 h-3"></i> {{ $typeLabels[$ticket->type] ?? $ticket->type }}
                    </span>
                    <span class="text-xs px-3 py-1.5 rounded-lg font-bold {{ $priorityColors[$ticket->priority] ?? '' }}">
                        {{ $priorityLabels[$ticket->priority] ?? $ticket->priority }}
                    </span>
                    <span class="text-xs px-3 py-1.5 rounded-lg font-bold {{ $statusColors[$ticket->status] ?? '' }}">
                        {{ $statusLabels[$ticket->status] ?? $ticket->status }}
                    </span>
                </div>
            </div>

            @if(Auth::user()->isSupport() || Auth::user()->isAdmin())
            <div class="bg-gray-900/80 border border-gray-800 p-5 rounded-2xl w-full md:w-auto shadow-inner">
                <form method="POST" action="/tickets/{{ $ticket->id }}/update" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-2 uppercase">تغيير الحالة</label>
                            <select name="status" class="w-full bg-[#1a1a24] border border-gray-700 text-white rounded-xl px-3 py-2 text-sm focus:ring-0 focus:border-red-500 appearance-none outline-none">
                                @foreach($statusLabels as $val => $label)
                                <option value="{{ $val }}" {{ $ticket->status == $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-2 uppercase">تعيين إلى</label>
                            <select name="assigned_to" class="w-full bg-[#1a1a24] border border-gray-700 text-white rounded-xl px-3 py-2 text-sm focus:ring-0 focus:border-red-500 appearance-none outline-none">
                                <option value="">غير معيّن</option>
                                @foreach($supportTeam ?? [] as $member)
                                <option value="{{ $member->id }}" {{ $ticket->assigned_to == $member->id ? 'selected' : '' }}>{{ $member->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="w-full bg-gray-800 hover:bg-gray-700 border border-gray-700 text-white rounded-xl px-4 py-2 text-sm font-bold transition-colors">
                        حفظ التعديلات
                    </button>
                </form>
            </div>
            @endif
        </div>
    </div>

    <!-- Original Ticket Description -->
    <div class="bg-[#1a1a24] rounded-3xl border border-gray-800 p-8 shadow-xl">
        <div class="flex items-center gap-4 mb-6 pb-6 border-b border-gray-800/80">
            <div class="w-12 h-12 bg-gray-800 rounded-full flex items-center justify-center text-gray-300 font-bold border border-gray-700">
                {{ mb_substr($ticket->user->name ?? 'U', 0, 1) }}
            </div>
            <div>
                <p class="text-base font-bold text-white">{{ $ticket->user->name ?? 'مجهول' }}</p>
                <p class="text-xs text-gray-500 mt-1 font-medium">مقدم التذكرة</p>
            </div>
        </div>
        <div class="prose prose-invert max-w-none text-gray-300 leading-relaxed whitespace-pre-line text-lg">
            {{ $ticket->description }}
        </div>
    </div>

    <!-- Replies Section -->
    @if($ticket->replies && $ticket->replies->count() > 0)
    <div class="space-y-6 pt-4">
        <h3 class="text-lg font-bold text-white flex items-center gap-2 px-2">
            <i data-lucide="message-square" class="w-5 h-5 text-red-500"></i> الردود والمتابعة ({{ $ticket->replies->count() }})
        </h3>
        
        <div class="space-y-4">
            @foreach($ticket->replies as $reply)
            @php $isSupport = in_array($reply->user->role ?? '', ['support','admin']); @endphp
            <div class="bg-[#1a1a24] rounded-3xl p-6 md:p-8 border-r-4 shadow-lg transition-transform hover:-translate-y-0.5 {{ $isSupport ? 'border-r-red-500 border-t border-b border-l border-gray-800' : 'border-gray-800 border-r-gray-600' }}">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4 pb-4 border-b border-gray-800/50">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm border {{ $isSupport ? 'bg-red-500/10 text-red-500 border-red-500/20' : 'bg-gray-800 text-gray-400 border-gray-700' }}">
                            {{ mb_substr($reply->user->name ?? 'U', 0, 1) }}
                        </div>
                        <div>
                            <p class="text-sm font-bold {{ $isSupport ? 'text-red-400' : 'text-gray-200' }}">
                                {{ $reply->user->name ?? 'مجهول' }}
                                @if($isSupport) <span class="text-[10px] bg-red-500/20 text-red-400 px-2 py-0.5 rounded-md mr-2 font-black">دعم فني</span> @endif
                            </p>
                        </div>
                    </div>
                    <div class="text-xs text-gray-500 flex items-center gap-1 font-medium">
                        <i data-lucide="calendar" class="w-3 h-3"></i> {{ $reply->created_at->format('Y/m/d H:i') }}
                    </div>
                </div>
                <div class="text-gray-300 whitespace-pre-line leading-loose">
                    {{ $reply->body }}
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Reply Form -->
    @if($ticket->status !== 'closed')
    <div class="bg-gradient-to-br from-[#1a1a24] to-gray-900 rounded-3xl border border-gray-800 p-8 mt-8 shadow-2xl relative overflow-hidden">
        <div class="absolute right-0 top-0 w-1.5 h-full bg-gradient-to-b from-orange-600 to-red-600"></div>
        
        <h3 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
            <i data-lucide="corner-down-left" class="w-5 h-5 text-red-500"></i> إضافة رد
        </h3>
        
        <form method="POST" action="/tickets/{{ $ticket->id }}/reply" enctype="multipart/form-data" class="space-y-6 relative z-10">
            @csrf
            
            @if($errors->any())
            <div class="bg-red-500/10 border border-red-500/50 text-red-400 px-4 py-3 rounded-xl text-sm font-bold flex items-center gap-2">
                <i data-lucide="alert-circle" class="w-4 h-4 shrink-0"></i>
                {{ $errors->first() }}
            </div>
            @endif
            
            <div class="group">
                <textarea name="body" required rows="5" placeholder="اكتب ردك هنا (10 أحرف على الأقل)..."
                    class="w-full bg-gray-900 border border-gray-700 rounded-2xl px-5 py-4 text-white focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-all outline-none resize-y placeholder-gray-600"></textarea>
            </div>

            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                <div class="flex-1 w-full flex flex-col sm:flex-row items-center gap-4">
                    <div class="relative flex-1 w-full group">
                        <input type="file" name="attachment" accept=".jpg,.jpeg,.png,.pdf" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                        <div class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-3 flex items-center gap-3 group-hover:border-red-500/50 transition-colors">
                            <i data-lucide="paperclip" class="w-5 h-5 text-gray-500 group-hover:text-red-400 transition-colors"></i>
                            <span class="text-sm text-gray-500 font-medium">إرفاق ملف (اختياري)</span>
                        </div>
                    </div>
                    
                    @if(Auth::user()->isSupport() || Auth::user()->isAdmin())
                    <div class="flex items-center gap-3 w-full sm:w-auto bg-gray-900 border border-gray-700 rounded-xl px-4 py-2">
                        <label class="text-sm text-gray-400 font-bold whitespace-nowrap">تغيير الحالة:</label>
                        <select name="status" class="bg-transparent text-white border-0 focus:ring-0 text-sm font-bold outline-none appearance-none pr-2 pl-6 relative">
                            @foreach($statusLabels as $val => $label)
                            <option value="{{ $val }}" class="bg-gray-900" {{ $ticket->status == $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                </div>

                <button type="submit" class="w-full md:w-auto bg-gradient-to-r from-orange-600 to-red-600 text-white font-bold px-8 py-3.5 rounded-xl hover:shadow-[0_0_20px_rgba(239,68,68,0.4)] hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2">
                    <i data-lucide="send" class="w-4 h-4"></i> إرسال الرد
                </button>
            </div>
        </form>
    </div>
    @else
    <div class="bg-gray-900/50 border border-gray-800 rounded-3xl p-8 text-center mt-8">
        <div class="w-16 h-16 bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-4">
            <i data-lucide="lock" class="w-8 h-8 text-gray-500"></i>
        </div>
        <h3 class="text-xl font-bold text-gray-300">هذه التذكرة مغلقة</h3>
        <p class="text-gray-500 mt-2 font-medium">لا يمكن إضافة ردود جديدة على تذكرة مغلقة. إذا كانت المشكلة مستمرة، يرجى فتح تذكرة جديدة.</p>
    </div>
    @endif
</div>

<style>
    .animate-fade-in-up { animation: fadeInUp 0.5s ease-out; }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }
    select { -webkit-appearance: none; -moz-appearance: none; appearance: none; }
</style>
@endsection
