<?php

namespace App\Services;

use Illuminate\Validation\ValidationException;

class TelegramAuthService
{
    /**
     * Verify a Telegram Login Widget payload against the bot token, per Telegram's documented
     * check-hash algorithm: https://core.telegram.org/widgets/login#checking-authorization
     *
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function verifyLoginPayload(array $payload): array
    {
        $botToken = config('services.telegram.bot_token');

        if (! $botToken) {
            throw ValidationException::withMessages([
                'hash' => [__('Telegram login is not configured.')],
            ]);
        }

        $hash = $payload['hash'] ?? null;

        $checkString = collect($payload)
            ->except('hash')
            ->reject(fn ($value) => $value === null)
            ->sortKeys()
            ->map(fn ($value, $key) => "{$key}={$value}")
            ->implode("\n");

        $secretKey = hash('sha256', $botToken, true);
        $computedHash = hash_hmac('sha256', $checkString, $secretKey);

        if (! is_string($hash) || ! hash_equals($computedHash, $hash)) {
            throw ValidationException::withMessages([
                'hash' => [__('Invalid Telegram login data.')],
            ]);
        }

        if (now()->subDay()->timestamp > (int) ($payload['auth_date'] ?? 0)) {
            throw ValidationException::withMessages([
                'auth_date' => [__('This Telegram login has expired. Please sign in again.')],
            ]);
        }

        return $payload;
    }
}
