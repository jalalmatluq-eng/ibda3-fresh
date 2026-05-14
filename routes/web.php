<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Web\TicketWebController;
use App\Http\Controllers\Web\KnowledgeWebController;
use App\Http\Controllers\Web\UserWebController;
use App\Http\Controllers\Web\ReportWebController;

// تسجيل الدخول
Route::get('/login', function () {
    if (Auth::check()) return redirect('/');
    return view('auth.login');
})->name('login');

Route::post('/login', function () {
    $credentials = request()->validate([
        'email'    => 'required|email',
        'password' => 'required',
    ]);
    if (Auth::attempt($credentials, request()->boolean('remember'))) {
        request()->session()->regenerate();
        return redirect('/');
    }
    return back()->withErrors(['email' => 'البريد الإلكتروني أو كلمة المرور غير صحيحة.']);
})->name('login.post');

// تسجيل الخروج
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/login');
})->name('logout');

// الصفحات المحمية
Route::middleware('auth')->group(function () {

    // الرئيسية
    Route::get('/', function () {
        $stats = [
            'open'       => \App\Models\Ticket::where('status', 'open')->count(),
            'in_progress'=> \App\Models\Ticket::where('status', 'in_progress')->count(),
            'resolved'   => \App\Models\Ticket::whereIn('status', ['resolved','closed'])->count(),
            'urgent'     => \App\Models\Ticket::where('priority', 'urgent')->where('status','!=','closed')->count(),
        ];
        $recentTickets = \App\Models\Ticket::with('user:id,name')
            ->orderByDesc('created_at')->limit(5)->get();
        return view('dashboard', compact('stats', 'recentTickets'));
    });

    // التذاكر
    Route::get   ('/tickets',                [TicketWebController::class, 'index']);
    Route::get   ('/tickets/create',         [TicketWebController::class, 'create']);
    Route::post  ('/tickets',                [TicketWebController::class, 'store']);
    Route::get   ('/tickets/{id}',           [TicketWebController::class, 'show']);
    Route::post  ('/tickets/{id}/reply',     [TicketWebController::class, 'reply']);
    Route::post  ('/tickets/{id}/update',    [TicketWebController::class, 'update']);

    // قاعدة المعرفة
    Route::get   ('/knowledge',              [KnowledgeWebController::class, 'index']);
    Route::get   ('/knowledge/create',       [KnowledgeWebController::class, 'create']);
    Route::post  ('/knowledge',              [KnowledgeWebController::class, 'store']);
    Route::get   ('/knowledge/{id}',         [KnowledgeWebController::class, 'show']);
    Route::post  ('/knowledge/{id}/rate',    [KnowledgeWebController::class, 'rate']);

    // المستخدمون (مشرف فقط)
    Route::get   ('/users',                  [UserWebController::class, 'index']);
    Route::post  ('/users',                  [UserWebController::class, 'store']);
    Route::post  ('/users/{id}/toggle',      [UserWebController::class, 'toggle']);

    // التقارير
    Route::get   ('/reports',                [ReportWebController::class, 'index']);
});
