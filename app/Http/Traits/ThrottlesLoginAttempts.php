<?php

namespace App\Http\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

trait ThrottlesLoginAttempts
{
    protected int $maxAttempts = 10;
    protected int $decayMinutes = 180; // 3時間

    protected function checkTooManyAttempts(Request $request): void
    {
        if (RateLimiter::tooManyAttempts($this->throttleKey($request), $this->maxAttempts)) {
            $seconds = RateLimiter::availableIn($this->throttleKey($request));
            $minutes = ceil($seconds / 60);

            throw ValidationException::withMessages([
                $this->username() => ["ログイン試行回数が上限に達しました。{$minutes}分後に再試行してください。"],
            ]);
        }
    }

    protected function incrementAttempts(Request $request): void
    {
        RateLimiter::hit($this->throttleKey($request), $this->decayMinutes * 60);
    }

    protected function clearAttempts(Request $request): void
    {
        RateLimiter::clear($this->throttleKey($request));
    }

    protected function throttleKey(Request $request): string
    {
        return mb_strtolower($request->input($this->username())) . '|' . $request->ip();
    }

    protected function username(): string
    {
        return 'email';
    }
}
