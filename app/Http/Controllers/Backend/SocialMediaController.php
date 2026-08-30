<?php

namespace App\Http\Controllers\Backend;

use App\Actions\SocialMedias\CreateSocialMediaAction;
use App\Actions\SocialMedias\DeleteSocialMediaAction;
use App\Actions\SocialMedias\UpdateSocialMediaAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\StoreSocialMediaRequest;
use App\Http\Requests\Backend\UpdateSocialMediaRequest;
use App\Models\SocialMedia;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class SocialMediaController extends Controller
{
    /**
     * Display a listing of the social media links.
     */
    public function index(): Response
    {
        return Inertia::render('social-medias/Index', [
            'socialMedias' => SocialMedia::query()->latest()->get(),
        ]);
    }

    /**
     * Store a newly created social media link.
     */
    public function store(StoreSocialMediaRequest $request, CreateSocialMediaAction $action): RedirectResponse
    {
        $data = $request->validated();
        $data['status'] = $request->boolean('status');

        try {
            $action->handle($data);
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not create the social media link. Please try again.')]);

            return back()->withInput();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Social media link created.')]);

        return to_route('social-medias.index');
    }

    /**
     * Update the specified social media link.
     */
    public function update(UpdateSocialMediaRequest $request, SocialMedia $socialMedia, UpdateSocialMediaAction $action): RedirectResponse
    {
        $data = $request->validated();
        $data['status'] = $request->boolean('status');

        try {
            $action->handle($socialMedia, $data);
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not update the social media link. Please try again.')]);

            return back()->withInput();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Social media link updated.')]);

        return to_route('social-medias.index');
    }

    /**
     * Remove the specified social media link.
     */
    public function destroy(SocialMedia $socialMedia, DeleteSocialMediaAction $action): RedirectResponse
    {
        try {
            $action->handle($socialMedia);
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not delete the social media link. Please try again.')]);

            return back();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Social media link deleted.')]);

        return to_route('social-medias.index');
    }
}
