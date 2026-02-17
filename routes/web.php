<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\DeviceLoginController;
use App\Http\Controllers\Auth\PinResetController;
use App\Http\Controllers\Admin\AdminLoginController;
use App\Http\Controllers\Admin\AdminPasswordController;
use App\Http\Controllers\Admin\OrgAdminController;
use App\Http\Controllers\MypageController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\EmailSettingsController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\Admin\MasterController;
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

// 管理者認証済み（共通）
Route::middleware(AdminAuth::class)->prefix('admin')->group(function () {
    Route::get('/', [MasterController::class, 'index'])->name('admin.dashboard');
    Route::post('/issue', [MasterController::class, 'issueDevice'])->name('admin.issue');
    Route::post('/issue-bulk', [MasterController::class, 'issueBulk'])->name('admin.issue-bulk');
    Route::post('/logout', [AdminLoginController::class, 'logout'])->name('admin.logout');

    // パスワード変更
    Route::get('/password-change', [AdminPasswordController::class, 'showForm'])->name('admin.password-change');
    Route::post('/password-change', [AdminPasswordController::class, 'update'])->name('admin.password-change.update');

    // 管理者アカウント管理
    Route::post('/admin-users', [MasterController::class, 'storeAdminUser'])->name('admin.admin-users.store');
    Route::put('/admin-users/{id}', [MasterController::class, 'updateAdminUser'])->name('admin.admin-users.update');
    Route::delete('/admin-users/{id}', [MasterController::class, 'destroyAdminUser'])->name('admin.admin-users.destroy');
});

// 組織管理者（operator）専用
Route::middleware(AdminAuth::class.':operator')->prefix('admin/org')->group(function () {
    Route::get('/', [OrgAdminController::class, 'index'])->name('admin.org.dashboard');
    Route::post('/devices/add', [OrgAdminController::class, 'addDevice'])->name('admin.org.devices.add');
    Route::post('/devices/{deviceId}/remove', [OrgAdminController::class, 'removeDevice'])->name('admin.org.devices.remove');
    Route::post('/devices/{deviceId}/toggle-watch', [OrgAdminController::class, 'toggleWatch'])->name('admin.org.devices.toggle-watch');
    Route::get('/devices/{deviceId}/detail', [OrgAdminController::class, 'deviceDetail'])->name('admin.org.devices.detail');
    Route::put('/devices/{deviceId}/assignment', [OrgAdminController::class, 'updateAssignment'])->name('admin.org.devices.update-assignment');
    Route::get('/csv', [OrgAdminController::class, 'exportCsv'])->name('admin.org.csv');
});
