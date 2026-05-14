<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\KnowledgeController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\TicketController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

// ==============================
// مسارات عامة (بدون مصادقة)
// ==============================
Route::post('/login', [AuthController::class, 'login']);

// ==============================
// مسارات محمية بـ Sanctum
// ==============================
Route::middleware('auth:sanctum')->group(function () {

    // المصادقة
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me',      [AuthController::class, 'me']);

    // التذاكر
    Route::get   ('/tickets',                [TicketController::class, 'index']);
    Route::post  ('/tickets',                [TicketController::class, 'store']);
    Route::get   ('/tickets/{ticket}',       [TicketController::class, 'show']);
    Route::put   ('/tickets/{ticket}',       [TicketController::class, 'update']);
    Route::post  ('/tickets/{ticket}/replies', [TicketController::class, 'reply']);

    // قاعدة المعرفة
    Route::get   ('/kb/categories',           [KnowledgeController::class, 'categories']);
    Route::get   ('/kb/articles',             [KnowledgeController::class, 'index']);
    Route::post  ('/kb/articles',             [KnowledgeController::class, 'store']);
    Route::get   ('/kb/articles/{article}',   [KnowledgeController::class, 'show']);
    Route::put   ('/kb/articles/{article}',   [KnowledgeController::class, 'update']);
    Route::delete('/kb/articles/{article}',   [KnowledgeController::class, 'destroy']);
    Route::post  ('/kb/articles/{article}/rate', [KnowledgeController::class, 'rate']);

    // المستخدمون
    Route::get   ('/users',                    [UserController::class, 'index']);
    Route::post  ('/users',                    [UserController::class, 'store']);
    Route::put   ('/users/{user}',             [UserController::class, 'update']);
    Route::post  ('/users/{user}/reset-password', [UserController::class, 'resetPassword']);
    Route::get   ('/users/support-team',       [UserController::class, 'supportTeam']);

    // التقارير
    Route::get('/reports/dashboard', [ReportController::class, 'dashboard']);
    Route::get('/reports/monthly',   [ReportController::class, 'monthly']);
});
