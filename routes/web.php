<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\DeviceLoginController;
use App\Http\Controllers\MypageController;

// ゲスト用（未ログイン）
Route::middleware('guest')->group(function () {
    Route::get('/login', [DeviceLoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [DeviceLoginController::class, 'login']);
});

// 認証済みユーザー
Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return redirect('/mypage');
    });
    Route::get('/mypage', [MypageController::class, 'index'])->name('mypage');
    Route::post('/logout', [DeviceLoginController::class, 'logout'])->name('logout');
});
