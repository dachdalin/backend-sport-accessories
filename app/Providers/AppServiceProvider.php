<?php

namespace App\Providers;

use App\Models\User;
use Carbon\CarbonImmutable;
use Cloudinary\Cloudinary;
use Cloudinary\Configuration\Configuration;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Inertia\ExceptionResponse;
use Inertia\Inertia;
use League\Flysystem\Filesystem;
use ThomasVantuycom\FlysystemCloudinary\CloudinaryAdapter;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
        $this->configureCloudinary();
        $this->configureRateLimiting();
        $this->configureAuthorization();
        $this->configureErrorPages();
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }

    /**
     * Register the Cloudinary filesystem driver.
     */
    protected function configureCloudinary(): void
    {
        Storage::extend('cloudinary', function ($app, $config) {
            $client = new Cloudinary(
                Configuration::instance([
                    'cloud' => [
                        'cloud_name' => $config['cloud_name'],
                        'api_key' => $config['api_key'],
                        'api_secret' => $config['api_secret'],
                    ],
                    'url' => [
                        'secure' => true,
                    ],
                ])
            );

            $adapter = new CloudinaryAdapter($client);

            return new FilesystemAdapter(
                new Filesystem($adapter, $config),
                $adapter,
                $config,
            );
        });
    }

    /**
     * Configure rate limiting for the application.
     */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for('customer-auth', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        RateLimiter::for('coupon-apply', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip());
        });

        RateLimiter::for('gift-card-check', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip());
        });

        RateLimiter::for('review-store', function (Request $request) {
            return Limit::perMinute(5)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('contact-store', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        RateLimiter::for('newsletter-subscribe', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        RateLimiter::for('order-store', function (Request $request) {
            return Limit::perMinute(5)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('password-reset-request', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        RateLimiter::for('password-reset', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip());
        });
    }

    /**
     * Configure authorization overrides.
     */
    protected function configureAuthorization(): void
    {
        Gate::before(function ($user, string $ability) {
            return $user instanceof User && $user->hasRole('admin') ? true : null;
        });
    }

    /**
     * Render branded Inertia pages for HTTP error responses.
     */
    protected function configureErrorPages(): void
    {
        Inertia::handleExceptionsUsing(function (ExceptionResponse $response) {
            $status = $response->statusCode();

            // Keep Laravel's debug page for 500/503 locally so stack traces stay visible.
            if (in_array($status, [500, 503], true) && app()->hasDebugModeEnabled()) {
                return null;
            }

            if (in_array($status, [403, 404, 419, 429, 500, 503], true)) {
                return $response->render('ErrorPage', ['status' => $status])->withSharedData();
            }
        });
    }
}
