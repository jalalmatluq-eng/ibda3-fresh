@extends('layouts.app')
@section('title', 'التقارير والإحصائيات')

@section('content')
<div class="p-6 md:p-10 max-w-7xl mx-auto space-y-8 animate-fade-in-up">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between bg-gradient-to-br from-[#1e1e2d] to-[#151521] border border-gray-200 dark:border-gray-800 rounded-3xl p-8 shadow-2xl relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-r from-primary-600/5 to-secondary-600/5 mix-blend-overlay pointer-events-none"></div>
        <div class="relative z-10">
            <h2 class="text-3xl font-black text-gray-900 dark:text-white flex items-center gap-3">
                <i data-lucide="bar-chart-2" class="w-8 h-8 text-primary-500"></i> التقارير والإحصائيات
            </h2>
            <p class="text-gray-600 dark:text-gray-400 mt-2 text-lg font-medium">نظرة شاملة على أداء النظام وفريق الدعم الفني</p>
        </div>
        <div class="mt-4 md:mt-0 relative z-10">
            <button class="bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 px-6 py-3 rounded-xl font-bold hover:bg-gray-700 hover:text-gray-900 dark:text-white transition-colors flex items-center gap-2">
                <i data-lucide="download" class="w-5 h-5"></i> تصدير التقرير
            </button>
        </div>
    </div>

    <!-- KPIs -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
        @foreach([
            ['إجمالي التذاكر', $total, 'from-blue-600/20 to-blue-500/5', 'text-blue-400', 'border-blue-500/30', 'shadow-[0_0_15px_rgba(59,130,246,0.15)]'],
            ['تذاكر مفتوحة', $open, 'from-secondary-600/20 to-secondary-500/5', 'text-secondary-400', 'border-orange-500/30', 'shadow-[0_0_15px_rgba(249,115,22,0.15)]'],
            ['تذاكر محلولة', $resolved, 'from-emerald-600/20 to-emerald-500/5', 'text-emerald-400', 'border-emerald-500/30', 'shadow-[0_0_15px_rgba(16,185,129,0.15)]'],
            ['معدل الحل', $resolveRate . '%', 'from-primary-600/20 to-primary-500/5', 'text-primary-400', 'border-primary-500/30', 'shadow-[0_0_15px_rgba(63, 158, 143,0.15)]'],
        ] as [$label, $value, $bgGradient, $textColor, $borderColor, $shadow])
        <div class="bg-gradient-to-br {{ $bgGradient }} rounded-3xl border {{ $borderColor }} p-6 {{ $shadow }} relative overflow-hidden group hover:-translate-y-1 transition-transform">
            <div class="absolute -right-6 -top-6 w-24 h-24 bg-white/5 rounded-full blur-2xl group-hover:bg-white/10 transition-colors"></div>
            <div class="relative z-10">
                <p class="text-4xl font-black {{ $textColor }}">{{ $value }}</p>
                <p class="text-sm font-bold text-gray-600 dark:text-gray-400 mt-2 tracking-wide uppercase">{{ $label }}</p>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Charts Row -->
    <div class="grid md:grid-cols-2 gap-8">
        <!-- By Type -->
        <div class="bg-white dark:bg-[#1a1a24] rounded-3xl border border-gray-200 dark:border-gray-800 p-8 shadow-lg relative overflow-hidden">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                <i data-lucide="pie-chart" class="w-5 h-5 text-gray-500"></i> التذاكر حسب النوع
            </h3>
            @php $typeLabels = ['technical'=>'تقني','accounting'=>'محاسبي','development'=>'تطوير']; @endphp
            <div class="space-y-6">
                @foreach($byType as $item)
                <div>
                    <div class="flex justify-between text-sm font-bold mb-2">
                        <span class="text-gray-700 dark:text-gray-300">{{ $typeLabels[$item->type] ?? $item->type }}</span>
                        <span class="text-gray-900 dark:text-white">{{ $item->count }}</span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-900 rounded-full h-2.5 overflow-hidden border border-gray-200 dark:border-gray-800">
                        <div class="bg-gradient-to-r from-primary-600 to-secondary-500 h-full rounded-full relative" style="width: {{ $total > 0 ? round($item->count/$total*100) : 0 }}%">
                            <div class="absolute inset-0 bg-white/20 w-full h-full animate-[shimmer_2s_infinite]"></div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- By Priority -->
        <div class="bg-white dark:bg-[#1a1a24] rounded-3xl border border-gray-200 dark:border-gray-800 p-8 shadow-lg relative overflow-hidden">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                <i data-lucide="bar-chart" class="w-5 h-5 text-gray-500"></i> التذاكر حسب الأولوية
            </h3>
            @php
                $priorityLabels = ['normal'=>'عادية','high'=>'مرتفعة','urgent'=>'عاجلة'];
                $priorityGradients = [
                    'normal'=>'from-gray-600 to-gray-500',
                    'high'=>'from-secondary-600 to-secondary-400',
                    'urgent'=>'from-primary-600 to-primary-400'
                ];
            @endphp
            <div class="space-y-6">
                @foreach($byPriority as $item)
                <div>
                    <div class="flex justify-between text-sm font-bold mb-2">
                        <span class="text-gray-700 dark:text-gray-300">{{ $priorityLabels[$item->priority] ?? $item->priority }}</span>
                        <span class="text-gray-900 dark:text-white">{{ $item->count }}</span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-900 rounded-full h-2.5 overflow-hidden border border-gray-200 dark:border-gray-800">
                        <div class="bg-gradient-to-r {{ $priorityGradients[$item->priority] ?? 'from-gray-600 to-gray-500' }} h-full rounded-full relative" style="width: {{ $total > 0 ? round($item->count/$total*100) : 0 }}%">
                             <div class="absolute inset-0 bg-white/20 w-full h-full animate-[shimmer_2s_infinite]"></div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Team Performance -->
    <div class="bg-white dark:bg-[#1a1a24] rounded-3xl border border-gray-200 dark:border-gray-800 overflow-hidden shadow-lg">
        <div class="p-8 border-b border-gray-200 dark:border-gray-800 bg-gray-200 dark:bg-gray-900/30">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <i data-lucide="award" class="w-5 h-5 text-primary-500"></i> أداء فريق الدعم
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-right border-collapse">
                <thead>
                    <tr class="bg-gray-200 dark:bg-gray-900/80 border-b border-gray-200 dark:border-gray-800 text-gray-600 dark:text-gray-400 text-sm font-bold">
                        <th class="px-8 py-4">الموظف</th>
                        <th class="px-8 py-4 text-center">المعيّنة</th>
                        <th class="px-8 py-4 text-center">المحلولة</th>
                        <th class="px-8 py-4">معدل الحل</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800/50">
                    @foreach($teamPerf as $member)
                    @php $rate = $member->total_assigned > 0 ? round($member->resolved_count/$member->total_assigned*100) : 0; @endphp
                    <tr class="hover:bg-gray-100 dark:bg-gray-800/40 transition-colors">
                        <td class="px-8 py-5 text-sm font-bold text-gray-800 dark:text-gray-200 flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 flex items-center justify-center text-xs text-gray-600 dark:text-gray-400">
                                {{ mb_substr($member->name, 0, 1) }}
                            </div>
                            {{ $member->name }}
                        </td>
                        <td class="px-8 py-5 text-center text-gray-600 dark:text-gray-400 font-bold">{{ $member->total_assigned }}</td>
                        <td class="px-8 py-5 text-center text-emerald-400 font-bold">{{ $member->resolved_count }}</td>
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-3">
                                <div class="flex-1 bg-gray-200 dark:bg-gray-900 rounded-full h-2 overflow-hidden border border-gray-200 dark:border-gray-800">
                                    <div class="bg-gradient-to-r from-emerald-600 to-teal-400 h-full rounded-full" style="width: {{ $rate }}%"></div>
                                </div>
                                <span class="text-xs font-bold text-gray-600 dark:text-gray-400 w-8">{{ $rate }}%</span>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    @if(count($teamPerf) === 0)
                    <tr>
                        <td colspan="4" class="px-8 py-12 text-center text-gray-500 font-medium">لا توجد بيانات لأداء الفريق بعد</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .animate-fade-in-up { animation: fadeInUp 0.5s ease-out; }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes shimmer { 100% { transform: translateX(-100%); } }
</style>
@endsection
