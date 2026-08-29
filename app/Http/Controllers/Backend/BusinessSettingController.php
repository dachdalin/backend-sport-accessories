<?php

namespace App\Http\Controllers\Backend;

use App\Actions\BusinessSettings\UpdateBusinessSettingsAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\UpdateBusinessSettingRequest;
use App\Services\BusinessSettingService;
use DateTimeZone;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class BusinessSettingController extends Controller
{
    public function __construct(private readonly BusinessSettingService $businessSettings) {}

    /**
     * Show the business settings page.
     */
    public function edit(): Response
    {
        return Inertia::render('business-settings/Edit', [
            'settings' => $this->businessSettings->all(),
            'timezones' => DateTimeZone::listIdentifiers(),
        ]);
    }

    /**
     * Update the business settings.
     */
    public function update(UpdateBusinessSettingRequest $request, UpdateBusinessSettingsAction $action): RedirectResponse
    {
        $data = $request->safe()->except('logo', 'working_days');
        $data['tax_included_in_price'] = $request->boolean('tax_included_in_price') ? '1' : '0';
        $data['maintenance_mode'] = $request->boolean('maintenance_mode') ? '1' : '0';
        $data['guest_checkout'] = $request->boolean('guest_checkout') ? '1' : '0';
        $data['working_days'] = implode(',', $request->safe()->input('working_days', []));

        try {
            $action->handle($data, $request->file('logo'));
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not update the business settings. Please try again.')]);

            return back()->withInput();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Business settings updated.')]);

        return to_route('business-settings.edit');
    }
}
