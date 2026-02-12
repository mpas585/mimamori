<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\DeviceLoginController;
use App\Http\Controllers\Auth\PinResetController;
use App\Http\Controllers\MypageController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\Admin\AdminLoginController;
use App\Http\Controllers\Admin\MasterController;
use App\Http\Middleware\AdminAuth;

// ゲスト用（未ログイン）
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

    // 検知ログ
    Route::get('/logs', [LogController::class, 'index'])->name('logs');
});

// ============================================================
// 管理者画面
// ============================================================

// 管理者ログイン（未認証）
Route::get('/admin/login', [AdminLoginController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminLoginController::class, 'login']);

// 管理者認証済み
Route::middleware(AdminAuth::class)->prefix('admin')->group(function () {
    Route::get('/', [MasterController::class, 'index'])->name('admin.dashboard');
    Route::post('/issue', [MasterController::class, 'issueDevice'])->name('admin.issue');
    Route::post('/issue-bulk', [MasterController::class, 'issueBulk'])->name('admin.issue-bulk');
    Route::post('/logout', [AdminLoginController::class, 'logout'])->name('admin.logout');
});
