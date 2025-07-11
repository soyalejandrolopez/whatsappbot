<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes with Custom Controller
Route::get('login', [App\Http\Controllers\Auth\CustomLoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [App\Http\Controllers\Auth\CustomLoginController::class, 'login']);
Route::post('logout', [App\Http\Controllers\Auth\CustomLoginController::class, 'logout'])->name('logout');

// 3D Authentication Routes (Additional)
Route::get('login-3d', function() {
    return view('auth.login-3d');
})->name('login.3d');

Route::get('demo-3d', function() {
    return view('auth.demo-3d');
})->name('demo.3d');

Route::get('password-3d', function() {
    return view('auth.passwords.email-3d');
})->name('password.3d');

// Password Reset Routes
Route::get('password/reset', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [App\Http\Controllers\Auth\ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])->name('password.update');

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Ruta de prueba para admin (temporal)
Route::get('/admin-test', function() {
    return view('admin.test');
})->middleware('auth');

// Rutas administrativas
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    Route::get('/', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard.main');

    // Vista de prueba 3D
    Route::get('/test', function() {
        return view('admin.test-3d');
    })->name('test');

    Route::get('/analytics', [App\Http\Controllers\Admin\DashboardController::class, 'analytics'])->name('analytics');
    Route::post('/clear-welcome-notifications', [App\Http\Controllers\Admin\DashboardController::class, 'clearWelcomeNotifications'])->name('notifications.clear');

    // Gestión de usuarios
    Route::resource('users', App\Http\Controllers\Admin\UserController::class);
    Route::post('users/{user}/send-notification', [App\Http\Controllers\Admin\UserController::class, 'sendNotification'])->name('users.send-notification');
    Route::post('users/{user}/toggle-status', [App\Http\Controllers\Admin\UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::post('users-bulk-action', [App\Http\Controllers\Admin\UserController::class, 'bulkAction'])->name('users.bulk-action');

    // Gestión de contactos
    Route::resource('contacts', App\Http\Controllers\Admin\ContactController::class);
    Route::post('contacts/{contact}/send-message', [App\Http\Controllers\Admin\ContactController::class, 'sendMessage'])->name('contacts.send-message');
    Route::post('contacts/{contact}/sync-profile', [App\Http\Controllers\Admin\ContactController::class, 'syncProfile'])->name('contacts.sync-profile');
    Route::post('contacts/{contact}/toggle-block', [App\Http\Controllers\Admin\ContactController::class, 'toggleBlock'])->name('contacts.toggle-block');

    // Gestión de conversaciones
    Route::resource('conversations', App\Http\Controllers\Admin\ConversationController::class);
    Route::post('conversations/{conversation}/assign', [App\Http\Controllers\Admin\ConversationController::class, 'assign'])->name('conversations.assign');
    Route::post('conversations/{conversation}/close', [App\Http\Controllers\Admin\ConversationController::class, 'close'])->name('conversations.close');
    Route::post('conversations/{conversation}/reopen', [App\Http\Controllers\Admin\ConversationController::class, 'reopen'])->name('conversations.reopen');
    Route::post('conversations/{conversation}/transfer', [App\Http\Controllers\Admin\ConversationController::class, 'transfer'])->name('conversations.transfer');
    Route::post('conversations/{conversation}/message', [App\Http\Controllers\Admin\ConversationController::class, 'sendMessage'])->name('conversations.message');
    Route::post('conversations/{conversation}/mark-read', [App\Http\Controllers\Admin\ConversationController::class, 'markAsRead'])->name('conversations.mark-read');
    Route::get('conversations-updates', [App\Http\Controllers\Admin\ConversationController::class, 'getUpdates'])->name('conversations.updates');
    Route::get('conversations-export', [App\Http\Controllers\Admin\ConversationController::class, 'export'])->name('conversations.export');
    Route::post('conversations-bulk-action', [App\Http\Controllers\Admin\ConversationController::class, 'bulkAction'])->name('conversations.bulk-action');

    // Gestión de flujos del chatbot
    Route::resource('chatbot-flows', App\Http\Controllers\Admin\ChatbotFlowController::class);
    Route::post('chatbot-flows/{flow}/toggle', [App\Http\Controllers\Admin\ChatbotFlowController::class, 'toggle'])->name('chatbot-flows.toggle');
    Route::post('chatbot-flows/{chatbotFlow}/duplicate', [App\Http\Controllers\Admin\ChatbotFlowController::class, 'duplicate'])->name('chatbot-flows.duplicate');
    Route::post('chatbot-flows/{chatbotFlow}/test', [App\Http\Controllers\Admin\ChatbotFlowController::class, 'test'])->name('chatbot-flows.test');

    // Gestión de respuestas del chatbot
    Route::resource('chatbot-responses', App\Http\Controllers\Admin\ChatbotResponseController::class);
    Route::post('chatbot-responses/{response}/toggle', [App\Http\Controllers\Admin\ChatbotResponseController::class, 'toggle'])->name('chatbot-responses.toggle');
    Route::post('chatbot-responses/{chatbotResponse}/test', [App\Http\Controllers\Admin\ChatbotResponseController::class, 'test'])->name('chatbot-responses.test');
    Route::post('chatbot-responses/{chatbotResponse}/duplicate', [App\Http\Controllers\Admin\ChatbotResponseController::class, 'duplicate'])->name('chatbot-responses.duplicate');
    Route::get('chatbot-responses-by-category', [App\Http\Controllers\Admin\ChatbotResponseController::class, 'getByCategory'])->name('chatbot-responses.by-category');
    Route::post('chatbot-responses-bulk-action', [App\Http\Controllers\Admin\ChatbotResponseController::class, 'bulkAction'])->name('chatbot-responses.bulk-action');
});

// Rutas para agentes
Route::prefix('agent')->name('agent.')->middleware(['auth'])->group(function () {
    Route::get('/', [App\Http\Controllers\Agent\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/conversations', [App\Http\Controllers\Agent\ConversationController::class, 'index'])->name('conversations.index');
    Route::get('/conversations/{conversation}', [App\Http\Controllers\Agent\ConversationController::class, 'show'])->name('conversations.show');
    Route::post('/conversations/{conversation}/message', [App\Http\Controllers\Agent\ConversationController::class, 'sendMessage'])->name('conversations.message');
});
