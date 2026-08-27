<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Throwable;

class GoogleAuthService
{
    /**
     * Verify a Google Sign-In ID token and return the signed-in profile.
     *
     * Uses Google's tokeninfo endpoint, the documented lightweight verification method for
     * low/medium-traffic backends (no google-api-php-client dependency required).
     *
     * @return array{id: string, email: string, name: string}
     */
    public function verifyIdToken(string $idToken): array
    {
        $response = Http::timeout(5)
            ->connectTimeout(3)
            ->retry(2, 100, fn (Throwable $exception) => $exception instanceof ConnectionException
                || ($exception instanceof RequestException && $exception->response->serverError()), throw: false)
            ->get('https://oauth2.googleapis.com/tokeninfo', ['id_token' => $idToken]);

        if ($response->failed()) {
            throw ValidationException::withMessages([
                'id_token' => [__('This Google sign-in token is invalid or has expired.')],
            ]);
        }

        $payload = $response->json();

        $clientId = config('services.google.client_id');

        if ($clientId && ($payload['aud'] ?? null) !== $clientId) {
            throw ValidationException::withMessages([
                'id_token' => [__('This Google sign-in token was not issued for this application.')],
            ]);
        }

        if (($payload['email_verified'] ?? 'false') !== 'true' || empty($payload['email'])) {
            throw ValidationException::withMessages([
                'id_token' => [__('This Google account does not have a verified email address.')],
            ]);
        }

        return [
            'id' => $payload['sub'],
            'email' => $payload['email'],
            'name' => $payload['name'] ?? explode('@', $payload['email'])[0],
        ];
    }
}
