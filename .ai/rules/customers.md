---
paths:
  - 'app/Http/Controllers/Api/V1/Auth/**,app/Services/GoogleAuthService.php,app/Services/TelegramAuthService.php,app/Actions/Customers/FindOrCreateSocialCustomerAction.php'
---

# Customers

## Google + Telegram social login API built (2026-08-27)
Added POST api/v1/auth/google (`id_token`) and POST api/v1/auth/telegram (Telegram Login Widget fields: id/first_name/last_name/username/photo_url/auth_date/hash) alongside the existing email/password register+login, same controller (AuthController), same `throttle:customer-auth` limiter. No new composer dependency (no Socialite) — this is a stateless mobile/API backend, not server-side OAuth redirects, so verification happens against the provider directly:
- App\Services\GoogleAuthService::verifyIdToken() calls Google's `oauth2.googleapis.com/tokeninfo` endpoint (documented lightweight method, no google-api-php-client needed), checks `aud` against `services.google.client_id` (skipped if unset), requires `email_verified=true`.
- App\Services\TelegramAuthService::verifyLoginPayload() implements Telegram's documented check-hash algorithm by hand (HMAC-SHA256 over sorted `key=value` fields, secret = SHA256(bot_token), `hash_equals` for timing-safe compare) plus a 24h `auth_date` freshness check against replay. Reuses `services.telegram.bot_token` (already added for the order-notification feature) — no separate Telegram config needed.

Both flows go through App\Actions\Customers\FindOrCreateSocialCustomerAction::handle(string $column, string $id, array $attributes) — a single generic action (not one per provider) that: finds by `google_id`/`telegram_id` if already linked, else links to an existing customer with a matching `email` (safe for Google since Google verifies email ownership; Telegram has no email so this branch never triggers for it), else creates a new customer. New nullable+unique `customers.google_id`/`customers.telegram_id` columns (migration 2026_08_27_160000_add_social_ids_to_customers_table). Telegram accounts get a synthetic `telegram-{id}@telegram.local` email since `customers.email` is required+unique and Telegram never provides a real one — don't treat that domain as a real deliverable address anywhere (notifications, exports, etc).

Gotcha: `Http::retry($times, $sleep, $when, $throw)` throws by default after exhausting retries (`$throw` defaults to `true`) — GoogleAuthService needed `throw: false` plus a `$when` callback restricted to connection errors/5xx (not blind retry-on-any-failure) to avoid retrying a definitive 400 "invalid token" response and to let `$response->failed()` be checked normally afterward, per [[http-client]]. Don't rebuild — extend AuthController/GoogleAuthService/TelegramAuthService/FindOrCreateSocialCustomerAction instead.
