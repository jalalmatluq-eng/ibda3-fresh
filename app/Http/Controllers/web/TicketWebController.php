<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketReply;
use App\Models\User;
use App\Models\ActivityLog;
use App\Notifications\TicketUpdatedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketWebController extends Controller
{
    public function index(Request $request)
    {
        $user  = Auth::user();
        $query = Ticket::with(['user:id,name', 'assignee:id,name']);

        if ($user->role === 'accountant') {
            $query->where('user_id', $user->id);
        } elseif ($user->role === 'financial_manager') {
            $deptUsers = User::where('department', $user->department)->pluck('id');
            $query->whereIn('user_id', $deptUsers);
        }

        if ($request->status)   $query->where('status',   $request->status);
        if ($request->priority) $query->where('priority', $request->priority);
        if ($request->type)     $query->where('type',     $request->type);
        if ($request->search) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', "%$s%")
                  ->orWhere('ticket_number', 'like', "%$s%")
                  ->orWhereHas('user', function ($uq) use ($s) {
                      $uq->where('name', 'like', "%$s%");
                  });
            });
        }

        $tickets = $query->orderByDesc('created_at')->paginate(15)->withQueryString();
        return view('tickets.index', compact('tickets'));
    }

    public function create()
    {
        return view('tickets.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'       => 'required|string|min:5|max:200',
            'description' => 'required|string|min:20',
            'type'        => 'required|in:technical,accounting,development',
            'priority'    => 'required|in:normal,high,urgent',
            'attachment'  => 'nullable|file|max:2048|mimes:jpg,jpeg,png,pdf',
        ]);

        $path = null;
        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('attachments', 'public');
        }

        $ticket = Ticket::create([
            ...$data,
            'user_id'    => Auth::id(),
            'status'     => 'open',
            'attachment' => $path,
        ]);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'فتح تذكرة',
            'description' => 'قام بإنشاء تذكرة جديدة',
            'model_type' => Ticket::class,
            'model_id' => $ticket->id,
        ]);

        return redirect("/tickets/{$ticket->id}")->with('success', 'تم فتح التذكرة بنجاح!');
    }

    public function show($id)
    {
        $user   = Auth::user();
        $ticket = Ticket::with(['user:id,name,email', 'assignee:id,name', 'replies.user:id,name,role'])->findOrFail($id);

        if ($user->role === 'accountant' && $ticket->user_id !== $user->id) {
            abort(403, 'غير مصرح.');
        }

        $supportTeam = [];
        if ($user->isSupport()) {
            $supportTeam = User::whereIn('role', ['support', 'admin'])->where('is_active', true)->get(['id', 'name']);
        }

        return view('tickets.show', compact('ticket', 'supportTeam'));
    }

    public function reply(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);
        $user   = Auth::user();

        $data = $request->validate([
            'body'       => 'required|string|min:10',
            'status'     => 'nullable|in:open,in_progress,waiting_customer,resolved,closed',
            'attachment' => 'nullable|file|max:2048|mimes:jpg,jpeg,png,pdf',
        ]);

        $path = null;
        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('attachments', 'public');
        }

        TicketReply::create([
            'ticket_id'  => $ticket->id,
            'user_id'    => $user->id,
            'body'       => $data['body'],
            'attachment' => $path,
        ]);

        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'إضافة رد',
            'description' => 'قام بإضافة رد على التذكرة',
            'model_type' => Ticket::class,
            'model_id' => $ticket->id,
        ]);

        if ($user->isSupport() && !empty($data['status'])) {
            $ticket->update(['status' => $data['status']]);
        } elseif ($user->isSupport() && $ticket->status === 'open') {
            $ticket->update(['status' => 'in_progress']);
        }

        // Send notification
        if ($user->id !== $ticket->user_id) {
            $ticket->user->notify(new TicketUpdatedNotification($ticket, "تم الرد على تذكرتك من قبل " . $user->name));
        }
        if ($ticket->assigned_to && $user->id !== $ticket->assigned_to) {
            $ticket->assignee->notify(new TicketUpdatedNotification($ticket, "تم إضافة رد من العميل على تذكرة قيد المعالجة"));
        }

        return redirect("/tickets/{$ticket->id}")->with('success', 'تم إرسال الرد!');
    }

    public function update(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);
        $data   = $request->validate([
            'status'      => 'sometimes|in:open,in_progress,waiting_customer,resolved,closed',
            'assigned_to' => 'sometimes|nullable|exists:users,id',
            'priority'    => 'sometimes|in:normal,high,urgent',
        ]);
        $ticket->update($data);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'تحديث تذكرة',
            'description' => 'قام بتحديث تفاصيل التذكرة (الحالة/الأولوية/التعيين)',
            'model_type' => Ticket::class,
            'model_id' => $ticket->id,
        ]);

        if ($ticket->user_id !== Auth::id()) {
            $ticket->user->notify(new TicketUpdatedNotification($ticket, "تم تحديث حالة تذكرتك"));
        }
        if (isset($data['assigned_to']) && $data['assigned_to'] != Auth::id()) {
            $assignedUser = User::find($data['assigned_to']);
            if ($assignedUser) {
                $assignedUser->notify(new TicketUpdatedNotification($ticket, "تم تعيين تذكرة جديدة لك"));
            }
        }
        return redirect("/tickets/{$ticket->id}")->with('success', 'تم التحديث!');
    }
}
