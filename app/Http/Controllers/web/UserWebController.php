<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserWebController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();
        if ($request->role)   $query->where('role', $request->role);
        if ($request->search) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('name', 'like', "%$s%")->orWhere('email', 'like', "%$s%"));
        }
        $users = $query->orderByDesc('created_at')->paginate(20)->withQueryString();
        return view('users.index', compact('users'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'       => 'required|string|min:3|max:50',
            'email'      => 'required|email|unique:users',
            'password'   => 'required|string|min:8',
            'phone'      => 'nullable|string|max:20',
            'role'       => 'required|in:accountant,financial_manager,support,admin',
            'department' => 'nullable|string|max:100',
        ]);
        $newUser = User::create([...$data, 'password' => Hash::make($data['password'])]);
        
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'إنشاء مستخدم',
            'description' => 'قام بإنشاء حساب للمستخدم: ' . $newUser->name,
            'model_type' => User::class,
            'model_id' => $newUser->id,
        ]);

        return redirect('/users')->with('success', 'تم إنشاء المستخدم!');
    }

    public function toggle($id)
    {
        $user = User::findOrFail($id);
        $user->update(['is_active' => !$user->is_active]);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'تعديل حالة مستخدم',
            'description' => 'قام بتغيير حالة حساب المستخدم: ' . $user->name,
            'model_type' => User::class,
            'model_id' => $user->id,
        ]);

        return back()->with('success', $user->is_active ? 'تم تفعيل الحساب' : 'تم إيقاف الحساب');
    }
}
