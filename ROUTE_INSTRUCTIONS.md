# email-settings ルート追加手順

## routes/web.php に追加する内容

認証済みユーザーのグループ内（`Route::middleware('auth')->group(function () {` の中）に、
以下を追加してください：

```php
    // メールアドレス設定
    Route::get('/email-settings', [EmailSettingsController::class, 'index'])->name('email-settings');
    Route::post('/email-settings/send', [EmailSettingsController::class, 'sendVerification'])->name('email-settings.send');
    Route::get('/email-settings/sent', [EmailSettingsController::class, 'sent'])->name('email-settings.sent');
    Route::post('/email-settings/delete', [EmailSettingsController::class, 'delete'])->name('email-settings.delete');
```

## ファイル先頭の use 文に追加

```php
use App\Http\Controllers\EmailSettingsController;
```

## 認証不要ルートとして追加（メール内リンク用）

認証グループの **外** に以下を追加：

```php
// メール認証リンク（ログイン不要でアクセス可能にする）
Route::get('/email-settings/verify/{token}', [EmailSettingsController::class, 'verify'])->name('email-settings.verify');
```

※ メールリンクはログインしていない状態でもクリックできるように認証グループ外に配置
