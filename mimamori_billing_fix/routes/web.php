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
use App\Http\Controllers\PlanController;
use App\Http\Controllers\BillingController;

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

Route::get('/terms', function () { return view('terms'); })->name('terms');

Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/contact', [ContactController::class, 'send'])->middleware('throttle:5,1');

Route::post('/webhook/payjp', [PlanController::class, 'webhook'])->name('webhook.payjp');

Route::middleware('auth')->group(function () {
    Route::get('/', function () { return redirect('/mypage'); });
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

    Route::get('/plan', [PlanController::class, 'index'])->name('plan');
    Route::post('/plan/subscribe', [PlanController::class, 'subscribe'])->name('plan.subscribe');
    Route::post('/plan/cancel', [PlanController::class, 'cancel'])->name('plan.cancel');
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

// master・operator 共通
Route::middleware(PartnerAuth::class)->prefix('partner')->group(function () {
    Route::get('/', [MasterController::class, 'index'])->name('partner.dashboard');
    Route::post('/issue', [MasterController::class, 'issueDevice'])->name('partner.issue');
    Route::post('/issue-bulk', [MasterController::class, 'issueBulk'])->name('partner.issue-bulk');
    Route::post('/logout', [PartnerLoginController::class, 'logout'])->name('partner.logout');

    Route::get('/password-change', [PartnerPasswordController::class, 'showForm'])->name('partner.password-change');
    Route::post('/password-change', [PartnerPasswordController::class, 'update'])->name('partner.password-change.update');
    Route::post('/email-change', [PartnerPasswordController::class, 'updateEmail'])->name('partner.email-change');

    Route::get('/devices/{deviceId}/detail', [MasterController::class, 'deviceDetail'])->name('partner.devices.detail');
    Route::put('/devices/{deviceId}/assignment', [MasterController::class, 'updateDeviceAssignment'])->name('partner.devices.update-assignment');
    Route::post('/devices/{deviceId}/notification', [MasterController::class, 'updateDeviceNotification'])->name('partner.devices.update-notification');
    Route::post('/devices/{deviceId}/toggle-watch', [MasterController::class, 'toggleDeviceWatch'])->name('partner.devices.toggle-watch');
    Route::post('/devices/{deviceId}/clear-alert', [MasterController::class, 'clearDeviceAlert'])->name('partner.devices.clear-alert');
    Route::post('/devices/{deviceId}/toggle-premium', [MasterController::class, 'toggleDevicePremium'])->name('partner.devices.toggle-premium');
    Route::post('/devices/{deviceId}/schedules', [MasterController::class, 'storeDeviceSchedule'])->name('partner.devices.schedules.store');
    Route::delete('/devices/{deviceId}/schedules/{scheduleId}', [MasterController::class, 'destroyDeviceSchedule'])->name('partner.devices.schedules.destroy');

    Route::post('/admin-users', [MasterController::class, 'storeAdminUser'])->name('partner.admin-users.store');
    Route::put('/admin-users/{id}', [MasterController::class, 'updateAdminUser'])->name('partner.admin-users.update');
    Route::delete('/admin-users/{id}', [MasterController::class, 'destroyAdminUser'])->name('partner.admin-users.destroy');

    Route::post('/orgs', [MasterController::class, 'storeOrg'])->name('partner.orgs.store');
    Route::put('/orgs/{id}', [MasterController::class, 'updateOrg'])->name('partner.orgs.update');
    Route::delete('/orgs/{id}', [MasterController::class, 'destroyOrg'])->name('partner.orgs.destroy');
    Route::post('/orgs/{orgId}/toggle-premium', [MasterController::class, 'toggleOrgPremium'])->name('partner.orgs.toggle-premium');
});

// ★ master 限定（課金管理）
Route::middleware(PartnerAuth::class.':master')->prefix('partner')->group(function () {
    Route::get('/billing', [BillingController::class, 'index'])->name('partner.billing.index');
    Route::post('/billing', [BillingController::class, 'store'])->name('partner.billing.store');
    Route::put('/billing/{contract}', [BillingController::class, 'update'])->name('partner.billing.update');
    Route::post('/billing/{contract}/cancel', [BillingController::class, 'cancel'])->name('partner.billing.cancel');
    Route::post('/billing/{contract}/update-card', [BillingController::class, 'updateCard'])->name('partner.billing.update-card');
    Route::post('/billing/{contract}/charge-now', [BillingController::class, 'chargeNow'])->name('partner.billing.charge-now');
});

// operator 限定
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
    Route::post('/devices/{deviceId}/toggle-premium', [OrgAdminController::class, 'toggleDevicePremium'])->name('partner.org.devices.toggle-premium');
    Route::get('/csv', [OrgAdminController::class, 'exportCsv'])->name('partner.org.csv');
    Route::get('/timers', [OrgAdminController::class, 'timerList'])->name('partner.org.timers');
    Route::post('/devices/{deviceId}/schedules', [OrgAdminController::class, 'storeSchedule'])->name('partner.org.devices.schedules.store');
    Route::delete('/devices/{deviceId}/schedules/{scheduleId}', [OrgAdminController::class, 'destroySchedule'])->name('partner.org.devices.schedules.destroy');

    Route::get('/notification', [OrgAdminController::class, 'getNotification'])->name('partner.org.notification');
    Route::post('/notification', [OrgAdminController::class, 'updateNotification'])->name('partner.org.notification.update');
});
