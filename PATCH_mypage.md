### mypage.blade.php フッターリンク修正

ファイル: resources/views/mypage.blade.php

#### 変更箇所（フッター部分）

【変更前】
```html
<a href="#">使い方</a><span class="footer-sep">|</span>
<a href="#">利用規約</a><span class="footer-sep">|</span>
<a href="#">お問い合わせ</a>
```

【変更後】
```html
<a href="/guide">使い方</a><span class="footer-sep">|</span>
<a href="/terms">利用規約</a><span class="footer-sep">|</span>
<a href="/contact">お問い合わせ</a>
```
