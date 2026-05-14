<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ReportWebController extends Controller
{
    public function index()
    {
        $total      = Ticket::count();
        $open       = Ticket::where('status', 'open')->count();
        $inProgress = Ticket::where('status', 'in_progress')->count();
        $resolved   = Ticket::whereIn('status', ['resolved', 'closed'])->count();
        $resolveRate = $total > 0 ? round(($resolved / $total) * 100, 1) : 0;

        $byType     = Ticket::select('type', DB::raw('count(*) as count'))->groupBy('type')->get();
        $byPriority = Ticket::select('priority', DB::raw('count(*) as count'))->groupBy('priority')->get();
        $last7Days  = Ticket::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')->orderBy('date')->get();

        $teamPerf = User::whereIn('role', ['support', 'admin'])
            ->withCount([
                'assignedTickets as total_assigned',
                'assignedTickets as resolved_count' => fn($q) => $q->whereIn('status', ['resolved', 'closed'])
            ])->get(['id', 'name']);

        return view('reports.dashboard', compact(
            'total', 'open', 'inProgress', 'resolved',
            'resolveRate', 'byType', 'byPriority', 'last7Days', 'teamPerf'
        ));
    }
}
