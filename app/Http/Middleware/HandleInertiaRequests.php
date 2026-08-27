<?php

namespace App\Http\Middleware;

use App\Services\BusinessSettingService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    public function __construct(private readonly BusinessSettingService $businessSettings) {}

    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $settings = $this->businessSettings->all();

        return [
            ...parent::share($request),
            'name' => $settings['site_name'] !== '' ? $settings['site_name'] : config('app.name'),
            'logoUrl' => $settings['logo'] !== 'def.png'
                ? Storage::disk($settings['logo_storage_type'])->url($settings['logo'])
                : null,
            'auth' => [
                'user' => $request->user(),
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
            'locale' => app()->getLocale(),
            'notifications' => $request->user()
                ? fn () => app(NotificationService::class)->summary($request->user())
                : null,
        ];
    }
}
