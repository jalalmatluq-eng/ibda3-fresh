<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    // GET /api/reports/dashboard
    public function dashboard(Request $request)
    {
        if (! $request->user()->isSupport()) {
            return response()->json(['message' => 'غير مصرح.'], 403);
        }

        $total        = Ticket::count();
        $open         = Ticket::where('status', 'open')->count();
        $inProgress   = Ticket::where('status', 'in_progress')->count();
        $resolved     = Ticket::whereIn('status', ['resolved', 'closed'])->count();
        $resolveRate  = $total > 0 ? round(($resolved / $total) * 100, 1) : 0;

        // متوسط وقت الاستجابة (بالساعات) — الفرق بين إنشاء التذكرة وأول رد
        $avgResponse = DB::table('ticket_replies as r')
            ->join('tickets as t', 't.id', '=', 'r.ticket_id')
            ->select(DB::raw('AVG(TIMESTAMPDIFF(HOUR, t.created_at, r.created_at)) as avg_hours'))
            ->whereRaw('r.id = (SELECT MIN(id) FROM ticket_replies WHERE ticket_id = t.id)')
            ->value('avg_hours');

        // تذاكر حسب النوع
        $byType = Ticket::select('type', DB::raw('count(*) as count'))
            ->groupBy('type')->get();

        // تذاكر حسب الأولوية
        $byPriority = Ticket::select('priority', DB::raw('count(*) as count'))
            ->groupBy('priority')->get();

        // آخر 7 أيام
        $last7Days = Ticket::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as count')
            )
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // أداء فريق الدعم
        $teamPerf = User::whereIn('role', ['support', 'admin'])
            ->withCount(['assignedTickets as total_assigned',
                'assignedTickets as resolved_count' => fn($q) =>
                    $q->whereIn('status', ['resolved', 'closed'])
            ])
            ->get(['id', 'name']);

        return response()->json(compact(
            'total', 'open', 'inProgress', 'resolved',
            'resolveRate', 'avgResponse',
            'byType', 'byPriority', 'last7Days', 'teamPerf'
        ));
    }

    // GET /api/reports/monthly?month=2024-01
    public function monthly(Request $request)
    {
        if (! $request->user()->isSupport()) {
            return response()->json(['message' => 'غير مصرح.'], 403);
        }

        $month = $request->month ?? now()->format('Y-m');
        [$year, $mon] = explode('-', $month);

        $tickets = Ticket::whereYear('created_at', $year)
            ->whereMonth('created_at', $mon)
            ->with(['user:id,name', 'assignee:id,name'])
            ->get();

        return response()->json([
            'month'      => $month,
            'total'      => $tickets->count(),
            'open'       => $tickets->where('status', 'open')->count(),
            'resolved'   => $tickets->whereIn('status', ['resolved', 'closed'])->count(),
            'byPriority' => $tickets->groupBy('priority')->map->count(),
            'byType'     => $tickets->groupBy('type')->map->count(),
            'tickets'    => $tickets,
        ]);
    }
}
