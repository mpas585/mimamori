<?php

namespace App\Helpers;

class PhoneHelper
{
    /**
     * 電話番号を国際形式（+81）に正規化
     * 090-1234-5678 / 09012345678 → +819012345678
     * すでに+81形式の場合はそのまま
     */
    public static function normalize(?string $phone): ?string
    {
        if (empty($phone)) {
            return null;
        }

        // 数字とプラス記号以外を除去
        $cleaned = preg_replace('/[^\d+]/', '', $phone);

        // すでに+81形式
        if (str_starts_with($cleaned, '+81')) {
            return $cleaned;
        }

        // 81から始まる（国番号付き・プラスなし）
        if (str_starts_with($cleaned, '81') && strlen($cleaned) >= 11) {
            return '+' . $cleaned;
        }

        // 0から始まる日本の番号
        if (str_starts_with($cleaned, '0')) {
            return '+81' . substr($cleaned, 1);
        }

        return $cleaned;
    }

    /**
     * 表示用に変換（+819012345678 → 09012345678）
     */
    public static function toDisplay(?string $phone): ?string
    {
        if (empty($phone)) {
            return null;
        }

        if (str_starts_with($phone, '+81')) {
            return '0' . substr($phone, 3);
        }

        return $phone;
    }
}
