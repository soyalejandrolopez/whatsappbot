<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// WhatsApp Webhook Routes
Route::prefix('whatsapp')->middleware(['whatsapp.rate_limit', 'whatsapp.validate'])->group(function () {
    Route::get('/webhook', [App\Http\Controllers\Api\WhatsAppWebhookController::class, 'verify']);
    Route::post('/webhook', [App\Http\Controllers\Api\WhatsAppWebhookController::class, 'webhook']);
});

// API Routes for Admin Panel
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    // Conversations API
    Route::apiResource('conversations', App\Http\Controllers\Api\ConversationController::class);
    Route::post('conversations/{conversation}/send-message', [App\Http\Controllers\Api\ConversationController::class, 'sendMessage']);
    Route::post('conversations/{conversation}/assign', [App\Http\Controllers\Api\ConversationController::class, 'assign']);
    Route::post('conversations/{conversation}/close', [App\Http\Controllers\Api\ConversationController::class, 'close']);
    Route::get('conversations/{conversation}/messages', [App\Http\Controllers\Api\ConversationController::class, 'messages']);

    // Analytics API
    Route::prefix('analytics')->group(function () {
        Route::get('dashboard', [App\Http\Controllers\Api\AnalyticsController::class, 'dashboard']);
        Route::get('conversations', [App\Http\Controllers\Api\AnalyticsController::class, 'conversations']);
        Route::get('messages', [App\Http\Controllers\Api\AnalyticsController::class, 'messages']);
        Route::get('hourly-activity', [App\Http\Controllers\Api\AnalyticsController::class, 'hourlyActivity']);
        Route::get('agent-performance', [App\Http\Controllers\Api\AnalyticsController::class, 'agentPerformance']);
        Route::get('satisfaction', [App\Http\Controllers\Api\AnalyticsController::class, 'satisfaction']);
        Route::get('response-time', [App\Http\Controllers\Api\AnalyticsController::class, 'responseTime']);
    });
});
