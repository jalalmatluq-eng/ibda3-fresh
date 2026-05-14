@extends('layouts.app')

@section('title', 'لوحة التحكم')

@section('content')
<div class="p-6 md:p-10 max-w-7xl mx-auto space-y-8 animate-fade-in-up">
    <!-- Welcome Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between bg-gradient-to-br from-[#1e1e2d] to-[#151521] border border-gray-800 rounded-3xl p-8 shadow-2xl relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-r from-orange-600/10 to-red-600/10 mix-blend-overlay pointer-events-none"></div>
        <div class="relative z-10">
            <h2 class="text-3xl font-black text-white tracking-wide">مرحباً بك، <span class="text-red-500">{{ Auth::check() ? Auth::user()->name : 'ضيف' }}</span> 👋</h2>
            <p class="text-gray-400 mt-3 text-lg font-medium">نظام إبداع ميديا جاهز. لنجعل يومنا مليئاً بالإنجاز!</p>
        </div>
        <div class="mt-6 md:mt-0 relative z-10">
            <div class="w-16 h-16 bg-red-500/10 rounded-2xl flex items-center justify-center border border-red-500/20 backdrop-blur-sm">
                <i data-lucide="activity" class="w-8 h-8 text-red-500"></i>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Open -->
        <div class="bg-[#1a1a24] rounded-2xl border border-gray-800 p-6 hover:border-gray-700 transition-all duration-300 hover:-translate-y-1 relative overflow-hidden group">
            <div class="absolute top-0 right-0 w-24 h-24 bg-blue-500/10 rounded-full blur-2xl -mr-10 -mt-10 transition-all group-hover:bg-blue-500/20"></div>
            <div class="flex items-center justify-between relative z-10">
                <div>
                    <p class="text-sm font-bold text-gray-500 uppercase tracking-wider">التذاكر المفتوحة</p>
                    <p class="text-4xl font-black text-white mt-2">{{ $stats['open'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-gray-800/50 rounded-xl border border-gray-700 flex items-center justify-center">
                    <i data-lucide="ticket" class="w-6 h-6 text-blue-400"></i>
                </div>
            </div>
        </div>

        <!-- In Progress -->
        <div class="bg-[#1a1a24] rounded-2xl border border-gray-800 p-6 hover:border-gray-700 transition-all duration-300 hover:-translate-y-1 relative overflow-hidden group">
            <div class="absolute top-0 right-0 w-24 h-24 bg-orange-500/10 rounded-full blur-2xl -mr-10 -mt-10 transition-all group-hover:bg-orange-500/20"></div>
            <div class="flex items-center justify-between relative z-10">
                <div>
                    <p class="text-sm font-bold text-gray-500 uppercase tracking-wider">قيد المعالجة</p>
                    <p class="text-4xl font-black text-white mt-2">{{ $stats['in_progress'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-gray-800/50 rounded-xl border border-gray-700 flex items-center justify-center">
                    <i data-lucide="clock" class="w-6 h-6 text-orange-400"></i>
                </div>
            </div>
        </div>

        <!-- Resolved -->
        <div class="bg-[#1a1a24] rounded-2xl border border-gray-800 p-6 hover:border-gray-700 transition-all duration-300 hover:-translate-y-1 relative overflow-hidden group">
            <div class="absolute top-0 right-0 w-24 h-24 bg-emerald-500/10 rounded-full blur-2xl -mr-10 -mt-10 transition-all group-hover:bg-emerald-500/20"></div>
            <div class="flex items-center justify-between relative z-10">
                <div>
                    <p class="text-sm font-bold text-gray-500 uppercase tracking-wider">المحلولة</p>
                    <p class="text-4xl font-black text-white mt-2">{{ $stats['resolved'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-gray-800/50 rounded-xl border border-gray-700 flex items-center justify-center">
                    <i data-lucide="check-circle" class="w-6 h-6 text-emerald-400"></i>
                </div>
            </div>
        </div>

        <!-- Urgent -->
        <div class="bg-[#1a1a24] rounded-2xl border border-red-900/30 shadow-[0_0_15px_rgba(239,68,68,0.1)] p-6 hover:shadow-[0_0_20px_rgba(239,68,68,0.2)] transition-all duration-300 hover:-translate-y-1 relative overflow-hidden group">
            <div class="absolute inset-0 bg-gradient-to-br from-red-600/5 to-transparent"></div>
            <div class="absolute top-0 right-0 w-24 h-24 bg-red-500/20 rounded-full blur-2xl -mr-10 -mt-10"></div>
            <div class="flex items-center justify-between relative z-10">
                <div>
                    <p class="text-sm font-bold text-red-500/80 uppercase tracking-wider">العاجلة</p>
                    <p class="text-4xl font-black text-red-500 mt-2">{{ $stats['urgent'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-red-500/10 rounded-xl border border-red-500/20 flex items-center justify-center animate-pulse">
                    <i data-lucide="alert-triangle" class="w-6 h-6 text-red-500"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Quick Actions -->
        <div class="lg:col-span-1 space-y-6">
            <h3 class="text-xl font-bold text-white mb-2 flex items-center gap-2">
                <i data-lucide="zap" class="w-5 h-5 text-orange-500"></i> إجراءات سريعة
            </h3>
            
            <a href="/tickets/create" class="block group relative overflow-hidden bg-[#1a1a24] rounded-2xl p-6 border border-gray-800 hover:border-red-500/50 transition-all duration-300">
                <div class="absolute right-0 top-0 w-1.5 h-full bg-gradient-to-b from-orange-500 to-red-600"></div>
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-gray-800 rounded-xl flex items-center justify-center border border-gray-700 group-hover:border-red-500/30 group-hover:bg-red-500/10 transition-colors">
                        <i data-lucide="plus" class="w-6 h-6 text-gray-400 group-hover:text-red-500 transition-colors"></i>
                    </div>
                    <div class="mr-4">
                        <h4 class="text-lg font-bold text-white">تذكرة جديدة</h4>
                        <p class="text-sm text-gray-500 mt-1">إنشاء تذكرة دعم فني جديدة</p>
                    </div>
                </div>
            </a>

            <a href="/knowledge" class="block group relative overflow-hidden bg-[#1a1a24] rounded-2xl p-6 border border-gray-800 hover:border-blue-500/50 transition-all duration-300">
                <div class="absolute right-0 top-0 w-1.5 h-full bg-gradient-to-b from-blue-400 to-indigo-600"></div>
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-gray-800 rounded-xl flex items-center justify-center border border-gray-700 group-hover:border-blue-500/30 group-hover:bg-blue-500/10 transition-colors">
                        <i data-lucide="book-open" class="w-6 h-6 text-gray-400 group-hover:text-blue-500 transition-colors"></i>
                    </div>
                    <div class="mr-4">
                        <h4 class="text-lg font-bold text-white">قاعدة المعرفة</h4>
                        <p class="text-sm text-gray-500 mt-1">استعراض حلول ومقالات المساعدة</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Recent Tickets -->
        <div class="lg:col-span-2 space-y-6">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-bold text-white flex items-center gap-2">
                    <i data-lucide="list" class="w-5 h-5 text-gray-400"></i> آخر التذاكر
                </h3>
                <a href="/tickets" class="text-sm font-bold text-red-500 hover:text-red-400 flex items-center gap-1 transition-colors">
                    عرض الكل <i data-lucide="arrow-up-left" class="w-4 h-4"></i>
                </a>
            </div>
            
            <div class="bg-[#1a1a24] rounded-3xl border border-gray-800 overflow-hidden">
                <div class="p-0">
                    @forelse($recentTickets ?? [] as $ticket)
                    <a href="/tickets/{{ $ticket->id }}" class="block px-8 py-5 border-b border-gray-800 hover:bg-gray-800/40 transition-colors group">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-3">
                                    <span class="text-xs font-mono text-gray-400 bg-gray-900 border border-gray-700 px-2 py-1 rounded-md">{{ $ticket->ticket_number }}</span>
                                    <h4 class="text-base font-bold text-gray-200 group-hover:text-white transition-colors">{{ $ticket->title }}</h4>
                                </div>
                                <div class="flex items-center mt-3 text-sm text-gray-500 gap-4">
                                    <span class="flex items-center gap-1.5"><i data-lucide="user" class="w-4 h-4 text-gray-600"></i> {{ $ticket->user->name ?? 'غير معروف' }}</span>
                                    <span class="flex items-center gap-1.5"><i data-lucide="clock" class="w-4 h-4 text-gray-600"></i> {{ $ticket->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-xs px-3 py-1.5 rounded-lg font-bold border
                                    @if($ticket->status == 'open') bg-blue-500/10 text-blue-400 border-blue-500/20
                                    @elseif($ticket->status == 'in_progress') bg-orange-500/10 text-orange-400 border-orange-500/20
                                    @elseif($ticket->status == 'resolved') bg-emerald-500/10 text-emerald-400 border-emerald-500/20
                                    @else bg-gray-500/10 text-gray-400 border-gray-500/20 @endif">
                                    {{ $ticket->status_label }}
                                </span>
                                @if($ticket->priority == 'urgent')
                                <span class="text-xs px-3 py-1.5 rounded-lg font-bold bg-red-500/10 text-red-500 border border-red-500/20 flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></span> عاجل
                                </span>
                                @endif
                            </div>
                        </div>
                    </a>
                    @empty
                    <div class="text-center py-16">
                        <div class="bg-gray-800/50 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-700">
                            <i data-lucide="inbox" class="w-8 h-8 text-gray-500"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-300">سجل التذاكر فارغ</h3>
                        <p class="text-gray-500 mt-1">لم يتم إضافة أي تذاكر في النظام بعد.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .animate-fade-in-up { animation: fadeInUp 0.5s ease-out; }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }
</style>
@endsection
