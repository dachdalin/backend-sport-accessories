<?php

namespace App\Http\Controllers\Backend;

use App\Actions\Faqs\CreateFaqAction;
use App\Actions\Faqs\DeleteFaqAction;
use App\Actions\Faqs\UpdateFaqAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\StoreFaqRequest;
use App\Http\Requests\Backend\UpdateFaqRequest;
use App\Models\Faq;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class FaqController extends Controller
{
    /**
     * Display a listing of the FAQs.
     */
    public function index(): Response
    {
        return Inertia::render('faqs/Index', [
            'faqs' => Faq::query()->orderBy('sort_order')->latest()->get(),
        ]);
    }

    /**
     * Show the form for creating a new FAQ.
     */
    public function create(): Response
    {
        return Inertia::render('faqs/Create');
    }

    /**
     * Store a newly created FAQ.
     */
    public function store(StoreFaqRequest $request, CreateFaqAction $action): RedirectResponse
    {
        $data = $request->validated();
        $data['status'] = $request->boolean('status');

        try {
            $action->handle($data);
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not create the FAQ. Please try again.')]);

            return back()->withInput();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('FAQ created.')]);

        return to_route('faqs.index');
    }

    /**
     * Show the form for editing the specified FAQ.
     */
    public function edit(Faq $faq): Response
    {
        return Inertia::render('faqs/Edit', [
            'faq' => $faq,
        ]);
    }

    /**
     * Update the specified FAQ.
     */
    public function update(UpdateFaqRequest $request, Faq $faq, UpdateFaqAction $action): RedirectResponse
    {
        $data = $request->validated();
        $data['status'] = $request->boolean('status');

        try {
            $action->handle($faq, $data);
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not update the FAQ. Please try again.')]);

            return back()->withInput();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('FAQ updated.')]);

        return to_route('faqs.index');
    }

    /**
     * Remove the specified FAQ.
     */
    public function destroy(Faq $faq, DeleteFaqAction $action): RedirectResponse
    {
        try {
            $action->handle($faq);
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not delete the FAQ. Please try again.')]);

            return back();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('FAQ deleted.')]);

        return to_route('faqs.index');
    }
}
