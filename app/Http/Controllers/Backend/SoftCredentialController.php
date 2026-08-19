<?php

namespace App\Http\Controllers\Backend;

use App\Actions\SoftCredentials\CreateSoftCredentialAction;
use App\Actions\SoftCredentials\DeleteSoftCredentialAction;
use App\Actions\SoftCredentials\UpdateSoftCredentialAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\StoreSoftCredentialRequest;
use App\Http\Requests\Backend\UpdateSoftCredentialRequest;
use App\Models\SoftCredential;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class SoftCredentialController extends Controller
{
    /**
     * Display a listing of the credentials.
     *
     * The raw secret is never sent to the client — the model hides `value`
     * and appends `is_configured` instead.
     */
    public function index(): Response
    {
        // Select `value` so the `is_configured` accessor can decrypt and
        // check it — the model's $hidden strips it from serialization
        // before it ever reaches Inertia::render().
        return Inertia::render('credentials/Index', [
            'credentials' => SoftCredential::query()->latest()->get(),
        ]);
    }

    /**
     * Store a newly created credential.
     */
    public function store(StoreSoftCredentialRequest $request, CreateSoftCredentialAction $action): RedirectResponse
    {
        try {
            $action->handle($request->validated());
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not save the credential. Please try again.')]);

            return back()->withInput(['key' => $request->input('key')]);
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Credential created.')]);

        return to_route('credentials.index');
    }

    /**
     * Update the specified credential.
     */
    public function update(UpdateSoftCredentialRequest $request, SoftCredential $credential, UpdateSoftCredentialAction $action): RedirectResponse
    {
        try {
            $action->handle($credential, $request->validated());
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not update the credential. Please try again.')]);

            return back()->withInput(['key' => $request->input('key')]);
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Credential updated.')]);

        return to_route('credentials.index');
    }

    /**
     * Remove the specified credential.
     */
    public function destroy(SoftCredential $credential, DeleteSoftCredentialAction $action): RedirectResponse
    {
        try {
            $action->handle($credential);
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not delete the credential. Please try again.')]);

            return back();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Credential deleted.')]);

        return to_route('credentials.index');
    }
}
