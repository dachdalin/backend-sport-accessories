<?php

namespace App\Http\Controllers\Backend;

use App\Actions\NewsletterSubscribers\CreateNewsletterSubscriberAction;
use App\Actions\NewsletterSubscribers\DeleteNewsletterSubscriberAction;
use App\Actions\NewsletterSubscribers\UpdateNewsletterSubscriberAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\StoreNewsletterSubscriberRequest;
use App\Http\Requests\Backend\UpdateNewsletterSubscriberRequest;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class NewsletterSubscriberController extends Controller
{
    /**
     * Display a listing of the newsletter subscribers.
     */
    public function index(): Response
    {
        return Inertia::render('newsletter-subscribers/Index', [
            'subscribers' => NewsletterSubscriber::query()->latest()->paginate(15)->withQueryString(),
        ]);
    }

    /**
     * Store a newly created newsletter subscriber.
     */
    public function store(StoreNewsletterSubscriberRequest $request, CreateNewsletterSubscriberAction $action): RedirectResponse
    {
        try {
            $action->handle($request->validated());
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not add the subscriber. Please try again.')]);

            return back()->withInput();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Subscriber added.')]);

        return to_route('newsletter-subscribers.index');
    }

    /**
     * Update the specified newsletter subscriber.
     */
    public function update(UpdateNewsletterSubscriberRequest $request, NewsletterSubscriber $newsletterSubscriber, UpdateNewsletterSubscriberAction $action): RedirectResponse
    {
        try {
            $action->handle($newsletterSubscriber, $request->validated());
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not update the subscriber. Please try again.')]);

            return back()->withInput();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Subscriber updated.')]);

        return to_route('newsletter-subscribers.index');
    }

    /**
     * Remove the specified newsletter subscriber.
     */
    public function destroy(NewsletterSubscriber $newsletterSubscriber, DeleteNewsletterSubscriberAction $action): RedirectResponse
    {
        try {
            $action->handle($newsletterSubscriber);
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not delete the subscriber. Please try again.')]);

            return back();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Subscriber deleted.')]);

        return to_route('newsletter-subscribers.index');
    }
}
