<?php
// ============================================================
// web.php への追記内容
// ============================================================

// ▼ use文に追加（ファイル上部）
// use App\Http\Controllers\ContactController;

// ▼ 以下のルートを追加（ゲスト・認証済み両方アクセス可の位置に配置）
// PIN再設定の Route::middleware('throttle:5,1') ブロックの後あたりに追加推奨

// 利用規約
Route::get('/terms', function () {
    return view('terms');
})->name('terms');

// プライバシーポリシー（TODO: privacy.blade.php 作成後に有効化）
// Route::get('/privacy', function () {
//     return view('privacy');
// })->name('privacy');

// お問い合わせ
Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/contact', [ContactController::class, 'send'])->middleware('throttle:5,1');
