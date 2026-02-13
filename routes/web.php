<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\DeviceLoginController;
use App\Http\Controllers\Auth\PinResetController;
use App\Http\Controllers\Admin\AdminLoginController;
use App\Http\Controllers\MypageController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\EmailSettingsController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\Admin\MasterController;
use App\Http\Controllers\Admin\OrgAdminController;
use App\Http\Controllers\ContactController;
use App\Http\Middleware\AdminAuth;

// ============================================================
// ユーザー画面
// ============================================================

// ゲスト（未ログイン）
Route::middleware('guest')->group(function () {
    Route::get('/login', [DeviceLoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [DeviceLoginController::class, 'login']);
});

// PIN再設定（ゲスト・認証済み両方アクセス可）
Route::middleware('throttle:5,1')->group(function () {
    Route::get('/pin-reset', [PinResetController::class, 'showForm'])->name('pin-reset');
    Route::post('/pin-reset', [PinResetController::class, 'verifyDevice']);
    Route::post('/pin-reset/send-email', [PinResetController::class, 'sendResetEmail']);
    Route::get('/pin-reset/initial', [PinResetController::class, 'showInitialPinForm']);
    Route::post('/pin-reset/initial', [PinResetController::class, 'resetWithInitialPin']);
    Route::get('/pin-reset/token/{token}', [PinResetController::class, 'showNewPinForm']);
    Route::post('/pin-reset/token/{token}', [PinResetController::class, 'resetWithToken']);
});

// メール認証リンク（ログイン不要でアクセス可能にする）
Route::get('/email-settings/verify/{token}', [EmailSettingsController::class, 'verify'])->name('email-settings.verify');

// 公開ページ（ゲスト・認証済み両方アクセス可）
Route::get('/terms', function () {
    return view('terms');
})->name('terms');

Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/contact', [ContactController::class, 'send'])->middleware('throttle:5,1');

// 認証済みユーザー
Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return redirect('/mypage');
    });

    Route::get('/mypage', [MypageController::class, 'index'])->name('mypage');
    Route::post('/mypage/toggle-watch', [MypageController::class, 'toggleWatch']);
    Route::post('/logout', [DeviceLoginController::class, 'logout'])->name('logout');

    // 設定
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::post('/settings/device', [SettingsController::class, 'updateDevice']);
    Route::post('/settings/notification', [SettingsController::class, 'updateNotification']);
    Route::post('/settings/test-notification', [SettingsController::class, 'sendTestNotification']);

    // メールアドレス設定
    Route::get('/email-settings', [EmailSettingsController::class, 'index'])->name('email-settings');
    Route::post('/email-settings/send', [EmailSettingsController::class, 'sendVerification'])->name('email-settings.send');
    Route::get('/email-settings/sent', [EmailSettingsController::class, 'sent'])->name('email-settings.sent');
    Route::post('/email-settings/delete', [EmailSettingsController::class, 'delete'])->name('email-settings.delete');

    // 検知ログ
    Route::get('/logs', [LogController::class, 'index'])->name('logs');

    // スケジュール（外出モード）
    Route::apiResource('schedules', ScheduleController::class)->except(['show']);
});

// ============================================================
// 管理者画面
// ============================================================

// 管理者ログイン（未認証）
Route::get('/admin/login', [AdminLoginController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminLoginController::class, 'login']);

// マスター管理者のみ
Route::middleware(AdminAuth::class . ':master')->prefix('admin')->group(function () {
    Route::get('/', [MasterController::class, 'index'])->name('admin.dashboard');
    Route::post('/issue', [MasterController::class, 'issueDevice'])->name('admin.issue');
    Route::post('/issue-bulk', [MasterController::class, 'issueBulk'])->name('admin.issue-bulk');
});

// 組織管理者のみ
Route::middleware(AdminAuth::class . ':operator')->prefix('admin')->group(function () {
    Route::get('/org', [OrgAdminController::class, 'index'])->name('admin.org.dashboard');
});

// 共通（どちらのroleでもOK）
Route::middleware(AdminAuth::class)->prefix('admin')->group(function () {
    Route::post('/logout', [AdminLoginController::class, 'logout'])->name('admin.logout');
});
