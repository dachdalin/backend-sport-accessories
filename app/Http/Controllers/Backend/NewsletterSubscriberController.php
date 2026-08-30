<?php

namespace App\Http\Controllers\Backend;

use App\Actions\NewsletterSubscribers\CreateNewsletterSubscriberAction;
use App\Actions\NewsletterSubscribers\DeleteNewsletterSubscriberAction;
use App\Actions\NewsletterSubscribers\SendNewsletterAction;
use App\Actions\NewsletterSubscribers\UpdateNewsletterSubscriberAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\SendNewsletterRequest;
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
            'subscribedCount' => NewsletterSubscriber::query()->where('status', true)->count(),
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

    /**
     * Send a newsletter email to a single subscriber.
     */
    public function send(SendNewsletterRequest $request, NewsletterSubscriber $newsletterSubscriber, SendNewsletterAction $action): RedirectResponse
    {
        if (! $newsletterSubscriber->status) {
            Inertia::flash('toast', ['type' => 'error', 'message' => __('This subscriber has unsubscribed and cannot be emailed.')]);

            return back();
        }

        try {
            $action->handle(collect([$newsletterSubscriber]), $request->validated());
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not send the email. Please try again.')]);

            return back()->withInput();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Email queued to send to :email.', ['email' => $newsletterSubscriber->email])]);

        return back();
    }

    /**
     * Send a newsletter email to every subscribed subscriber.
     */
    public function sendAll(SendNewsletterRequest $request, SendNewsletterAction $action): RedirectResponse
    {
        $subscribers = NewsletterSubscriber::query()->where('status', true)->get();

        if ($subscribers->isEmpty()) {
            Inertia::flash('toast', ['type' => 'error', 'message' => __('There are no subscribed recipients to send to.')]);

            return back();
        }

        try {
            $count = $action->handle($subscribers, $request->validated());
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not send the newsletter. Please try again.')]);

            return back()->withInput();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Email queued to send to :count subscribers.', ['count' => $count])]);

        return back();
    }
}
