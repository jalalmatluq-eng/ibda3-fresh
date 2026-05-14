<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketReply;
use App\Models\User;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Ticket::query()->with(['user:id,name', 'assignee:id,name', 'replies']);

        // صلاحيات مبسطة حسب منطق TicketWebController
        if ($user->role === 'accountant') {
            $query->where('user_id', $user->id);
        } elseif ($user->role === 'financial_manager') {
            $deptUsers = User::where('department', $user->department)->pluck('id');
            $query->whereIn('user_id', $deptUsers);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('title', 'like', "%$s%")
                ->orWhere('id', $s));
        }

        return response()->json($query->orderByDesc('created_at')->paginate(15));
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
            'user_id'    => $request->user()->id,
            'status'     => 'open',
            'attachment' => $path,
        ]);

        return response()->json($ticket->load(['user:id,name', 'assignee:id,name']), 201);
    }

    public function show(Ticket $ticket)
    {
        $user = request()->user();

        // منع accountant من رؤية تذاكر غير تابعة له
        if ($user->role === 'accountant' && $ticket->user_id !== $user->id) {
            abort(403);
        }

        $ticket->load(['user:id,name,email', 'assignee:id,name', 'replies.user:id,name,role']);
        return response()->json($ticket);
    }

    public function update(Request $request, Ticket $ticket)
    {
        $user = $request->user();
        if (! $user->isSupport()) {
            return response()->json(['message' => 'غير مصرح.'], 403);
        }

        $data = $request->validate([
            'status'      => 'sometimes|in:open,in_progress,waiting_customer,resolved,closed',
            'assigned_to' => 'sometimes|nullable|exists:users,id',
            'priority'    => 'sometimes|in:normal,high,urgent',
        ]);

        $ticket->update($data);

        return response()->json($ticket->fresh());
    }

    public function reply(Request $request, Ticket $ticket)
    {
        $data = $request->validate([
            'body'       => 'required|string|min:10',
            'status'     => 'nullable|in:open,in_progress,waiting_customer,resolved,closed',
            'attachment' => 'nullable|file|max:2048|mimes:jpg,jpeg,png,pdf',
        ]);

        $path = null;
        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('attachments', 'public');
        }

        $reply = TicketReply::create([
            'ticket_id'  => $ticket->id,
            'user_id'    => $request->user()->id,
            'body'       => $data['body'],
            'attachment' => $path,
        ]);

        // تحديث حالة التذكرة حسب صلاحية الدعم
        if ($request->user()->isSupport()) {
            if (isset($data['status'])) {
                $ticket->update(['status' => $data['status']]);
            } elseif ($ticket->status === 'open') {
                $ticket->update(['status' => 'in_progress']);
            }
        }

        return response()->json($reply->load('user:id,name,role'));
    }
}

