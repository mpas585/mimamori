<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\DeviceLoginController;
use App\Http\Controllers\Auth\PinResetController;
use App\Http\Controllers\Partner\PartnerLoginController;
use App\Http\Controllers\Partner\PartnerPasswordController;
use App\Http\Controllers\Partner\PartnerPasswordResetController;
use App\Http\Controllers\MypageController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\EmailSettingsController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\Partner\MasterController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\Partner\OrgAdminController;
use App\Http\Middleware\PartnerAuth;

// ============================================================
// ユーザー画面
// ============================================================

Route::middleware('guest')->group(function () {
    Route::get('/login', [DeviceLoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [DeviceLoginController::class, 'login']);
});

Route::middleware('throttle:5,1')->group(function () {
    Route::get('/pin-reset', [PinResetController::class, 'showForm'])->name('pin-reset');
    Route::post('/pin-reset', [PinResetController::class, 'verifyDevice']);
    Route::post('/pin-reset/send-email', [PinResetController::class, 'sendResetEmail']);
    Route::get('/pin-reset/initial', [PinResetController::class, 'showInitialPinForm']);
    Route::post('/pin-reset/initial', [PinResetController::class, 'resetWithInitialPin']);
    Route::get('/pin-reset/token/{token}', [PinResetController::class, 'showNewPinForm']);
    Route::post('/pin-reset/token/{token}', [PinResetController::class, 'resetWithToken']);
});

Route::get('/email-settings/verify/{token}', [EmailSettingsController::class, 'verify'])->name('email-settings.verify');

Route::get('/terms', function () {
    return view('terms');
})->name('terms');

Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/contact', [ContactController::class, 'send'])->middleware('throttle:5,1');

Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return redirect('/mypage');
    });

    Route::get('/mypage', [MypageController::class, 'index'])->name('mypage');
    Route::post('/mypage/toggle-watch', [MypageController::class, 'toggleWatch']);
    Route::post('/logout', [DeviceLoginController::class, 'logout'])->name('logout');

    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::post('/settings/device', [SettingsController::class, 'updateDevice']);
    Route::post('/settings/notification', [SettingsController::class, 'updateNotification']);
    Route::post('/settings/test-notification', [SettingsController::class, 'sendTestNotification']);

    Route::get('/email-settings', [EmailSettingsController::class, 'index'])->name('email-settings');
    Route::post('/email-settings/send', [EmailSettingsController::class, 'sendVerification'])->name('email-settings.send');
    Route::get('/email-settings/sent', [EmailSettingsController::class, 'sent'])->name('email-settings.sent');
    Route::post('/email-settings/delete', [EmailSettingsController::class, 'delete'])->name('email-settings.delete');

    Route::get('/logs', [LogController::class, 'index'])->name('logs');

    Route::apiResource('schedules', ScheduleController::class)->except(['show']);
});

// ============================================================
// パートナー（管理会社）画面
// ============================================================

Route::get('/partner/login', [PartnerLoginController::class, 'showLoginForm'])->name('partner.login');
Route::post('/partner/login', [PartnerLoginController::class, 'login']);

Route::get('/partner/password-reset', [PartnerPasswordResetController::class, 'showRequestForm'])->name('partner.password-reset');
Route::post('/partner/password-reset', [PartnerPasswordResetController::class, 'sendResetLink']);
Route::get('/partner/password-reset/{token}', [PartnerPasswordResetController::class, 'showResetForm'])->name('partner.password-reset.show');
Route::post('/partner/password-reset/{token}', [PartnerPasswordResetController::class, 'reset']);

Route::middleware(PartnerAuth::class)->prefix('partner')->group(function () {
    Route::get('/', [MasterController::class, 'index'])->name('partner.dashboard');
    Route::post('/issue', [MasterController::class, 'issueDevice'])->name('partner.issue');
    Route::post('/issue-bulk', [MasterController::class, 'issueBulk'])->name('partner.issue-bulk');
    Route::post('/logout', [PartnerLoginController::class, 'logout'])->name('partner.logout');

    Route::get('/password-change', [PartnerPasswordController::class, 'showForm'])->name('partner.password-change');
    Route::post('/password-change', [PartnerPasswordController::class, 'update'])->name('partner.password-change.update');
    Route::post('/email-change', [PartnerPasswordController::class, 'updateEmail'])->name('partner.email-change');

    // デバイス詳細
    Route::get('/devices/{deviceId}/detail', [MasterController::class, 'deviceDetail'])->name('partner.devices.detail');

    // 管理者アカウント管理
    Route::post('/admin-users', [MasterController::class, 'storeAdminUser'])->name('partner.admin-users.store');
    Route::put('/admin-users/{id}', [MasterController::class, 'updateAdminUser'])->name('partner.admin-users.update');
    Route::delete('/admin-users/{id}', [MasterController::class, 'destroyAdminUser'])->name('partner.admin-users.destroy');

    // 組織管理
    Route::post('/orgs', [MasterController::class, 'storeOrg'])->name('partner.orgs.store');
    Route::put('/orgs/{id}', [MasterController::class, 'updateOrg'])->name('partner.orgs.update');
    Route::delete('/orgs/{id}', [MasterController::class, 'destroyOrg'])->name('partner.orgs.destroy');
    Route::post('/orgs/{orgId}/toggle-premium', [MasterController::class, 'toggleOrgPremium'])->name('partner.orgs.toggle-premium');
});

Route::middleware(PartnerAuth::class.':operator')->prefix('partner/org')->group(function () {
    Route::get('/', [OrgAdminController::class, 'index'])->name('partner.org.dashboard');
    Route::post('/devices/add', [OrgAdminController::class, 'addDevice'])->name('partner.org.devices.add');
    Route::post('/devices/bulk-checkout', [OrgAdminController::class, 'bulkCheckout'])->name('partner.org.devices.bulk-checkout');
    Route::post('/devices/{deviceId}/remove', [OrgAdminController::class, 'removeDevice'])->name('partner.org.devices.remove');
    Route::post('/devices/{deviceId}/toggle-watch', [OrgAdminController::class, 'toggleWatch'])->name('partner.org.devices.toggle-watch');
    Route::post('/devices/{deviceId}/clear-alert', [OrgAdminController::class, 'clearAlert'])->name('partner.org.devices.clear-alert');
    Route::get('/devices/{deviceId}/detail', [OrgAdminController::class, 'deviceDetail'])->name('partner.org.devices.detail');
    Route::put('/devices/{deviceId}/assignment', [OrgAdminController::class, 'updateAssignment'])->name('partner.org.devices.update-assignment');
    Route::post('/devices/{deviceId}/notification', [OrgAdminController::class, 'updateDeviceNotification'])->name('partner.org.devices.update-notification');
    Route::get('/csv', [OrgAdminController::class, 'exportCsv'])->name('partner.org.csv');
    Route::get('/timers', [OrgAdminController::class, 'timerList'])->name('partner.org.timers');
    Route::post('/devices/{deviceId}/schedules', [OrgAdminController::class, 'storeSchedule'])->name('partner.org.devices.schedules.store');
    Route::delete('/devices/{deviceId}/schedules/{scheduleId}', [OrgAdminController::class, 'destroySchedule'])->name('partner.org.devices.schedules.destroy');

    Route::get('/notification', [OrgAdminController::class, 'getNotification'])->name('partner.org.notification');
    Route::post('/notification', [OrgAdminController::class, 'updateNotification'])->name('partner.org.notification.update');
});


