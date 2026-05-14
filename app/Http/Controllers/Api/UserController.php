<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        if (! $user->isAdmin()) {
            return response()->json(['message' => 'غير مصرح.'], 403);
        }

        $query = User::query();
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('name', 'like', "%$s%")
                ->orWhere('email', 'like', "%$s%"));
        }

        return response()->json($query->orderByDesc('created_at')->paginate(20));
    }

    public function store(Request $request)
    {
        $actor = $request->user();
        if (! $actor->isAdmin()) {
            return response()->json(['message' => 'غير مصرح.'], 403);
        }

        $data = $request->validate([
            'name'       => 'required|string|min:3|max:50',
            'email'      => 'required|email|unique:users',
            'password'   => 'required|string|min:8',
            'phone'      => 'nullable|string|max:20',
            'role'       => 'required|in:accountant,financial_manager,support,admin',
            'department' => 'nullable|string|max:100',
            'specialization' => 'nullable|string|max:100',
            'is_active'  => 'sometimes|boolean',
        ]);

        $user = User::create([
            ...$data,
            'password' => Hash::make($data['password']),
            'is_active' => $data['is_active'] ?? true,
        ]);

        return response()->json($user->only(['id','name','email','role','phone','department','specialization','is_active']), 201);
    }

    public function update(Request $request, User $user)
    {
        $actor = $request->user();
        if (! $actor->isAdmin()) {
            return response()->json(['message' => 'غير مصرح.'], 403);
        }

        $data = $request->validate([
            'name'       => 'sometimes|required|string|min:3|max:50',
            'email'      => 'sometimes|required|email|unique:users,email,' . $user->id,
            'phone'      => 'sometimes|nullable|string|max:20',
            'role'       => 'sometimes|required|in:accountant,financial_manager,support,admin',
            'department' => 'sometimes|nullable|string|max:100',
            'specialization' => 'sometimes|nullable|string|max:100',
            'is_active'  => 'sometimes|boolean',
            'password'   => 'sometimes|nullable|string|min:8',
        ]);

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return response()->json($user->fresh()->only(['id','name','email','role','phone','department','specialization','is_active']));
    }

    public function resetPassword(Request $request, User $user)
    {
        $actor = $request->user();
        if (! $actor->isAdmin()) {
            return response()->json(['message' => 'غير مصرح.'], 403);
        }

        $data = $request->validate([
            'password' => 'required|string|min:8',
        ]);

        $user->update(['password' => Hash::make($data['password'])]);

        return response()->json(['message' => 'تم تحديث كلمة المرور.']);
    }

    public function supportTeam(Request $request)
    {
        $actor = $request->user();
        if (! $actor->isSupport()) {
            return response()->json(['message' => 'غير مصرح.'], 403);
        }

        $team = User::whereIn('role', ['support', 'admin'])->where('is_active', true)
            ->get(['id','name','role','department','specialization']);

        return response()->json($team);
    }
}

